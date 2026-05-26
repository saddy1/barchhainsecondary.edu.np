@extends('learning.layouts.admin')

@section('title', 'Quiz: ' . $quiz->title)

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="rounded-2xl bg-gradient-to-br from-[#0b2415] to-[#1a5632] p-5 sm:p-6 text-white shadow-sm flex flex-wrap items-start justify-between gap-4">
        <div class="min-w-0">
            <a href="{{ route('admin.learning.quizzes.index') }}"
               class="inline-flex items-center gap-1 text-xs font-bold text-white/50 hover:text-white/80 uppercase tracking-widest mb-2">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                Quizzes
            </a>
            <h1 class="text-2xl font-extrabold leading-tight">{{ $quiz->title }}</h1>
            <p class="mt-1 text-sm text-white/60">
                {{ $quiz->questions->count() }} questions ·
                {{ $quiz->totalMarks() }} total marks ·
                Pass {{ $quiz->pass_percentage }}%
                @if($quiz->time_limit_minutes) · ⏱ {{ $quiz->time_limit_minutes }}min @endif
            </p>
        </div>
        <span class="shrink-0 rounded-full {{ $quiz->is_published ? 'bg-emerald-400/25 text-emerald-200' : 'bg-white/10 text-white/50' }} px-3 py-1 text-xs font-extrabold">
            {{ $quiz->is_published ? 'Published' : 'Draft' }}
        </span>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">{{ session('success') }}</div>
    @endif

    {{-- Quiz settings edit --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm"
         x-data="{open: false, published: {{ $quiz->is_published ? 'true' : 'false' }}}">
        <button @click="open = !open" class="flex items-center gap-2 text-sm font-extrabold text-gray-700 hover:text-[#1a5632]">
            <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
            </svg>
            Edit Quiz Settings
        </button>
        <div x-show="open" x-cloak class="mt-4">
            <form method="POST" action="{{ route('admin.learning.quizzes.update', $quiz) }}">
                @csrf @method('PATCH')
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <input name="title" value="{{ $quiz->title }}" required placeholder="Title"
                           class="rounded-xl border border-gray-200 px-3 py-2.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-[#1a5632]/30 lg:col-span-2">
                    <input type="number" name="time_limit_minutes" value="{{ $quiz->time_limit_minutes }}" min="1" max="180" placeholder="Time limit (min)"
                           class="rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5632]/30">
                    <input type="number" name="pass_percentage" value="{{ $quiz->pass_percentage }}" min="1" max="100"
                           class="rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5632]/30"
                           placeholder="Pass %">
                    <input type="number" name="max_attempts" value="{{ $quiz->max_attempts }}" min="1" max="10"
                           class="rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5632]/30"
                           placeholder="Max attempts">
                    {{-- Lesson attachment --}}
                    <div class="sm:col-span-2 lg:col-span-3">
                        <label class="block text-[10px] font-extrabold uppercase tracking-widest text-gray-400 mb-1">Attach to Lesson (shows quiz after that lesson)</label>
                        <select name="learning_lesson_id"
                                class="w-full rounded-xl border border-amber-200 bg-white px-3 py-2.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-amber-400/30">
                            <option value="">— No lesson (course-level or standalone) —</option>
                            @foreach($courseLessons as $lesson)
                                <option value="{{ $lesson->id }}" {{ $quiz->learning_lesson_id == $lesson->id ? 'selected' : '' }}>
                                    {{ $lesson->title }}
                                </option>
                            @endforeach
                        </select>
                        @if($courseLessons->isEmpty())
                            <p class="mt-1 text-xs text-gray-400">No course linked — assign a course first to see lessons.</p>
                        @endif
                    </div>
                    <textarea name="description" rows="2" placeholder="Description"
                              class="rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5632]/30 sm:col-span-2 lg:col-span-3">{{ $quiz->description }}</textarea>
                </div>
                <div class="mt-3 flex flex-wrap items-center gap-3">
                    <input type="hidden" name="is_published" value="0">
                    <input type="checkbox" name="is_published" value="1" x-model="published" class="sr-only">
                    <button type="button" @click="published = !published"
                            class="inline-flex items-center gap-2 rounded-full border px-3 py-2 text-xs font-extrabold transition-all"
                            :class="published ? 'border-emerald-200 bg-emerald-50 text-emerald-700 ring-2 ring-emerald-100' : 'border-gray-200 bg-white text-gray-500 hover:bg-gray-50'">
                        <span class="flex h-4 w-4 items-center justify-center rounded-full"
                              :class="published ? 'bg-emerald-500 text-white' : 'border border-gray-300 bg-white'">
                            <svg x-show="published" class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"/>
                            </svg>
                        </span>
                        Published
                    </button>
                    <button class="rounded-xl bg-[#1a5632] px-5 py-2 text-sm font-extrabold text-white hover:bg-[#0b2415] transition-colors">Save</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Questions --}}
    <div class="space-y-4">
        @forelse($quiz->questions as $qIndex => $question)
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden"
             x-data="{editQ: false, addOption: false}">

            {{-- Question header --}}
            <div class="flex items-start gap-4 px-5 py-4">
                <span class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-xs font-extrabold {{ $question->type === 'mcq' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}">
                    {{ $qIndex + 1 }}
                </span>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-gray-900">{!! nl2br(e($question->question_text)) !!}</p>
                    <div class="flex flex-wrap gap-2 mt-1">
                        <span class="text-xs font-bold rounded-full {{ $question->type === 'mcq' ? 'bg-blue-50 text-blue-600' : 'bg-purple-50 text-purple-600' }} px-2 py-0.5">
                            {{ $question->type === 'mcq' ? 'MCQ' : 'Short Answer' }}
                        </span>
                        <span class="text-xs font-bold text-gray-400">{{ $question->marks }} {{ $question->marks === 1 ? 'mark' : 'marks' }}</span>
                    </div>

                    {{-- MCQ options display --}}
                    @if($question->type === 'mcq')
                    <div class="mt-3 space-y-1.5">
                        @foreach($question->options as $opt)
                        <div class="flex items-center gap-2">
                            <span class="shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center text-[10px] font-extrabold
                                {{ $opt->is_correct ? 'border-green-400 bg-green-50 text-green-700' : 'border-gray-200 text-gray-400' }}">
                                {{ $opt->is_correct ? '✓' : '' }}
                            </span>
                            <span class="text-sm {{ $opt->is_correct ? 'font-bold text-green-700' : 'text-gray-600' }}">{{ $opt->option_text }}</span>
                            @if(auth()->user()?->canAccess('learning.lessons.edit'))
                            <form method="POST" action="{{ route('admin.learning.quizzes.options.destroy', [$quiz, $question, $opt]) }}" class="ml-auto shrink-0">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 text-xs font-bold" onclick="return confirm('Remove this option?')">✕</button>
                            </form>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @if($question->explanation)
                    <p class="mt-2 text-xs italic text-gray-400">💡 {{ $question->explanation }}</p>
                    @endif
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button @click="editQ = !editQ"
                            class="rounded-lg border border-gray-200 px-2.5 py-1.5 text-xs font-bold text-gray-600 hover:bg-gray-50">Edit</button>
                    <form method="POST" action="{{ route('admin.learning.quizzes.questions.destroy', [$quiz, $question]) }}"
                          onsubmit="return confirm('Delete this question?')">
                        @csrf @method('DELETE')
                        <button class="rounded-lg border border-red-200 px-2.5 py-1.5 text-xs font-bold text-red-500 hover:bg-red-50">Del</button>
                    </form>
                </div>
            </div>

            {{-- Edit question --}}
            <div x-show="editQ" x-cloak class="border-t border-gray-100 bg-amber-50/50 px-4 py-4">
                <form method="POST" action="{{ route('admin.learning.quizzes.questions.update', [$quiz, $question]) }}">
                    @csrf @method('PATCH')
                    <div class="space-y-3">
                        <textarea name="question_text" rows="3" required
                                  class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5632]/30">{{ $question->question_text }}</textarea>
                        <input name="explanation" value="{{ $question->explanation }}" placeholder="Explanation (optional)"
                               class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5632]/30">
                        <div class="flex items-center gap-2">
                            <input type="number" name="marks" value="{{ $question->marks }}" min="1"
                                   class="w-24 rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5632]/30"
                                   placeholder="Marks">
                            <div class="flex gap-2 ml-auto">
                                <button type="button" @click="editQ = false"
                                        class="rounded-xl border border-gray-200 px-3 py-2 text-xs font-extrabold text-gray-500 hover:bg-gray-50">Cancel</button>
                                <button class="rounded-xl bg-[#1a5632] px-4 py-2 text-xs font-extrabold text-white hover:bg-[#0b2415]">Save</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Add option (MCQ only) --}}
            @if($question->type === 'mcq')
            <div class="border-t border-dashed border-gray-100">
                <button @click="addOption = !addOption"
                        class="w-full flex items-center gap-2 px-5 py-2.5 text-xs font-bold text-blue-600 hover:bg-blue-50/50 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span x-text="addOption ? 'Cancel' : 'Add Option'"></span>
                </button>
                <div x-show="addOption" x-cloak class="px-4 pb-4 bg-blue-50/30">
                    <form method="POST" action="{{ route('admin.learning.quizzes.options.store', [$quiz, $question]) }}">
                        @csrf
                        <div class="flex flex-wrap gap-2 items-center">
                            <input name="option_text" required placeholder="Option text"
                                   class="flex-1 min-w-40 rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5632]/30">
                            <label class="flex items-center gap-1.5 text-xs font-bold text-gray-600 whitespace-nowrap">
                                <input type="checkbox" name="is_correct" value="1" class="rounded border-gray-300 text-[#1a5632]">
                                Correct
                            </label>
                            <button class="rounded-xl bg-blue-600 px-4 py-2 text-xs font-extrabold text-white hover:bg-blue-700">Add</button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>
        @empty
            <div class="rounded-2xl border-2 border-dashed border-gray-200 py-10 text-center text-gray-400">
                <p class="font-extrabold">No questions yet</p>
                <p class="text-sm mt-1">Add the first question below.</p>
            </div>
        @endforelse
    </div>

    {{-- Add question --}}
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-5"
         x-data="{open: {{ $quiz->questions->isEmpty() ? 'true' : 'false' }}, qType: 'mcq', options: ['','','',''], correctIdx: 0}">

        <button @click="open = !open"
                class="flex items-center gap-2 text-sm font-extrabold text-[#1a5632] hover:text-[#0b2415]">
            <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-45' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            <span x-text="open ? 'Cancel' : 'Add Question'"></span>
        </button>

        <div x-show="open" x-cloak class="mt-4">
            <form method="POST" action="{{ route('admin.learning.quizzes.questions.store', $quiz) }}" class="space-y-4">
                @csrf

                <textarea name="question_text" rows="3" required
                          class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5632]/30"
                          placeholder="Question text (supports HTML/LaTeX)..."></textarea>

                <div class="grid gap-2 sm:grid-cols-2">
                    <div class="flex gap-2">
                        <select name="type" x-model="qType"
                                class="flex-1 rounded-xl border border-gray-200 px-3 py-2 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-[#1a5632]/30">
                            <option value="mcq">Multiple Choice (MCQ)</option>
                            <option value="short_answer">Short Answer</option>
                        </select>
                        <input type="number" name="marks" value="1" min="1" required
                               class="w-20 rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5632]/30"
                               placeholder="Marks" title="Marks">
                    </div>
                    <input name="explanation" placeholder="Explanation (optional)"
                           class="rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5632]/30">
                </div>

                {{-- MCQ options --}}
                <div x-show="qType === 'mcq'" class="space-y-2">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-extrabold uppercase tracking-widest text-gray-500">Answer Options</p>
                        <p class="text-xs text-gray-400">Click <span class="font-bold text-emerald-600">✓ Mark Correct</span> on the right answer</p>
                    </div>
                    <template x-for="(opt, i) in options" :key="i">
                        <div class="flex items-center gap-2 rounded-xl border-2 px-3 py-2 transition-all"
                             :class="correctIdx == i
                                 ? 'border-emerald-400 bg-emerald-50'
                                 : 'border-gray-200 bg-white hover:border-gray-300'">
                            <span class="shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-xs font-extrabold"
                                  :class="correctIdx == i ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-500'"
                                  x-text="String.fromCharCode(65 + i)"></span>
                            <input :name="'options[' + i + ']'" x-model="options[i]"
                                   :placeholder="'Option ' + String.fromCharCode(65 + i)"
                                   class="flex-1 bg-transparent text-sm font-semibold focus:outline-none placeholder-gray-300">
                            <button type="button" @click="correctIdx = i"
                                    class="shrink-0 rounded-lg px-2.5 py-1 text-[11px] font-extrabold transition-all"
                                    :class="correctIdx == i
                                        ? 'bg-emerald-500 text-white'
                                        : 'border border-gray-200 text-gray-400 hover:border-emerald-400 hover:text-emerald-600'">
                                <span x-text="correctIdx == i ? '✓ Correct' : 'Mark Correct'"></span>
                            </button>
                        </div>
                    </template>
                    <input type="hidden" name="correct_option" :value="correctIdx">
                    <button type="button" @click="options.push('')"
                            class="text-xs font-bold text-blue-600 hover:text-blue-800">+ Add another option</button>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="open = false"
                            class="rounded-xl border border-gray-200 px-4 py-2 text-xs font-extrabold text-gray-600 hover:bg-gray-50">Cancel</button>
                    <button type="submit"
                            class="rounded-xl bg-[#1a5632] px-5 py-2 text-xs font-extrabold text-white hover:bg-[#0b2415]">
                        Add Question
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
