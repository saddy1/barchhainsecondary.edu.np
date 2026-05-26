<?php

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Models\Learning\LearningCourse;
use App\Models\Learning\LearningProgress;
use Illuminate\Http\Request;

class LessonProgressController extends Controller
{
    public function progress(Request $request, LearningCourse $course, int $lesson)
    {
        $lessonModel = $course->lessons()
            ->where('id', $lesson)
            ->where('is_published', true)
            ->firstOrFail();

        abort_unless(in_array($lessonModel->type, ['video', 'audio'], true), 404);

        $data = $request->validate([
            'current_seconds' => ['required', 'integer', 'min:0'],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
        ]);

        $progress = LearningProgress::firstOrCreate(
            [
                'user_id' => $request->user()->id,
                'learning_course_id' => $course->id,
                'learning_lesson_id' => $lessonModel->id,
            ],
            ['started_at' => now()]
        );

        $duration = max((int) ($data['duration_seconds'] ?? 0), (int) $progress->media_duration_seconds);
        $current = (int) $data['current_seconds'];

        if ($duration > 0) {
            $current = min($current, $duration);
        }

        $maxWatched = max((int) $progress->max_watched_seconds, $current);
        $percent = $duration > 0 ? min(99, (int) floor(($maxWatched / $duration) * 100)) : $progress->progress_percent;

        $progress->forceFill([
            'started_at' => $progress->started_at ?: now(),
            'current_seconds' => $current,
            'max_watched_seconds' => $maxWatched,
            'media_duration_seconds' => $duration,
            'progress_percent' => $progress->completed_at ? 100 : $percent,
        ])->save();

        return response()->json([
            'current_seconds' => $progress->current_seconds,
            'max_watched_seconds' => $progress->max_watched_seconds,
            'media_duration_seconds' => $progress->media_duration_seconds,
        ]);
    }

    public function complete(Request $request, LearningCourse $course, int $lesson)
    {
        $lessonModel = $course->lessons()->where('id', $lesson)->where('is_published', true)->firstOrFail();
        $progress = LearningProgress::where('user_id', $request->user()->id)
            ->where('learning_course_id', $course->id)
            ->where('learning_lesson_id', $lessonModel->id)
            ->first();

        if (in_array($lessonModel->type, ['video', 'audio'], true)) {
            $request->validate([
                'player_completed' => ['accepted'],
            ], [
                'player_completed.accepted' => 'Please finish the media lesson before moving to the next lesson.',
            ]);

            $duration = (int) ($progress?->media_duration_seconds ?? 0);
            $maxWatched = (int) ($progress?->max_watched_seconds ?? 0);

            if (! $progress || $duration <= 0 || $maxWatched < max(0, $duration - 5)) {
                return back()
                    ->withErrors(['player_completed' => 'Please watch the lesson until the end before continuing.'])
                    ->withInput();
            }
        }

        LearningProgress::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'learning_course_id' => $course->id,
                'learning_lesson_id' => $lessonModel->id,
            ],
            [
                'started_at' => now(),
                'completed_at' => now(),
                'progress_percent' => 100,
                'current_seconds' => max((int) ($progress?->current_seconds ?? 0), (int) ($progress?->media_duration_seconds ?? 0)),
                'max_watched_seconds' => max((int) ($progress?->max_watched_seconds ?? 0), (int) ($progress?->media_duration_seconds ?? 0)),
            ]
        );

        $totalLessons = max(1, $course->lessons()->where('is_published', true)->count());
        $completedLessons = LearningProgress::where('user_id', $request->user()->id)
            ->where('learning_course_id', $course->id)
            ->whereNotNull('learning_lesson_id')
            ->whereNotNull('completed_at')
            ->count();

        LearningProgress::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'learning_course_id' => $course->id,
                'learning_lesson_id' => null,
            ],
            [
                'started_at' => now(),
                'completed_at' => $completedLessons >= $totalLessons ? now() : null,
                'progress_percent' => min(100, (int) round(($completedLessons / $totalLessons) * 100)),
            ]
        );

        if ($lessonModel->quiz) {
            return redirect()
                ->route('learning.lessons.show', [$course, $lessonModel->id])
                ->with('status', 'Lesson complete! Take the mock test to continue.');
        }

        // Find next lesson in chapter order and redirect to it
        $flatLessons = $course->chapters()
            ->orderBy('sort_order')
            ->with(['lessons' => fn ($q) => $q->where('is_published', true)->orderBy('sort_order')])
            ->get()
            ->flatMap(fn ($ch) => $ch->lessons)
            ->values();

        $currentIndex = $flatLessons->search(fn ($l) => $l->id === $lessonModel->id);
        $nextLesson = $currentIndex !== false && isset($flatLessons[$currentIndex + 1])
            ? $flatLessons[$currentIndex + 1]
            : null;

        if ($nextLesson) {
            return redirect()
                ->route('learning.lessons.show', [$course, $nextLesson->id])
                ->with('status', 'Lesson complete! Continue to the next one.');
        }

        return redirect()
            ->route('learning.courses.show', $course)
            ->with('status', 'All lessons completed! Great work.');
    }
}
