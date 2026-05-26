@extends('learning.layouts.app')

@section('title', $quiz->title)

@push('styles')
<style>
    .option-card input[type="radio"] { display: none; }
    .option-card label { display: block; cursor: pointer; transition: all .15s; }
    .option-card input[type="radio"]:checked + label {
        border-color: #1a5632 !important;
        background-color: #f0fdf4 !important;
        color: #14532d;
    }
</style>
@endpush

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Quiz header --}}
    <div class="rounded-2xl bg-gradient-to-br from-[#0b2415] to-[#1a5632] p-6 text-white shadow-sm">
        <p class="text-sm font-bold uppercase tracking-widest text-white/50 mb-1">Mock Test</p>
        <h1 class="text-2xl font-extrabold leading-tight">{{ $quiz->title }}</h1>
        @if($quiz->description)
            <p class="mt-2 text-sm text-white/70">{{ $quiz->description }}</p>
        @endif
        <div class="mt-4 flex flex-wrap gap-4 text-sm font-semibold text-white/80">
            <span class="flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                {{ $questions->count() }} Questions
            </span>
            <span class="flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                {{ $quiz->totalMarks() }} Marks
            </span>
            <span class="flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Pass: {{ $quiz->pass_percentage }}%
            </span>
            @if($quiz->time_limit_minutes)
            <span class="flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ $quiz->time_limit_minutes }} min
            </span>
            @endif
        </div>
    </div>

    {{-- Previous attempt info --}}
    @if($lastAttempt)
    <div class="rounded-2xl border {{ $lastAttempt->passed ? 'border-green-200 bg-green-50' : 'border-amber-200 bg-amber-50' }} p-4">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <p class="font-extrabold {{ $lastAttempt->passed ? 'text-green-700' : 'text-amber-700' }}">
                    {{ $lastAttempt->passed ? '✓ You passed' : '✗ Not passed' }} — Last attempt: {{ $lastAttempt->score }}/{{ $lastAttempt->total_marks }} ({{ $lastAttempt->percentage() }}%)
                </p>
                <p class="text-xs text-gray-500 mt-0.5">Attempts used: {{ $attemptsUsed }} / {{ $quiz->max_attempts }}</p>
            </div>
            <a href="{{ route('learning.quizzes.result', [$quiz, $lastAttempt]) }}"
               class="rounded-xl border border-current px-3 py-1.5 text-xs font-extrabold {{ $lastAttempt->passed ? 'text-green-700' : 'text-amber-700' }} hover:bg-white/50">
                View Last Result
            </a>
        </div>
    </div>
    @endif

    @if(!$canAttempt)
    <div class="rounded-2xl border border-red-200 bg-red-50 p-5 text-center">
        <p class="font-extrabold text-red-700">You have used all {{ $quiz->max_attempts }} attempts for this quiz.</p>
        @if($lastAttempt)
            <a href="{{ route('learning.quizzes.result', [$quiz, $lastAttempt]) }}"
               class="mt-3 inline-block rounded-xl bg-red-600 px-5 py-2 text-sm font-extrabold text-white hover:bg-red-700">View Last Result</a>
        @endif
    </div>
    @else

    {{-- Timer --}}
    @if($quiz->time_limit_minutes)
    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 flex items-center justify-between"
         x-data="quizTimer({{ $quiz->time_limit_minutes * 60 }})"
         x-init="startTimer()">
        <span class="font-semibold text-amber-800">Time remaining:</span>
        <span class="text-xl font-extrabold text-amber-700 font-mono" x-text="timeDisplay"></span>
    </div>
    @endif

    {{-- Questions form --}}
    <form method="POST" action="{{ route('learning.quizzes.submit', $quiz) }}"
          id="quizForm"
          onsubmit="return confirm('Submit your answers? You cannot change them after submitting.')">
        @csrf
        <div class="space-y-5">
            @foreach($questions as $qIndex => $question)
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="px-5 py-4 bg-gray-50/60 flex items-center gap-3">
                    <span class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-xs font-extrabold {{ $question->type === 'mcq' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                        {{ $qIndex + 1 }}
                    </span>
                    <div class="flex-1">
                        <p class="font-bold text-gray-900">{!! $question->question_text !!}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $question->type === 'mcq' ? 'Choose one' : 'Write your answer' }} · {{ $question->marks }} {{ $question->marks == 1 ? 'mark' : 'marks' }}
                        </p>
                    </div>
                </div>

                <div class="px-5 py-4">
                    @if($question->type === 'mcq')
                        <div class="space-y-2">
                            @foreach($question->options as $optIndex => $option)
                            <div class="option-card">
                                <input type="radio"
                                       id="opt_{{ $question->id }}_{{ $option->id }}"
                                       name="answers[{{ $question->id }}]"
                                       value="{{ $option->id }}">
                                <label for="opt_{{ $question->id }}_{{ $option->id }}"
                                       class="flex items-center gap-3 rounded-xl border-2 border-gray-200 px-4 py-3 text-sm font-semibold text-gray-700 hover:border-[#1a5632]/40 hover:bg-gray-50">
                                    <span class="shrink-0 w-6 h-6 rounded-full border-2 border-current flex items-center justify-center text-xs font-extrabold text-gray-400">
                                        {{ chr(65 + $optIndex) }}
                                    </span>
                                    {{ $option->option_text }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <textarea name="answers[{{ $question->id }}]" rows="4"
                                  class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5632]/20 focus:border-[#1a5632] resize-none"
                                  placeholder="Write your answer here..."></textarea>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6 flex flex-wrap items-center gap-3 justify-between">
            <a href="{{ $quiz->course ? route('learning.courses.show', $quiz->course) : route('learning.dashboard') }}"
               class="rounded-xl border border-gray-200 px-5 py-3 text-sm font-extrabold text-gray-600 hover:bg-gray-50 transition-colors">
                ← Back
            </a>
            <button type="submit"
                    class="flex-1 sm:flex-none rounded-xl bg-[#1a5632] px-8 py-3 text-sm font-extrabold text-white hover:bg-[#0b2415] transition-colors shadow-sm">
                Submit Answers
            </button>
        </div>
    </form>
    @endif

</div>
@endsection

@push('scripts')
<script>
function quizTimer(seconds) {
    return {
        remaining: seconds,
        timeDisplay: '',
        timer: null,
        startTimer() {
            this.update();
            this.timer = setInterval(() => {
                this.remaining--;
                this.update();
                if (this.remaining <= 0) {
                    clearInterval(this.timer);
                    document.getElementById('quizForm')?.submit();
                }
            }, 1000);
        },
        update() {
            const m = Math.floor(this.remaining / 60).toString().padStart(2, '0');
            const s = (this.remaining % 60).toString().padStart(2, '0');
            this.timeDisplay = m + ':' + s;
        }
    };
}
</script>
@endpush
