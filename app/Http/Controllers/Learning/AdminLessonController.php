<?php

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Models\Learning\LearningChapter;
use App\Models\Learning\LearningCourse;
use App\Models\Learning\LearningLesson;
use App\Models\Learning\LearningQuiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminLessonController extends Controller
{
    public function store(Request $request, LearningCourse $course, LearningChapter $chapter)
    {
        abort_unless($chapter->learning_course_id === $course->id, 404);
        abort_unless(Auth::user()->canManageLearningCourse($course), 403);

        $data = $this->validated($request);
        $data['learning_course_id']  = $course->id;
        $data['learning_chapter_id'] = $chapter->id;
        $data['sort_order'] ??= (int) $chapter->lessons()->max('sort_order') + 1;

        LearningLesson::create($data);

        return back()->with('success', 'Lesson added.');
    }

    public function update(Request $request, LearningCourse $course, LearningLesson $lesson)
    {
        abort_unless($lesson->learning_course_id === $course->id, 404);
        abort_unless(Auth::user()->canManageLearningCourse($course), 403);

        $lesson->update($this->validated($request));

        return back()->with('success', 'Lesson updated.');
    }

    public function attachQuiz(Request $request, LearningCourse $course, LearningLesson $lesson)
    {
        abort_unless($lesson->learning_course_id === $course->id, 404);
        abort_unless(Auth::user()->canManageLearningCourse($course), 403);

        $request->validate(['quiz_id' => 'nullable|exists:learning_quizzes,id']);

        // Detach any quiz currently linked to this lesson
        LearningQuiz::where('learning_lesson_id', $lesson->id)->update(['learning_lesson_id' => null]);

        if ($request->filled('quiz_id')) {
            LearningQuiz::where('id', $request->quiz_id)->update(['learning_lesson_id' => $lesson->id]);
        }

        return back()->with('success', 'Quiz assignment updated.');
    }

    public function destroy(LearningCourse $course, LearningLesson $lesson)
    {
        abort_unless($lesson->learning_course_id === $course->id, 404);
        abort_unless(Auth::user()->canManageLearningCourse($course), 403);

        $lesson->delete();

        return back()->with('success', 'Lesson deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'type'             => ['required', Rule::in(['video', 'audio', 'text'])],
            'description'      => ['nullable', 'string'],
            'content_body'     => ['nullable', 'string'],
            'video_url'        => ['nullable', 'url', 'max:500'],
            'audio_url'        => ['nullable', 'url', 'max:500'],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
            'sort_order'       => ['nullable', 'integer', 'min:0'],
            'is_published'     => ['required', 'boolean'],
            'is_free'          => ['required', 'boolean'],
        ]);
    }
}
