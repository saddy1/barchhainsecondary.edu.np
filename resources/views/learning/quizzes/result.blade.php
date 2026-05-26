@extends('learning.layouts.app')

@section('title', 'Result: ' . $quiz->title)

@push('styles')
@if($attempt->passed)
<style>
    @keyframes result-pop {
        0% { transform: scale(.92); opacity: 0; }
        60% { transform: scale(1.03); opacity: 1; }
        100% { transform: scale(1); opacity: 1; }
    }
    @keyframes result-confetti {
        0% { transform: translateY(-12px) rotate(0deg); opacity: 0; }
        15% { opacity: 1; }
        100% { transform: translateY(130px) rotate(280deg); opacity: 0; }
    }
    .result-pop { animation: result-pop .45s ease-out both; }
    .confetti-piece {
        position: absolute;
        top: 0;
        width: 8px;
        height: 14px;
        border-radius: 3px;
        animation: result-confetti 1.8s ease-in-out infinite;
    }
</style>
@endif
@endpush

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

    {{-- Score card --}}
    <div class="relative overflow-hidden rounded-2xl {{ $attempt->passed ? 'bg-gradient-to-br from-emerald-700 to-[#1a5632]' : 'bg-gradient-to-br from-slate-700 to-slate-500' }} px-4 py-6 sm:px-6 text-white shadow-sm text-center result-pop">
        @if($attempt->passed)
            @foreach(range(1, 18) as $i)
                <span class="confetti-piece"
                      style="left: {{ ($i * 5) % 96 }}%; background: {{ ['#fbbf24', '#34d399', '#60a5fa', '#f472b6', '#ffffff'][$i % 5] }}; animation-delay: {{ ($i % 6) * .15 }}s;"></span>
            @endforeach
        @endif

        <p class="text-xs font-bold uppercase tracking-widest text-white/60">{{ $quiz->title }}</p>

        <div class="mt-3">
            <span class="text-4xl font-black leading-none sm:text-5xl lg:text-6xl">{{ $attempt->percentage() }}%</span>
        </div>

        <p class="mt-2 text-base sm:text-lg font-extrabold">
            {{ $attempt->passed ? 'Congratulations! You Passed!' : 'Not Passed - Keep Studying!' }}
        </p>
        <p class="mt-1 text-sm font-semibold text-white/75">
            Score: {{ $attempt->score }} / {{ $attempt->total_marks }} &nbsp;·&nbsp; Pass mark: {{ $quiz->pass_percentage }}%
        </p>

        <div class="mt-4 flex flex-wrap justify-center gap-2">
            <a href="{{ route('learning.quizzes.show', $quiz) }}"
               class="rounded-xl bg-white/20 hover:bg-white/30 px-4 py-2 text-sm font-extrabold transition-colors">
                {{ $attempt->passed ? 'Retake Quiz' : 'Try Again' }}
            </a>
            @if($quiz->course)
            <a href="{{ route('learning.courses.show', $quiz->course) }}"
               class="rounded-xl bg-white/20 hover:bg-white/30 px-4 py-2 text-sm font-extrabold transition-colors">
                Back to Course
            </a>
            @endif
        </div>
    </div>

    {{-- Answer review --}}
    <div class="space-y-3">
        <h2 class="text-lg font-extrabold text-gray-900">Answer Review</h2>

        @foreach($quiz->questions as $qIndex => $question)
        @php $answer = $attempt->answers->firstWhere('learning_quiz_question_id', $question->id); @endphp
        <div class="rounded-2xl border {{ ($answer?->is_correct === true) ? 'border-green-200 bg-green-50/40' : (($answer?->is_correct === false) ? 'border-red-200 bg-red-50/30' : 'border-amber-200 bg-amber-50/30') }} overflow-hidden">

            {{-- Question header --}}
            <div class="flex items-start gap-3 px-4 py-3">
                <span class="shrink-0 w-7 h-7 rounded-full flex items-center justify-center text-xs font-extrabold
                    {{ ($answer?->is_correct === true) ? 'bg-green-100 text-green-700' : (($answer?->is_correct === false) ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                    {{ $qIndex + 1 }}
                </span>
                <div class="flex-1">
                    <p class="text-sm font-bold text-gray-900">{!! $question->question_text !!}</p>
                    <p class="text-xs mt-0.5 font-semibold
                        {{ ($answer?->is_correct === true) ? 'text-green-600' : (($answer?->is_correct === false) ? 'text-red-500' : 'text-amber-600') }}">
                        @if($answer?->is_correct === true)
                            ✓ Correct — {{ $question->marks }} {{ $question->marks == 1 ? 'mark' : 'marks' }}
                        @elseif($answer?->is_correct === false)
                            ✗ Incorrect — 0 marks
                        @else
                            ⏳ Pending review
                        @endif
                    </p>
                </div>
            </div>

            <div class="px-4 pb-4 space-y-1.5">
                @if($question->type === 'mcq')
                    @foreach($question->options as $option)
                    @php
                        $isSelected = $answer?->selected_option_id == $option->id;
                        $isCorrect  = $option->is_correct;
                    @endphp
                    <div class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-sm font-semibold
                        {{ $isCorrect ? 'bg-green-100 text-green-800 border border-green-300'
                            : ($isSelected && !$isCorrect ? 'bg-red-100 text-red-700 border border-red-300'
                            : 'bg-white border border-gray-200 text-gray-600') }}">
                        <span class="shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center text-[10px] font-extrabold
                            {{ $isCorrect ? 'border-green-500 text-green-600' : ($isSelected ? 'border-red-400 text-red-500' : 'border-gray-300 text-gray-400') }}">
                            @if($isCorrect) ✓ @elseif($isSelected) ✗ @endif
                        </span>
                        {{ $option->option_text }}
                        @if($isSelected && !$isCorrect) <span class="ml-auto text-xs text-red-400 font-bold">Your answer</span> @endif
                        @if($isCorrect) <span class="ml-auto text-xs text-green-600 font-bold">Correct answer</span> @endif
                    </div>
                    @endforeach
                @else
                    {{-- Short answer --}}
                    <div class="rounded-xl bg-white border border-gray-200 px-4 py-3">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Your answer</p>
                        <p class="text-sm text-gray-700">{{ $answer?->text_answer ?? '(no answer)' }}</p>
                    </div>
                    @if($answer?->is_correct === null)
                    <p class="text-xs text-amber-600 font-semibold">This question will be reviewed by your teacher.</p>
                    @endif
                @endif

                @if($question->explanation)
                <div class="mt-2 rounded-xl bg-blue-50 border border-blue-100 px-3 py-2 text-sm text-blue-700">
                    <span class="font-extrabold">Explanation:</span> {{ $question->explanation }}
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- Footer actions --}}
    <div class="flex flex-wrap gap-3 pt-2">
        <a href="{{ route('learning.quizzes.show', $quiz) }}"
           class="rounded-xl bg-[#1a5632] px-6 py-3 text-sm font-extrabold text-white hover:bg-[#0b2415] transition-colors">
            {{ $attempt->passed ? 'Retake Quiz' : 'Try Again' }}
        </a>
        <a href="{{ route('learning.dashboard') }}"
           class="rounded-xl border border-gray-200 px-6 py-3 text-sm font-extrabold text-gray-600 hover:bg-gray-50 transition-colors">
            My Courses
        </a>
    </div>

</div>
@endsection
