<?php

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Models\Learning\LearningClass;
use App\Models\Learning\LearningSubject;
use App\Models\Learning\LearningTeacherClassMap;
use App\Models\User;
use App\Services\LearningClassSyncService;
use Illuminate\Http\Request;

class AdminTeacherMapController extends Controller
{
    public function index(Request $request)
    {
        app(LearningClassSyncService::class)->syncFromCardDepartments();

        $classes = LearningClass::query()
            ->where('is_active', true)
            ->withCount('teacherMaps')
            ->with(['teachers' => fn ($q) => $q->orderBy('name')])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $selectedClass = ($request->filled('tab') && $request->tab !== 'all')
            ? $classes->firstWhere('id', (int) $request->tab)
            : null;

        $teachers = User::query()
            ->role('teacher')
            ->with([
                'assignedLearningClasses'  => fn ($q) => $q->orderBy('sort_order')->orderBy('name'),
                'assignedLearningSubjects' => fn ($q) => $q->orderBy('name'),
            ])
            ->orderBy('name')
            ->get();

        // Subjects for the selected class, with their assigned teachers pre-loaded
        $classSubjects = $selectedClass
            ? LearningSubject::where('learning_class_id', $selectedClass->id)
                ->where('is_active', true)
                ->with(['assignedTeachers' => fn ($q) => $q->orderBy('name')])
                ->orderBy('name')
                ->get()
            : collect();

        return view('learning.admin.teacher-maps.index', compact('classes', 'teachers', 'selectedClass', 'classSubjects'));
    }

    public function update(Request $request, User $teacher)
    {
        abort_unless($teacher->isTeacher(), 404);

        $data = $request->validate([
            'class_ids'   => ['array'],
            'class_ids.*' => ['integer', 'exists:learning_classes,id'],
        ]);

        $classIds = collect($data['class_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $teacher->assignedLearningClasses()->sync(
            $classIds->mapWithKeys(fn ($classId) => [
                $classId => ['assigned_by' => $request->user()->id],
            ])->all()
        );

        return back()->with('success', "Class access updated for {$teacher->name}.");
    }

    public function updateByClass(Request $request, LearningClass $class)
    {
        $data = $request->validate([
            'user_ids'   => ['array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $userIds = collect($data['user_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $class->teachers()->sync(
            $userIds->mapWithKeys(fn ($userId) => [
                $userId => ['assigned_by' => $request->user()->id],
            ])->all()
        );

        return redirect()
            ->route('admin.learning.teacher-maps.index', ['tab' => $class->id])
            ->with('success', "Teacher assignments updated for {$class->name}.");
    }

    public function updateBySubject(Request $request, LearningSubject $subject)
    {
        $data = $request->validate([
            'user_ids'   => ['array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $userIds = collect($data['user_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $subject->assignedTeachers()->sync(
            $userIds->mapWithKeys(fn ($userId) => [
                $userId => ['assigned_by' => $request->user()->id],
            ])->all()
        );

        return redirect()
            ->route('admin.learning.teacher-maps.index', ['tab' => $subject->learning_class_id])
            ->with('success', "Subject assignments updated for {$subject->name}.");
    }

    public function destroy(User $teacher, LearningClass $class)
    {
        LearningTeacherClassMap::query()
            ->where('user_id', $teacher->id)
            ->where('learning_class_id', $class->id)
            ->delete();

        return back()->with('success', 'Class access removed.');
    }
}
