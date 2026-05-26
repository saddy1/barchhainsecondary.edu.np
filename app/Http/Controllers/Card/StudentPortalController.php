<?php

namespace App\Http\Controllers\Card;

use App\Http\Controllers\Controller;

use App\Models\Card\Student;
use App\Models\Card\CardRequest;
use App\Models\Card\UpdateRequest;
use App\Models\Learning\LearningCourse;
use App\Models\Learning\LearningResource;
use App\Models\Learning\LearningProgress;
use Illuminate\Http\Request;

class StudentPortalController extends Controller
{
    // ── Helpers ────────────────────────────────────────────────────────────

    private function getStudent(): Student
    {
        return Student::findOrFail(session('student_id'));
    }

    private function profileCompletionRequired(Student $student): bool
    {
        return $student->profile_completed_at === null;
    }

    // ── Student Portal ─────────────────────────────────────────────────────

    public function dashboard()
    {
        $student      = $this->getStudent();

        if ($this->profileCompletionRequired($student)) {
            return redirect()->route('student.profile.edit')
                ->with('info', 'Please review and complete your profile before continuing.');
        }

        $cardRequest  = CardRequest::where('student_id', $student->id)->latest()->first();
        $updateRequest = UpdateRequest::where('student_id', $student->id)
            ->where('status', 'pending')->latest()->first();
        $courseCount = LearningCourse::query()
            ->published()
            ->when($student->stream, fn ($query) => $query->whereHas('learningClass', fn ($classQuery) => $classQuery->where('name', $student->stream)))
            ->count();
        $resourceCount = LearningResource::query()
            ->where('is_published', true)
            ->when($student->stream, fn ($query) => $query->whereHas('learningClass', fn ($classQuery) => $classQuery->where('name', $student->stream)))
            ->count();

        return view('card.student-portal.dashboard', compact('student', 'cardRequest', 'updateRequest', 'courseCount', 'resourceCount'));
    }

