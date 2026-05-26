@extends('learning.layouts.app')

@section('title', $lesson->title)

@section('content')
@php
    $isCompleted = in_array($lesson->id, $completedLessonIds);
    $requiresPlayerCompletion = in_array($lesson->type, ['video', 'audio'], true) && ! $isCompleted;
    $resumeSeconds = (int) ($lessonProgress->current_seconds ?? 0);
    $maxWatchedSeconds = (int) ($lessonProgress->max_watched_seconds ?? 0);
@endphp
<div class="grid gap-6 lg:grid-cols-[1fr_320px]">

    {{-- Main content --}}
    <section class="min-w-0 space-y-6">

        {{-- Breadcrumb --}}
        <div>
            <a href="{{ route('learning.courses.show', $course) }}"
               class="inline-flex items-center gap-1 text-sm font-bold text-[#1a5632] hover:underline">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ $course->title }}
            </a>
            <h1 class="mt-2 text-xl sm:text-2xl font-extrabold text-gray-950 leading-tight">{{ $lesson->title }}</h1>
            <div class="mt-1 flex flex-wrap items-center gap-3">
                <span class="capitalize rounded-full bg-gray-100 px-3 py-0.5 text-xs font-extrabold text-gray-500">
                    {{ $lesson->type }}
                </span>
                @if($lesson->is_free)
                    <span class="rounded-full bg-emerald-50 px-3 py-0.5 text-xs font-extrabold text-emerald-700">Free lesson</span>
                @endif
                @if(in_array($lesson->id, $completedLessonIds))
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500 px-3 py-0.5 text-xs font-extrabold text-white">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                        Completed
                    </span>
                @endif
            </div>
        </div>

        {{-- VIDEO --}}
        @if($lesson->type === 'video')
            @if($lesson->embed_url)
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-4 py-3">
                        <div>
                            <p class="text-xs font-extrabold uppercase tracking-widest text-[#1a5632]">School Learning Stream</p>
                            <p class="text-sm font-semibold text-gray-500">This video is delivered through the private learning library.</p>
                        </div>
                        <div class="flex flex-wrap items-center justify-end gap-2">
                            <span id="player-time-left-badge" class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-extrabold text-emerald-700">
                                Time left: --
                            </span>
                            <span id="player-status-badge" class="rounded-full bg-amber-50 px-3 py-1 text-xs font-extrabold text-amber-700">
                                Watch to unlock next
                            </span>
                        </div>
                    </div>
                    <div id="school-video-frame" class="relative aspect-video w-full bg-black">
                        <iframe id="youtube-player"
                            src="{{ $lesson->embed_url }}"
                            class="absolute inset-0 h-full w-full"
                            allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture; fullscreen">
                        </iframe>
                        <div class="absolute inset-0 z-10 cursor-default" id="youtube-click-shield" oncontextmenu="return false" aria-hidden="true"></div>
                        <div class="pointer-events-none absolute inset-x-0 bottom-0 z-20 bg-gradient-to-t from-black/75 to-transparent p-4">
                            <div class="pointer-events-auto flex flex-wrap items-center gap-2">
                                <button type="button" id="youtube-play-button"
                                    class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-extrabold transition-all">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M6.5 5.7v8.6c0 .6.7 1 1.2.6l6.2-4.3c.4-.3.4-.9 0-1.2L7.7 5.1c-.5-.4-1.2 0-1.2.6z"/>
                                    </svg>
                                    Play
                                </button>
                                <button type="button" id="youtube-pause-button"
                                    class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-extrabold transition-all">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M6 5a1 1 0 00-1 1v8a1 1 0 102 0V6a1 1 0 00-1-1zm8 0a1 1 0 00-1 1v8a1 1 0 102 0V6a1 1 0 00-1-1z"/>
                                    </svg>
                                    Pause
                                </button>
                                <button type="button" id="youtube-back-button"
                                    class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-2 text-sm font-extrabold text-white ring-1 ring-white/20 hover:bg-white/25">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M9 14l-4-4 4-4M5 10h8a6 6 0 110 12h-2"/>
                                    </svg>
                                    Back 10s
                                </button>
                                <button type="button" id="youtube-fullscreen-button"
                                    class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-2 text-sm font-extrabold text-white ring-1 ring-white/20 hover:bg-white/25">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M4 8V4h4M20 8V4h-4M4 16v4h4M20 16v4h-4"/>
                                    </svg>
                                    Fullscreen
                                </button>
                                <span class="rounded-full bg-black/40 px-3 py-2 text-xs font-bold text-white/80">
                                    Seeking is disabled for this lesson
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($lesson->stream_url)
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-4 py-3">
                        <div>
                            <p class="text-xs font-extrabold uppercase tracking-widest text-[#1a5632]">Private Video Library</p>
                            <p class="text-sm font-semibold text-gray-500">This file is served from the school's private media directory.</p>
                        </div>
                        <div class="flex flex-wrap items-center justify-end gap-2">
                            <span id="player-time-left-badge" class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-extrabold text-emerald-700">
                                Time left: --
                            </span>
                            <span id="player-status-badge" class="rounded-full bg-amber-50 px-3 py-1 text-xs font-extrabold text-amber-700">
                                Watch to unlock next
                            </span>
                        </div>
                    </div>
                    <video id="native-video-player" controls controlsList="nodownload" class="aspect-video w-full bg-black" preload="metadata">
                        <source src="{{ $lesson->stream_url }}">
                        Your browser does not support video playback.
                    </video>
                    <div class="border-t border-gray-100 bg-gray-50 px-4 py-3">
                        <button type="button" id="native-back-button"
                            class="inline-flex items-center gap-2 rounded-full border border-gray-200 bg-white px-4 py-2 text-sm font-extrabold text-[#1a5632] hover:bg-emerald-50">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M9 14l-4-4 4-4M5 10h8a6 6 0 110 12h-2"/>
                            </svg>
                            Back 10s
                        </button>
                    </div>
                </div>
            @else
                <div class="flex items-center justify-center aspect-video rounded-2xl bg-gray-100 text-gray-400">
                    <p class="text-sm font-semibold">No video URL set.</p>
                </div>
            @endif
        @endif

        {{-- AUDIO --}}
        @if($lesson->type === 'audio')
            @if($lesson->audio_url)
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-purple-100 text-purple-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z"/>
                            </svg>
                        </span>
                        <div>
                            <p class="font-extrabold text-gray-900">{{ $lesson->title }}</p>
                            <p class="text-xs text-gray-400">Audio lesson</p>
                        </div>
                    </div>
                    <audio id="native-audio-player" controls class="w-full" preload="metadata">
                        <source src="{{ $lesson->audio_url }}">
                        Your browser does not support audio playback.
                    </audio>
                    <div class="mt-4">
                        <button type="button" id="native-back-button"
                            class="inline-flex items-center gap-2 rounded-full border border-gray-200 bg-white px-4 py-2 text-sm font-extrabold text-[#1a5632] hover:bg-emerald-50">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M9 14l-4-4 4-4M5 10h8a6 6 0 110 12h-2"/>
                            </svg>
                            Back 10s
                        </button>
                    </div>
                </div>
            @else
                <div class="rounded-2xl border border-gray-200 bg-gray-50 px-5 py-8 text-center text-gray-400">
                    <p class="text-sm font-semibold">No audio file set.</p>
                </div>
            @endif
        @endif

        {{-- TEXT / READING --}}
        @if($lesson->type === 'text' && $lesson->content_body)
            <div class="rounded-2xl border border-gray-200 bg-white p-6 sm:p-8 shadow-sm lesson-body">
                {!! $lesson->content_body !!}
            </div>
        @endif

        {{-- Description (shown for all types) --}}
        @if($lesson->description && $lesson->type !== 'text')
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-2">About this lesson</p>
                <p class="text-gray-700 leading-relaxed">{{ $lesson->description }}</p>
            </div>
        @endif

        {{-- Mark complete button --}}
        @if(! $isCompleted)
            <form method="POST" action="{{ route('learning.lessons.complete', [$course, $lesson->id]) }}" id="complete-lesson-form">
                @csrf
                @if($requiresPlayerCompletion)
                    <input type="hidden" name="player_completed" id="player-completed-input" value="0">
                @endif
                <button id="complete-lesson-button"
                    class="w-full sm:w-auto rounded-xl px-6 py-3 font-extrabold text-white transition-colors {{ $requiresPlayerCompletion ? 'cursor-not-allowed bg-gray-400' : 'bg-[#1a5632] hover:bg-[#0b2415]' }}"
                    @if($requiresPlayerCompletion) disabled @endif>
                    {{ $requiresPlayerCompletion ? 'Finish media to continue' : 'Mark Lesson as Complete' }}
                </button>
                @if($requiresPlayerCompletion)
                    <p id="complete-help-text" class="mt-2 text-sm font-semibold text-gray-500">
                        The next lesson unlocks after this {{ $lesson->type }} finishes.
                        @if($resumeSeconds > 0)
                            Resuming near {{ gmdate('i:s', $resumeSeconds) }}.
                        @endif
                    </p>
                @endif
            </form>
        @else
            {{-- Completed: show next lesson button if available --}}
            @if($nextLesson)
                <div class="flex flex-wrap items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-50 px-4 py-2.5 text-sm font-extrabold text-emerald-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                        Lesson Complete
                    </span>
                    <a href="{{ route('learning.lessons.show', [$course, $nextLesson->id]) }}"
                       class="rounded-xl bg-[#1a5632] px-5 py-2.5 text-sm font-extrabold text-white hover:bg-[#0b2415] transition-colors inline-flex items-center gap-2">
                        Next Lesson
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            @else
                <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-5 py-4 flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-extrabold text-emerald-900">All lessons completed!</p>
                        <p class="text-sm text-emerald-700 mt-0.5">You've finished this course.</p>
                    </div>
                </div>
            @endif
        @endif

        {{-- Quiz section --}}
        @if($lesson->quiz && $lesson->quiz->is_published)
        @php
            $lq = $lesson->quiz;
            $lqAttempt = $lq->attempts()->where('user_id', auth()->id())->latest()->first();
            $lqUsed    = $lq->attempts()->where('user_id', auth()->id())->count();
            $lqCan     = $lqUsed < $lq->max_attempts;
        @endphp
            <div class="rounded-2xl border {{ $lqAttempt?->passed ? 'border-emerald-200 bg-emerald-50' : 'border-amber-200 bg-amber-50' }} p-5 sm:p-6">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-4 h-4 {{ $lqAttempt?->passed ? 'text-emerald-600' : 'text-amber-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                            <p class="font-extrabold {{ $lqAttempt?->passed ? 'text-emerald-900' : 'text-amber-900' }}">
                                Mock Test
                                @if($lqAttempt?->passed) <span class="text-emerald-600">✓ Passed</span> @endif
                            </p>
                        </div>
                        <p class="text-sm font-bold {{ $lqAttempt?->passed ? 'text-emerald-800' : 'text-amber-800' }}">{{ $lq->title }}</p>
                        <div class="mt-1 flex flex-wrap gap-3 text-xs font-semibold {{ $lqAttempt?->passed ? 'text-emerald-700' : 'text-amber-700' }}">
                            <span>{{ $lq->questions->count() }} questions</span>
                            <span>·</span>
                            <span>Pass: {{ $lq->pass_percentage }}%</span>
                            @if($lq->time_limit_minutes) <span>·</span><span>{{ $lq->time_limit_minutes }} min</span> @endif
                            @if($lqAttempt)
                                <span>·</span>
                                <span>Last score: {{ $lqAttempt->percentage() }}% ({{ $lqUsed }}/{{ $lq->max_attempts }} attempts)</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 shrink-0 items-end">
                        @if($lqAttempt)
                            <a href="{{ route('learning.quizzes.result', [$lq, $lqAttempt]) }}"
                               class="rounded-xl border border-current px-3 py-1.5 text-xs font-extrabold {{ $lqAttempt->passed ? 'text-emerald-700' : 'text-amber-700' }} hover:bg-white/50">
                                View Results
                            </a>
                        @endif
                        @if($lqCan && $isCompleted)
                            <a href="{{ route('learning.quizzes.show', $lq) }}"
                               class="rounded-xl bg-amber-500 hover:bg-amber-600 px-4 py-2 text-xs font-extrabold text-white transition-colors">
                                {{ $lqAttempt ? 'Retake Test' : 'Take Test' }}
                            </a>
                        @elseif(!$isCompleted)
                            <span class="rounded-xl bg-amber-100 px-4 py-2 text-xs font-extrabold text-amber-600">
                                Complete lesson first
                            </span>
                        @elseif(!$lqCan)
                            <span class="rounded-xl bg-gray-100 px-4 py-2 text-xs font-extrabold text-gray-500">
                                No attempts left
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endif

    </section>

    {{-- Sidebar: course outline --}}
    <aside class="space-y-4 lg:sticky lg:top-6 lg:self-start">
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="bg-[#1a5632] px-4 py-3">
                <p class="text-xs font-bold uppercase tracking-widest text-white/60">Course Content</p>
                <p class="text-sm font-extrabold text-white mt-0.5 truncate">{{ $course->title }}</p>
            </div>

            @php $chNum = 0; @endphp
            @foreach($course->chapters as $ch)
                @php $chNum++ @endphp
                <div class="border-t border-gray-100" x-data="{open: {{ $ch->lessons->contains('id', $lesson->id) ? 'true' : 'false' }}}">
                    <button type="button" @click="open = !open"
                            class="w-full flex items-center gap-2 px-4 py-2.5 text-left bg-gray-50 hover:bg-gray-100 transition-colors">
                        <span class="shrink-0 flex h-5 w-5 items-center justify-center rounded-full bg-[#1a5632] text-[10px] font-extrabold text-white">
                            {{ $chNum }}
                        </span>
                        <p class="flex-1 text-xs font-extrabold text-gray-700 leading-tight">{{ $ch->title }}</p>
                        <svg class="w-3 h-3 text-gray-400 transition-transform" :class="open ? '' : '-rotate-90'"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open" class="divide-y divide-gray-50">
                        @foreach($ch->lessons as $idx => $sl)
                            @php
                                $slCompleted = in_array($sl->id, $completedLessonIds);
                                $slUnlocked  = in_array($sl->id, $unlockedLessonIds);
                                $slCurrent   = $sl->id === $lesson->id;
                            @endphp
                            @if($slUnlocked)
                                <a href="{{ route('learning.lessons.show', [$course, $sl->id]) }}"
                                   class="flex items-center gap-2.5 px-4 py-2.5 transition-colors
                                          {{ $slCurrent ? 'bg-emerald-50 border-l-2 border-[#1a5632]' : 'hover:bg-gray-50' }}">
                            @else
                                <div class="flex items-center gap-2.5 px-4 py-2.5 opacity-50 cursor-not-allowed">
                            @endif
                                <span class="shrink-0 flex h-5 w-5 items-center justify-center rounded-full
                                    {{ $slCompleted ? 'bg-emerald-500' : ($slCurrent ? 'border-2 border-[#1a5632]' : 'border border-gray-300') }}">
                                    @if($slCompleted)
                                        <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @elseif(!$slUnlocked)
                                        <svg class="w-2.5 h-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    @endif
                                </span>
                                <p class="text-xs font-semibold leading-tight truncate
                                    {{ $slCurrent ? 'text-[#1a5632] font-extrabold' : ($slCompleted ? 'text-gray-400 line-through' : 'text-gray-700') }}">
                                    {{ $chNum }}.{{ $idx + 1 }}. {{ $sl->title }}
                                </p>
                            @if($slUnlocked)
                                </a>
                            @else
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </aside>

</div>
@endsection

@push('scripts')
@if($requiresPlayerCompletion)
<script>
    const mediaProgress = {
        endpoint: @json(route('learning.lessons.progress', [$course, $lesson->id])),
        token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        resumeSeconds: {{ $resumeSeconds }},
        maxWatchedSeconds: {{ $maxWatchedSeconds }},
        lastSavedAt: 0,
    };

    function saveMediaProgress(currentSeconds, durationSeconds = 0, force = false) {
        const current = Math.max(0, Math.floor(Number(currentSeconds) || 0));
        const duration = Math.max(0, Math.floor(Number(durationSeconds) || 0));
        const now = Date.now();

        mediaProgress.maxWatchedSeconds = Math.max(mediaProgress.maxWatchedSeconds, current);

        if (!force && now - mediaProgress.lastSavedAt < 5000) {
            return;
        }

        mediaProgress.lastSavedAt = now;

        fetch(mediaProgress.endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': mediaProgress.token,
            },
            body: JSON.stringify({
                current_seconds: current,
                duration_seconds: duration,
            }),
            keepalive: force,
        }).catch(() => {});
    }

    function formatMediaTime(seconds) {
        const total = Math.max(0, Math.ceil(Number(seconds) || 0));
        const hours = Math.floor(total / 3600);
        const minutes = Math.floor((total % 3600) / 60);
        const secs = total % 60;

        if (hours > 0) {
            return `${hours}:${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        }

        return `${minutes}:${String(secs).padStart(2, '0')}`;
    }

    function updateTimeLeftBadge(currentSeconds, durationSeconds) {
        const badge = document.getElementById('player-time-left-badge');
        const duration = Number(durationSeconds) || 0;

        if (!badge || duration <= 0) {
            return;
        }

        badge.textContent = `Time left: ${formatMediaTime(duration - currentSeconds)}`;
    }

    function setYoutubeControlState(state = 'paused') {
        const playButton = document.getElementById('youtube-play-button');
        const pauseButton = document.getElementById('youtube-pause-button');
        const active = 'bg-white text-[#1a5632] shadow-sm hover:bg-emerald-50';
        const inactive = 'bg-white/15 text-white ring-1 ring-white/20 hover:bg-white/25';

        if (!playButton || !pauseButton) {
            return;
        }

        playButton.className = `inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-extrabold transition-all ${state === 'playing' ? inactive : active}`;
        pauseButton.className = `inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-extrabold transition-all ${state === 'playing' ? active : inactive}`;
        playButton.setAttribute('aria-pressed', state === 'playing' ? 'false' : 'true');
        pauseButton.setAttribute('aria-pressed', state === 'playing' ? 'true' : 'false');
    }

    function unlockLessonCompletion() {
        const button = document.getElementById('complete-lesson-button');
        const input = document.getElementById('player-completed-input');
        const badge = document.getElementById('player-status-badge');
        const help = document.getElementById('complete-help-text');

        if (input) input.value = '1';
        if (button) {
            button.disabled = false;
            button.textContent = 'Mark Lesson as Complete';
            button.classList.remove('cursor-not-allowed', 'bg-gray-400');
            button.classList.add('bg-[#1a5632]', 'hover:bg-[#0b2415]');
        }
        if (badge) {
            badge.textContent = 'Completed';
            badge.className = 'rounded-full bg-emerald-50 px-3 py-1 text-xs font-extrabold text-emerald-700';
        }
        const timeLeftBadge = document.getElementById('player-time-left-badge');
        if (timeLeftBadge) {
            timeLeftBadge.textContent = 'Time left: 0:00';
        }
        if (help) help.textContent = 'Media finished. You can now complete this lesson and continue.';
    }

    function bindNativeMediaProgress(player) {
        if (!player) {
            return;
        }

        let nativeMaxTime = mediaProgress.maxWatchedSeconds;
        document.getElementById('native-back-button')?.addEventListener('click', () => {
            player.currentTime = Math.max(0, player.currentTime - 10);
            saveMediaProgress(player.currentTime, player.duration, true);
            updateTimeLeftBadge(player.currentTime, player.duration);
        });

        player.addEventListener('loadedmetadata', () => {
            if (mediaProgress.resumeSeconds > 0 && mediaProgress.resumeSeconds < player.duration - 3) {
                player.currentTime = mediaProgress.resumeSeconds;
            }

            updateTimeLeftBadge(player.currentTime, player.duration);
        });

        player.addEventListener('timeupdate', () => {
            if (player.currentTime > nativeMaxTime + 8 && player.currentTime < player.duration - 3) {
                player.currentTime = nativeMaxTime;
                return;
            }

            nativeMaxTime = Math.max(nativeMaxTime, player.currentTime);
            updateTimeLeftBadge(player.currentTime, player.duration);
            saveMediaProgress(player.currentTime, player.duration);
        });

        player.addEventListener('seeked', () => {
            if (player.currentTime > nativeMaxTime + 8 && player.currentTime < player.duration - 3) {
                player.currentTime = nativeMaxTime;
            }
        });

        player.addEventListener('pause', () => saveMediaProgress(player.currentTime, player.duration, true));
        window.addEventListener('beforeunload', () => saveMediaProgress(player.currentTime, player.duration, true));
        player.addEventListener('ended', () => {
            saveMediaProgress(player.duration || player.currentTime, player.duration, true);
            unlockLessonCompletion();
        });
    }

    bindNativeMediaProgress(document.getElementById('native-video-player'));
    bindNativeMediaProgress(document.getElementById('native-audio-player'));

    @if($lesson->embed_url)
        let youtubeLessonPlayer;
        let youtubeCompleted = false;
        let youtubeMaxTime = mediaProgress.maxWatchedSeconds;

        window.onYouTubeIframeAPIReady = function () {
            youtubeLessonPlayer = new YT.Player('youtube-player', {
                events: {
                    onReady() {
                        if (mediaProgress.resumeSeconds > 0) {
                            youtubeLessonPlayer.seekTo(mediaProgress.resumeSeconds, true);
                            youtubeMaxTime = Math.max(youtubeMaxTime, mediaProgress.resumeSeconds);
                        }

                        setYoutubeControlState('paused');
                        document.getElementById('youtube-play-button')?.addEventListener('click', () => {
                            youtubeLessonPlayer.playVideo();
                            setYoutubeControlState('playing');
                        });
                        document.getElementById('youtube-pause-button')?.addEventListener('click', () => {
                            youtubeLessonPlayer.pauseVideo();
                            setYoutubeControlState('paused');
                        });
                        document.getElementById('youtube-back-button')?.addEventListener('click', () => {
                            const duration = youtubeLessonPlayer.getDuration?.() || 0;
                            const targetTime = Math.max(0, youtubeLessonPlayer.getCurrentTime() - 10);
                            youtubeLessonPlayer.seekTo(targetTime, true);
                            saveMediaProgress(targetTime, duration, true);
                            updateTimeLeftBadge(targetTime, duration);
                        });
                        document.getElementById('youtube-fullscreen-button')?.addEventListener('click', () => {
                            const frame = document.getElementById('school-video-frame');

                            if (!frame) {
                                return;
                            }

                            if (document.fullscreenElement) {
                                document.exitFullscreen?.();
                                return;
                            }

                            frame.requestFullscreen?.();
                        });

                        window.setInterval(() => {
                            if (!youtubeLessonPlayer || youtubeCompleted || typeof youtubeLessonPlayer.getCurrentTime !== 'function') {
                                return;
                            }

                            const currentTime = youtubeLessonPlayer.getCurrentTime();
                            const duration = youtubeLessonPlayer.getDuration ? youtubeLessonPlayer.getDuration() : 0;

                            if (currentTime > youtubeMaxTime + 8 && duration && currentTime < duration - 3) {
                                youtubeLessonPlayer.seekTo(youtubeMaxTime, true);
                                youtubeLessonPlayer.playVideo();
                                return;
                            }

                            youtubeMaxTime = Math.max(youtubeMaxTime, currentTime);
                            updateTimeLeftBadge(currentTime, duration);
                            saveMediaProgress(currentTime, duration);
                        }, 1000);

                        window.addEventListener('beforeunload', () => {
                            if (!youtubeLessonPlayer || typeof youtubeLessonPlayer.getCurrentTime !== 'function') {
                                return;
                            }

                            saveMediaProgress(youtubeLessonPlayer.getCurrentTime(), youtubeLessonPlayer.getDuration?.() || 0, true);
                        });
                    },
                    onStateChange(event) {
                        if (event.data === YT.PlayerState.PLAYING) {
                            setYoutubeControlState('playing');
                        }

                        if (event.data === YT.PlayerState.PAUSED || event.data === YT.PlayerState.CUED) {
                            setYoutubeControlState('paused');
                        }

                        if (event.data === YT.PlayerState.ENDED) {
                            youtubeCompleted = true;
                            setYoutubeControlState('paused');
                            saveMediaProgress(
                                youtubeLessonPlayer.getDuration?.() || youtubeLessonPlayer.getCurrentTime?.() || 0,
                                youtubeLessonPlayer.getDuration?.() || 0,
                                true
                            );
                            unlockLessonCompletion();
                        }
                    }
                }
            });
        };

        if (!document.querySelector('script[src="https://www.youtube.com/iframe_api"]')) {
            const tag = document.createElement('script');
            tag.src = 'https://www.youtube.com/iframe_api';
            document.head.appendChild(tag);
        }
    @endif
</script>
@endif
@endpush
