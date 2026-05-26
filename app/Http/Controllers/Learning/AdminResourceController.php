<?php

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Models\Learning\LearningClass;
use App\Models\Learning\LearningResource;
use App\Models\Learning\LearningSubject;
use App\Services\LearningClassSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

class AdminResourceController extends Controller
{
    public function index(Request $request)
    {
        app(LearningClassSyncService::class)->syncFromCardDepartments();

        $user = $request->user();
        $manageableClassIds = $this->manageableClassIds($user);
        $isTeacherScoped = $user->isTeacher() && ! $this->hasFullResourceAccess($user);

        $classes = LearningClass::where('is_active', true)
            ->when($isTeacherScoped, fn ($query) => $query->whereIn('id', $manageableClassIds))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $subjects = LearningSubject::with('learningClass')
            ->where('is_active', true)
            ->when($isTeacherScoped, fn ($query) => $query->whereIn('learning_class_id', $manageableClassIds))
            ->orderBy('name')
            ->get();

        $resources = LearningResource::with(['learningClass', 'subject', 'creator'])
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->type))
            ->when($request->filled('class'), fn ($query) => $query->where('learning_class_id', $request->class))
            ->when($isTeacherScoped, fn ($query) => $query->where(function ($scoped) use ($manageableClassIds) {
                $scoped->whereIn('learning_class_id', $manageableClassIds)
                    ->orWhereNull('learning_class_id');
            }))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('learning.admin.resources.index', compact('classes', 'subjects', 'resources', 'manageableClassIds', 'isTeacherScoped'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['is_published'] = $request->boolean('is_published');
        $data['created_by'] = $request->user()->id;

        $this->authorizeClassManagement($request->user(), $data['learning_class_id'] ?? null);

        if ($request->hasFile('file')) {
            $data['file_path'] = $this->storeFile($request);
        }

        LearningResource::create($data);

        return back()->with('success', 'Resource added.');
    }

    public function update(Request $request, LearningResource $resource)
    {
        $data = $this->validated($request);
        $data['is_published'] = $request->boolean('is_published');

        $this->authorizeResourceMutation($request->user(), $resource, $data['learning_class_id'] ?? null);

        if ($request->hasFile('file')) {
            $this->deleteFile($resource->file_path);
            $data['file_path'] = $this->storeFile($request);
        }

        $resource->update($data);

        return back()->with('success', 'Resource updated.');
    }

    public function destroy(LearningResource $resource)
    {
        $this->authorizeResourceMutation(request()->user(), $resource, $resource->learning_class_id);

        $this->deleteFile($resource->file_path);
        $resource->delete();

        return back()->with('success', 'Resource deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'learning_class_id' => ['nullable', 'exists:learning_classes,id'],
            'learning_subject_id' => ['nullable', 'exists:learning_subjects,id'],
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['note', 'syllabus', 'old-question', 'practice-material'])],
            'description' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'max:20480'],
        ]);
    }

    private function hasFullResourceAccess($user): bool
    {
        return $user->isSuperAdmin() || $user->isPrincipal() || $user->hasAnyRole(['administrator']);
    }

    private function manageableClassIds($user): array
    {
        if ($this->hasFullResourceAccess($user)) {
            return LearningClass::query()->pluck('id')->map(fn ($id) => (int) $id)->all();
        }

        if (! $user->isTeacher()) {
            return [];
        }

        return $user->assignedLearningClasses()
            ->pluck('learning_classes.id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    private function authorizeClassManagement($user, ?int $classId): void
    {
        if ($this->hasFullResourceAccess($user)) {
            return;
        }

        abort_unless($user->canManageLearningClass($classId), 403);
    }

    private function authorizeResourceMutation($user, LearningResource $resource, ?int $targetClassId): void
    {
        if ($this->hasFullResourceAccess($user)) {
            return;
        }

        abort_unless($resource->created_by === $user->id, 403);
        $this->authorizeClassManagement($user, $targetClassId);
    }

    private function storeFile(Request $request): string
    {
        $dir = public_path('uploads/learning/resources');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $file = $request->file('file');
        $name = uniqid('resource_', true) . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $name);

        return 'uploads/learning/resources/' . $name;
    }

    private function deleteFile(?string $path): void
    {
        if ($path && str_starts_with($path, 'uploads/learning/resources/') && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}
