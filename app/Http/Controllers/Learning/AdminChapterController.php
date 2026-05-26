<?php

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Models\Learning\LearningChapter;
use App\Models\Learning\LearningCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminChapterController extends Controller
{
    public function store(Request $request, LearningCourse $course)
    {
        abort_unless(Auth::user()->canManageLearningCourse($course), 403);

        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
        ]);

        $data['learning_course_id'] = $course->id;
        $data['sort_order'] ??= (int) $course->chapters()->max('sort_order') + 1;

        $course->chapters()->create($data);

        return back()->with('success', 'Chapter added.');
    }

    public function update(Request $request, LearningCourse $course, LearningChapter $chapter)
    {
        abort_unless($chapter->learning_course_id === $course->id, 404);
        abort_unless(Auth::user()->canManageLearningCourse($course), 403);

        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
        ]);

        $chapter->update($data);

        return back()->with('success', 'Chapter updated.');
    }

    public function destroy(LearningCourse $course, LearningChapter $chapter)
    {
        abort_unless($chapter->learning_course_id === $course->id, 404);
        abort_unless(Auth::user()->canManageLearningCourse($course), 403);

        $chapter->delete();

        return back()->with('success', 'Chapter deleted.');
    }
}
