<?php

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Models\Learning\LearningClass;
use App\Models\Learning\LearningCourse;
use App\Models\Learning\LearningQuiz;
use App\Models\Learning\LearningQuizOption;
use App\Models\Learning\LearningQuizQuestion;
use App\Models\Learning\LearningSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminQuizController extends Controller
{
    public function index(Request $request)
    {
        $query = LearningQuiz::with(['course.learningClass', 'course.subject', 'creator'])
            ->withCount('questions')
            ->withCount('attempts')
            ->orderBy('sort_order')
            ->orderByDesc('created_at');

        if ($request->filled('course')) {
            $query->where('learning_course_id', $request->input('course'));
        }

        if ($request->filled('class')) {
            $query->whereHas('course', fn ($q) => $q->where('learning_class_id', $request->input('class')));
        }

        if ($request->filled('subject')) {
            $query->whereHas('course', fn ($q) => $q->where('learning_subject_id', $request->input('subject')));
        }

        $quizzes  = $query->paginate(20)->withQueryString();
        $courses  = LearningCourse::orderBy('title')->get(['id', 'title', 'learning_class_id', 'learning_subject_id']);
        $classes  = LearningClass::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $subjects = LearningSubject::where('is_active', true)->orderBy('name')->get();

        return view('learning.admin.quizzes.index', compact('quizzes', 'courses', 'classes', 'subjects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'learning_course_id'  => 'required|exists:learning_courses,id',
            'time_limit_minutes'  => 'nullable|integer|min:1|max:180',
            'pass_percentage'     => 'required|integer|min:1|max:100',
            'max_attempts'        => 'required|integer|min:1|max:10',
            'is_published'        => 'boolean',
            'sort_order'          => 'nullable|integer|min:0',
        ]);

        $data['created_by']   = Auth::id();
        $data['is_published'] = $request->boolean('is_published');

        $quiz = LearningQuiz::create($data);

        if ($request->boolean('_redirect_manage')) {
            return redirect()->route('admin.learning.quizzes.manage', $quiz)
                ->with('success', 'Quiz created — now add your questions.');
        }

        return back()->with('success', 'Quiz created.');
    }

    public function manage(LearningQuiz $quiz)
    {
        $quiz->load(['questions.options', 'course.chapters.lessons']);

        // Lessons belonging to this quiz's course (for the lesson-link picker)
        $courseLessons = $quiz->course
            ? $quiz->course->chapters->flatMap(fn ($ch) => $ch->lessons)->values()
            : collect();

        return view('learning.admin.quizzes.manage', compact('quiz', 'courseLessons'));
    }

    public function update(Request $request, LearningQuiz $quiz)
    {
        $data = $request->validate([
            'title'                => 'required|string|max:255',
            'description'          => 'nullable|string',
            'learning_course_id'   => 'nullable|exists:learning_courses,id',
            'learning_lesson_id'   => 'nullable|exists:learning_lessons,id',
            'time_limit_minutes'   => 'nullable|integer|min:1|max:180',
            'pass_percentage'      => 'required|integer|min:1|max:100',
            'max_attempts'         => 'required|integer|min:1|max:10',
            'is_published'         => 'boolean',
            'sort_order'         => 'nullable|integer|min:0',
        ]);

        $data['is_published'] = $request->boolean('is_published');
        $quiz->update($data);

        return back()->with('success', 'Quiz updated.');
    }

    public function destroy(LearningQuiz $quiz)
    {
        $quiz->delete();
        return redirect()->route('admin.learning.quizzes.index')->with('success', 'Quiz deleted.');
    }

    public function storeQuestion(Request $request, LearningQuiz $quiz)
    {
        $data = $request->validate([
            'question_text' => 'required|string',
            'type'          => 'required|in:mcq,short_answer',
            'marks'         => 'required|integer|min:1|max:100',
            'sort_order'    => 'nullable|integer|min:0',
            'explanation'   => 'nullable|string',
            // MCQ options
            'options'       => 'required_if:type,mcq|array|min:2',
            'options.*'     => 'required_if:type,mcq|string',
            'correct_option' => 'required_if:type,mcq|integer',
        ]);

        $question = $quiz->questions()->create([
            'question_text' => $data['question_text'],
            'type'          => $data['type'],
            'marks'         => $data['marks'],
            'sort_order'    => $data['sort_order'] ?? $quiz->questions()->max('sort_order') + 1,
            'explanation'   => $data['explanation'] ?? null,
        ]);

        if ($data['type'] === 'mcq' && isset($data['options'])) {
            foreach ($data['options'] as $index => $optionText) {
                if (blank($optionText)) continue;
                $question->options()->create([
                    'option_text' => $optionText,
                    'is_correct'  => (int) ($data['correct_option'] ?? -1) === $index,
                    'sort_order'  => $index,
                ]);
            }
        }

        return back()->with('success', 'Question added.');
    }

    public function updateQuestion(Request $request, LearningQuiz $quiz, LearningQuizQuestion $question)
    {
        $data = $request->validate([
            'question_text' => 'required|string',
            'marks'         => 'required|integer|min:1',
            'sort_order'    => 'nullable|integer|min:0',
            'explanation'   => 'nullable|string',
        ]);

        $question->update($data);

        return back()->with('success', 'Question updated.');
    }

    public function destroyQuestion(LearningQuiz $quiz, LearningQuizQuestion $question)
    {
        $question->delete();
        return back()->with('success', 'Question deleted.');
    }

    public function storeOption(Request $request, LearningQuiz $quiz, LearningQuizQuestion $question)
    {
        $request->validate([
            'option_text' => 'required|string',
            'is_correct'  => 'boolean',
        ]);

        if ($request->boolean('is_correct')) {
            $question->options()->update(['is_correct' => false]);
        }

        $question->options()->create([
            'option_text' => $request->option_text,
            'is_correct'  => $request->boolean('is_correct'),
            'sort_order'  => $question->options()->max('sort_order') + 1,
        ]);

        return back()->with('success', 'Option added.');
    }

    public function destroyOption(LearningQuiz $quiz, LearningQuizQuestion $question, LearningQuizOption $option)
    {
        $option->delete();
        return back()->with('success', 'Option deleted.');
    }
}