    public function learning()
    {
        $student = $this->getStudent();
        $user = $student->user;

        $courses = LearningCourse::query()
            ->with(['learningClass', 'subject', 'lessons' => fn ($query) => $query->where('is_published', true)])
            ->published()
            ->when($student->stream, fn ($query) => $query->whereHas('learningClass', fn ($classQuery) => $classQuery->where('name', $student->stream)))
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        $progress = $user
            ? LearningProgress::query()
                ->where('user_id', $user->id)
                ->whereNull('learning_lesson_id')
                ->pluck('progress_percent', 'learning_course_id')
            : collect();

        $resources = LearningResource::query()
            ->with(['learningClass', 'subject'])
            ->where('is_published', true)
            ->when($student->stream, fn ($query) => $query->whereHas('learningClass', fn ($classQuery) => $classQuery->where('name', $student->stream)))
            ->latest()
            ->take(12)
            ->get();

        return view('card.student-portal.learning', compact('student', 'courses', 'progress', 'resources'));
    }

    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,jpg,png|max:200',
        ]);

        $student = $this->getStudent();
        $ext     = $request->file('photo')->getClientOriginalExtension();
        $filename = $student->roll_number . '.' . $ext;
        $request->file('photo')->move(public_path('photos'), $filename);
        $student->update(['photo' => 'photos/' . $filename]);

        return back()->with('success', 'Photo uploaded successfully. It will appear on your ID card.');
    }

    public function requestCard(Request $request)
    {
        $student = $this->getStudent();

        $existing = CardRequest::where('student_id', $student->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existing) {
            return redirect()->route('student.card-status')
                ->with('info', 'You already have a card request in progress.');
        }

        CardRequest::create([
            'student_id' => $student->id,
            'status'     => 'pending',
        ]);

        return redirect()->route('student.card-status')
            ->with('success', 'Card request submitted! Please complete payment and visit the admin office.');
    }

    public function cardStatus()
    {
        $student     = $this->getStudent();
        $cardRequest = CardRequest::where('student_id', $student->id)->latest()->first();

        return view('card.student-portal.request-card', compact('student', 'cardRequest'));
    }

    public function requestUpdateForm()
    {
        $student = $this->getStudent();
        $pending = UpdateRequest::where('student_id', $student->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        return view('card.student-portal.request-update', compact('student', 'pending'));
    }

    public function submitUpdate(Request $request)
    {
        $student = $this->getStudent();

        $pending = UpdateRequest::where('student_id', $student->id)
            ->where('status', 'pending')
            ->exists();

        if ($pending) {
            return back()->with('info', 'You already have a pending update request.');
        }

        $data = $request->validate([
            'mobile' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'parent_contact' => ['nullable', 'string', 'max:30'],
            'guardian_contact' => ['nullable', 'string', 'max:30'],
            'permanent_province' => ['nullable', 'string', 'max:100'],
            'permanent_district' => ['nullable', 'string', 'max:100'],
            'permanent_municipality' => ['nullable', 'string', 'max:150'],
            'permanent_ward' => ['nullable', 'string', 'max:20'],
            'permanent_tole' => ['nullable', 'string', 'max:150'],
            'temporary_province' => ['nullable', 'string', 'max:100'],
            'temporary_district' => ['nullable', 'string', 'max:100'],
            'temporary_municipality' => ['nullable', 'string', 'max:150'],
            'temporary_ward' => ['nullable', 'string', 'max:20'],
            'temporary_tole' => ['nullable', 'string', 'max:150'],
        ]);

        $changes = collect($data)
            ->filter(fn ($value, $field) => filled($value) && (string) $student->{$field} !== (string) $value)
            ->all();

        if (empty($changes)) {
            return back()->with('info', 'Please enter at least one new value before submitting.');
        }

        UpdateRequest::create([
            'student_id' => $student->id,
            'requested_changes' => $changes,
            'status' => 'pending',
        ]);

        return redirect()->route('student.dashboard')->with('success', 'Profile update request submitted for admin review.');
    }

    public function editProfile()
    {
        $student = $this->getStudent();

        return view('card.student-portal.profile', compact('student'));
    }

    public function updateProfile(Request $request)
    {
        $student = $this->getStudent();

        $data = $request->validate([
            'dob'            => 'required|date',
            'mobile'         => 'required|string|max:20',
            'email'          => 'nullable|email|max:150',
            'zone'           => 'nullable|string|max:100',
            'district'       => 'nullable|string|max:100',
            'municipality'   => 'nullable|string|max:100',
            'citizenship_no' => 'nullable|string|max:50',
            'program'        => 'nullable|string|max:100',
            'batch'          => 'nullable|string|max:20',
            'photo'          => 'nullable|image|mimes:jpeg,jpg,png|max:200',
        ]);

        // Handle photo upload if provided
        if ($request->hasFile('photo')) {
            $ext = $request->file('photo')->getClientOriginalExtension();
            $filename = $student->roll_number . '.' . $ext;
            $request->file('photo')->move(public_path('photos'), $filename);
            $data['photo'] = 'photos/' . $filename;
        }

        $data['profile_completed_at'] = $student->profile_completed_at ?? now();
        $student->update($data);

        return redirect()->route('student.dashboard')
            ->with('success', 'Profile updated successfully.');
    }

    // ── Admin: Card Requests ───────────────────────────────────────────────

    public function adminCardRequests()
    {
        $query = CardRequest::with('student')
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'collected', 'rejected')")
            ->orderBy('created_at', 'desc');

        // Scope to admin's organization
        if (!auth()->user()->isSuperAdmin()) {
            $query->whereHas('student', fn($q) => auth()->user()->applyStudentScope($q));
        }

        $requests = $query->paginate(25);

        return view('card.admin.card-requests', compact('requests'));
    }

    public function adminUpdateCardRequest(Request $request, CardRequest $cardRequest)
    {
        $request->validate([
            'status'     => 'required|in:pending,approved,collected,rejected',
            'admin_note' => 'nullable|string|max:500',
        ]);

        $cardRequest->update([
            'status'     => $request->status,
            'admin_note' => $request->admin_note,
        ]);

        return back()->with('success', 'Card request updated.');
    }

    // ── Admin: Update Requests ─────────────────────────────────────────────

    public function adminUpdateRequests()
    {
        $query = UpdateRequest::with('student')
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderBy('created_at', 'desc');

        if (!auth()->user()->isSuperAdmin()) {
            $query->whereHas('student', fn($q) => auth()->user()->applyStudentScope($q));
        }

        $requests = $query->paginate(25);

        return view('card.admin.update-requests', compact('requests'));
    }

    public function adminReviewUpdateRequest(Request $request, UpdateRequest $updateRequest)
    {
        $request->validate([
            'action'     => 'required|in:approve,reject',
            'admin_note' => 'nullable|string|max:500',
        ]);

        if ($request->action === 'approve') {
            // Apply only safe, allowed fields
            $allowed = [
                'mobile', 'email', 'parent_contact', 'guardian_contact',
                'permanent_province', 'permanent_district', 'permanent_municipality', 'permanent_ward', 'permanent_tole',
                'temporary_province', 'temporary_district', 'temporary_municipality', 'temporary_ward', 'temporary_tole',
                'zone', 'district', 'municipality',
            ];
            $changes = array_intersect_key($updateRequest->requested_changes, array_flip($allowed));
            $updateRequest->student->update($changes);
            $updateRequest->update(['status' => 'approved', 'admin_note' => $request->admin_note]);
        } else {
            $updateRequest->update(['status' => 'rejected', 'admin_note' => $request->admin_note]);
        }

        return back()->with('success', 'Update request ' . $request->action . 'd successfully.');
    }
}
