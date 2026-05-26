<?php

namespace App\Http\Controllers\Work;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Work\WorkGroup;
use App\Models\Work\WorkTask;
use App\Models\Work\WorkTaskSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

class WorkTaskController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $tasks = WorkTask::with(['assignedUser', 'group.members', 'creator', 'submissions.user', 'submissions.group'])
            ->visibleTo($user)
            ->when($request->filled('status'), function ($query) use ($request) {
                if ($request->status === 'pending') {
                    $query->whereDoesntHave('submissions');
                } elseif ($request->status === 'submitted') {
                    $query->whereHas('submissions', fn ($submissions) => $submissions->where('status', 'submitted'));
                } elseif ($request->status === 'approved') {
                    $query->whereHas('submissions', fn ($submissions) => $submissions->where('status', 'approved'));
                }
            })
            ->orderBy('due_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $groups = WorkGroup::with('members')->orderBy('name')->get();
        $teachers = User::role('teacher')->where('is_active', true)->orderBy('name')->get();
        $reviewQueue = WorkTaskSubmission::with(['task', 'user', 'group', 'submittedBy'])
            ->where('status', 'submitted')
            ->latest('submitted_at')
            ->take(8)
            ->get();

        $stats = [
            'total' => WorkTask::visibleTo($user)->count(),
            'pending_review' => WorkTaskSubmission::whereHas('task', fn ($query) => $query->visibleTo($user))->where('status', 'submitted')->count(),
            'approved' => WorkTaskSubmission::whereHas('task', fn ($query) => $query->visibleTo($user))->where('status', 'approved')->count(),
            'payout' => WorkTaskSubmission::whereHas('task', fn ($query) => $query->visibleTo($user))->where('status', 'approved')->sum('payout_amount'),
        ];

        return view('work-tasks.index', compact('tasks', 'groups', 'teachers', 'reviewQueue', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:120'],
            'due_date' => ['required', 'date'],
            'max_score' => ['required', 'integer', 'min:1', 'max:1000'],
            'incentive_amount' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'assignment_type' => ['required', Rule::in(['individual', 'group'])],
            'assigned_user_id' => ['nullable', 'required_if:assignment_type,individual', 'exists:users,id'],
            'work_group_id' => ['nullable', 'required_if:assignment_type,group', 'exists:work_groups,id'],
            'group_submission_mode' => ['nullable', 'required_if:assignment_type,group', Rule::in(['individual', 'shared'])],
            'group_payment_mode' => ['nullable', 'required_if:assignment_type,group', Rule::in(['equal', 'score'])],
            'late_penalty_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        if ($data['assignment_type'] === 'individual') {
            $data['work_group_id'] = null;
            $data['group_submission_mode'] = null;
            $data['group_payment_mode'] = null;
        } else {
            $data['assigned_user_id'] = null;
        }

        $data['created_by'] = $request->user()->id;
        $data['late_penalty_percent'] = $data['late_penalty_percent'] ?? 0;

        WorkTask::create($data);

        return back()->with('success', 'Work task assigned.');
    }

    public function show(Request $request, WorkTask $task)
    {
        $task->load(['assignedUser', 'group.members', 'creator', 'submissions.user', 'submissions.group', 'submissions.submittedBy', 'submissions.reviewer']);
        $this->authorizeView($request->user(), $task);

        return view('work-tasks.show', compact('task'));
    }

    public function submit(Request $request, WorkTask $task)
    {
        $task->load('group.members');
        $this->authorizeSubmit($request->user(), $task);

        $data = $request->validate([
            'comment' => ['nullable', 'string'],
            'evidence_link' => ['nullable', 'url', 'max:255'],
            'evidence_file' => ['nullable', 'file', 'max:20480'],
        ]);

        if ($request->hasFile('evidence_file')) {
            $data['evidence_file'] = $this->storeEvidence($request);
        }

        $identity = $this->submissionIdentity($request->user(), $task);
        $submission = WorkTaskSubmission::firstOrNew($identity);

        if ($submission->exists && $submission->status === 'approved') {
            return back()->withErrors(['submission' => 'Approved submissions cannot be changed.']);
        }

        if ($submission->evidence_file && isset($data['evidence_file'])) {
            $this->deleteEvidence($submission->evidence_file);
        }

        $submission->fill([
            'submitted_by' => $request->user()->id,
            'comment' => $data['comment'] ?? null,
            'evidence_link' => $data['evidence_link'] ?? null,
            'evidence_file' => $data['evidence_file'] ?? $submission->evidence_file,
            'submitted_at' => now(),
            'status' => 'submitted',
            'score' => null,
            'review_note' => null,
            'payout_amount' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ])->save();

        return back()->with('success', 'Submission sent for review.');
    }

    public function review(Request $request, WorkTask $task, WorkTaskSubmission $submission)
    {
        abort_unless((int) $submission->work_task_id === (int) $task->id, 404);

        $data = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
            'score' => ['nullable', 'required_if:status,approved', 'integer', 'min:0', 'max:' . $task->max_score],
            'review_note' => ['nullable', 'string'],
        ]);

        $payout = null;
        if ($data['status'] === 'approved') {
            $payout = $this->calculatePayout($task, $submission, (int) $data['score']);
        }

        $submission->update([
            'status' => $data['status'],
            'score' => $data['status'] === 'approved' ? $data['score'] : null,
            'review_note' => $data['review_note'] ?? null,
            'payout_amount' => $payout,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Submission reviewed.');
    }

    private function authorizeView(User $user, WorkTask $task): void
    {
        if ($user->canAccess('work-tasks.review') || $user->canAccess('work-tasks.create')) {
            return;
        }

        abort_unless($task->isAssignedTo($user), 403);
    }

    private function authorizeSubmit(User $user, WorkTask $task): void
    {
        abort_unless($user->canAccess('work-tasks.submit') && $task->isAssignedTo($user), 403);
    }

    private function submissionIdentity(User $user, WorkTask $task): array
    {
        if ($task->isSharedGroupTask()) {
            return [
                'work_task_id' => $task->id,
                'work_group_id' => $task->work_group_id,
                'user_id' => null,
            ];
        }

        return [
            'work_task_id' => $task->id,
            'work_group_id' => $task->assignment_type === 'group' ? $task->work_group_id : null,
            'user_id' => $user->id,
        ];
    }

    private function calculatePayout(WorkTask $task, WorkTaskSubmission $submission, int $score): float
    {
        if (! $task->incentive_amount || $task->max_score < 1) {
            return 0;
        }

        $base = ((float) $score / (float) $task->max_score) * (float) $task->incentive_amount;

        if ($task->assignment_type === 'group' && $task->group_payment_mode === 'equal') {
            $members = max(1, $task->group?->members()->count() ?? 1);
            $base = $base / $members;
        }

        if ($submission->submitted_at && $submission->submitted_at->toDateString() > $task->due_date->toDateString()) {
            $base -= $base * ((float) $task->late_penalty_percent / 100);
        }

        return round(max(0, $base), 2);
    }

    private function storeEvidence(Request $request): string
    {
        $dir = public_path('uploads/work-tasks');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $file = $request->file('evidence_file');
        $name = uniqid('work_', true) . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $name);

        return 'uploads/work-tasks/' . $name;
    }

    private function deleteEvidence(?string $path): void
    {
        if ($path && str_starts_with($path, 'uploads/work-tasks/') && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}
