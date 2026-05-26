<?php

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Models\Learning\LearningCourse;
use App\Models\Learning\LearningResource;
use App\Models\Learning\LearningTeacherClassMap;
use App\Models\Learning\LearningProgress;
use App\Models\User;
use App\Services\LearningClassSyncService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function student(Request $request)
    {
        $user = $request->user();

        $courses = LearningCourse::query()
            ->with(['learningClass', 'subject', 'lessons' => fn ($query) => $query->where('is_published', true)])
            ->published()
            ->when($user->class_grade, fn ($query) => $query->whereHas('learningClass', fn ($classQuery) => $classQuery->where('name', $user->class_grade)))
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        $progress = LearningProgress::query()
            ->where('user_id', $user->id)
            ->whereNull('learning_lesson_id')
            ->pluck('progress_percent', 'learning_course_id');

        return view('learning.dashboard', compact('courses', 'progress'));
    }

    public function admin(Request $request)
    {
        app(LearningClassSyncService::class)->syncFromCardDepartments();

        $user = $request->user();
        $isTeacherScoped = $user?->isTeacher()
            && ! $user->isSuperAdmin()
            && ! $user->isPrincipal()
            && ! $user->hasRole('administrator');

        $assignedClassIds = $isTeacherScoped ? $user->assignedLearningClasses()->pluck('learning_classes.id')->all() : [];
        $assignedSubjectIds = $isTeacherScoped ? $user->assignedLearningSubjects()->pluck('learning_subjects.id')->all() : [];

        $visibleCoursesQuery = LearningCourse::query()
            ->with(['learningClass', 'subject'])
            ->withCount(['lessons' => fn ($query) => $query->where('is_published', true)])
            ->when($isTeacherScoped, function ($query) use ($assignedClassIds, $assignedSubjectIds) {
                $query->whereIn('learning_class_id', $assignedClassIds)
                    ->where(function ($subjectQuery) use ($assignedSubjectIds) {
                        $subjectQuery->whereNull('learning_subject_id')
                            ->orWhereIn('learning_subject_id', $assignedSubjectIds);
                    });
            });

        $courses = (clone $visibleCoursesQuery)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        $courseIds = $courses->pluck('id')->all();
        $classNames = $courses->pluck('learningClass.name')->filter()->unique()->values();

        $students = User::role('student')
            ->when($isTeacherScoped, fn ($query) => $query->whereIn('class_grade', $classNames))
            ->orderBy('class_grade')
            ->orderBy('section')
            ->orderBy('name')
            ->get();

        $courseProgressRows = LearningProgress::query()
            ->whereNull('learning_lesson_id')
            ->when($courseIds, fn ($query) => $query->whereIn('learning_course_id', $courseIds), fn ($query) => $query->whereRaw('1 = 0'))
            ->whereIn('user_id', $students->pluck('id'))
            ->get();

        $lessonProgressRows = LearningProgress::query()
            ->whereNotNull('learning_lesson_id')
            ->whereNotNull('completed_at')
            ->when($courseIds, fn ($query) => $query->whereIn('learning_course_id', $courseIds), fn ($query) => $query->whereRaw('1 = 0'))
            ->whereIn('user_id', $students->pluck('id'))
            ->get();

        $courseProgress = $courseProgressRows->groupBy('learning_course_id');
        $studentProgress = $courseProgressRows->groupBy('user_id');
        $courseLessonProgress = $lessonProgressRows->groupBy('learning_course_id');

        $courseRows = $courses->map(function (LearningCourse $course) use ($courseProgress, $courseLessonProgress, $students) {
            $rows = $courseProgress->get($course->id, collect());
            $completedLessons = $courseLessonProgress->get($course->id, collect())->count();
            $startedCount = $rows->count();
            $avgProgress = (int) round($rows->avg('progress_percent') ?? 0);

            return [
                'course' => $course,
                'students' => $students->where('class_grade', $course->learningClass?->name)->count(),
                'started' => $startedCount,
                'completed' => $rows->whereNotNull('completed_at')->count(),
                'avg_progress' => $avgProgress,
                'completed_lessons' => $completedLessons,
            ];
        });

        $studentRows = $students->map(function (User $student) use ($studentProgress, $courses) {
            $visibleCourseIds = $courses
                ->filter(fn (LearningCourse $course) => $course->learningClass?->name === $student->class_grade)
                ->pluck('id');

            $rows = $studentProgress->get($student->id, collect())
                ->whereIn('learning_course_id', $visibleCourseIds);

            return [
                'student' => $student,
                'course_count' => $visibleCourseIds->count(),
                'started' => $rows->count(),
                'completed' => $rows->whereNotNull('completed_at')->count(),
                'avg_progress' => (int) round($rows->avg('progress_percent') ?? 0),
                'last_activity' => $rows->sortByDesc('updated_at')->first()?->updated_at,
            ];
        })->sortByDesc('avg_progress')->values();

        $recentActivity = LearningProgress::query()
            ->with(['user', 'course.learningClass', 'course.subject', 'lesson'])
            ->whereNotNull('learning_lesson_id')
            ->when($courseIds, fn ($query) => $query->whereIn('learning_course_id', $courseIds), fn ($query) => $query->whereRaw('1 = 0'))
            ->whereIn('user_id', $students->pluck('id'))
            ->latest('updated_at')
            ->limit(8)
            ->get();

        $courseCount = $courses->count();
        $publishedCourseCount = $courses->where('status', 'published')->count();
        $studentCount = $students->count();
        $resourceCount = LearningResource::query()
            ->when($isTeacherScoped, fn ($query) => $query->whereIn('learning_class_id', $assignedClassIds))
            ->count();
        $teacherMapCount = $isTeacherScoped ? count($assignedClassIds) : LearningTeacherClassMap::count();
        $averageProgress = (int) round($courseProgressRows->avg('progress_percent') ?? 0);
        $completedLessonCount = $lessonProgressRows->count();

        return view('learning.admin.dashboard', compact(
            'courseCount',
            'publishedCourseCount',
            'studentCount',
            'resourceCount',
            'teacherMapCount',
            'averageProgress',
            'completedLessonCount',
            'courseRows',
            'studentRows',
            'recentActivity',
            'isTeacherScoped'
        ));
    }
}
