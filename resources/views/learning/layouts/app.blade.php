<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'E-Learning') | {{ $siteSettings->localized('site_name', 'Barchhain Secondary School') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ $siteSettings->faviconUrl() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    {{-- KaTeX for LaTeX math rendering --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.css" crossorigin="anonymous">
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.js" crossorigin="anonymous"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/contrib/auto-render.min.js" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof renderMathInElement === 'function') {
                renderMathInElement(document.body, {
                    delimiters: [
                        { left: '$$', right: '$$', display: true  },
                        { left: '$',  right: '$',  display: false },
                        { left: '\\(', right: '\\)', display: false },
                        { left: '\\[', right: '\\]', display: true  }
                    ],
                    throwOnError: false
                });
            }
        });
    </script>

    @php
        $primary   = $siteSettings->get('primary_color', '#1a5632');
        $dark      = $siteSettings->get('dark_color', '#0b2415');
        $secondary = $siteSettings->get('secondary_color', '#e2a024');
        $sidebarEnd = $siteSettings->get('sidebar_gradient_end', '#050f09');
    @endphp
    <style>
        :root {
            --theme-primary: {{ $primary }};
            --theme-dark: {{ $dark }};
            --theme-secondary: {{ $secondary }};
            --theme-sidebar-bg: {{ $dark }};
            --theme-sidebar-gradient-end: {{ $sidebarEnd }};
        }
        /* Rich text content in lesson body */
        .lesson-body h1, .lesson-body h2, .lesson-body h3, .lesson-body h4 {
            font-weight: 800; color: #111827; margin-top: 1.5rem; margin-bottom: .5rem; line-height: 1.3;
        }
        .lesson-body h1 { font-size: 1.5rem; }
        .lesson-body h2 { font-size: 1.25rem; }
        .lesson-body h3 { font-size: 1.1rem; }
        .lesson-body p  { color: #374151; line-height: 1.75; margin-bottom: .75rem; }
        .lesson-body ul { list-style: disc; padding-left: 1.5rem; margin-bottom: .75rem; color: #374151; }
        .lesson-body ol { list-style: decimal; padding-left: 1.5rem; margin-bottom: .75rem; color: #374151; }
        .lesson-body li { margin-bottom: .25rem; line-height: 1.6; }
        .lesson-body strong { font-weight: 700; color: #111827; }
        .lesson-body em { font-style: italic; }
        .lesson-body blockquote {
            border-left: 4px solid var(--theme-primary); background: #f0fdf4; padding: .75rem 1rem;
            border-radius: 0 .75rem .75rem 0; margin: 1rem 0; color: #166534; font-style: italic;
        }
        .lesson-body code {
            background: #f3f4f6; border-radius: .25rem; padding: .1rem .35rem;
            font-family: monospace; font-size: .875em; color: #1f2937;
        }
        .lesson-body pre {
            background: #1f2937; color: #f9fafb; border-radius: .75rem;
            padding: 1rem; overflow-x: auto; margin-bottom: .75rem;
        }
        .lesson-body pre code { background: none; color: inherit; padding: 0; }
        .lesson-body hr { border-color: #e5e7eb; margin: 1.5rem 0; }
        .lesson-body a { color: var(--theme-primary); text-decoration: underline; }
        main { min-width: 0; }
    </style>
    @stack('styles')
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-50" x-data="{ sidebarOpen: false }">
<div class="flex h-dvh overflow-hidden">

    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen"
         x-transition.opacity
         @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-gray-900/70 backdrop-blur-sm lg:hidden"
         style="display:none;"></div>

    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           @click.capture="if ($event.target.closest('a')) sidebarOpen = false"
           class="fixed inset-y-0 left-0 z-50 w-60 text-white transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto flex flex-col h-dvh border-r shrink-0"
           style="background: linear-gradient(180deg, var(--theme-sidebar-bg, #0b2415) 0%, var(--theme-sidebar-gradient-end, #050f09) 100%); border-color: rgba(255,255,255,0.08);">

        {{-- Logo --}}
        <div class="flex items-center justify-between h-14 px-4 border-b shrink-0"
             style="border-color: rgba(255,255,255,0.08); background: rgba(0,0,0,0.25);">
            <a href="{{ route('learning.dashboard') }}" class="flex items-center gap-2.5 min-w-0">
                <div class="w-7 h-7 bg-white/90 rounded-lg flex items-center justify-center p-1 shrink-0">
                    <img src="{{ $siteSettings->logoUrl() }}" alt="Logo" class="w-full h-full object-contain">
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-bold text-white leading-none truncate">{{ $siteSettings->get('app_name', 'Barchhain ERP') }}</p>
                    <p class="text-[9px] uppercase tracking-widest font-semibold mt-0.5" style="color: var(--theme-secondary, #e2a024);">E-Learning</p>
                </div>
            </a>
            <button type="button" @click="sidebarOpen = false"
                    class="lg:hidden p-1 text-white/40 hover:text-white rounded-md hover:bg-white/10 transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        @php
            $navActive = fn(string ...$patterns) => collect($patterns)->contains(
                fn ($p) => request()->routeIs($p) || request()->is($p)
            );
        @endphp

        <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-0.5">

            <p class="px-2 pt-1 pb-1.5 text-[10px] font-bold text-white/30 uppercase tracking-widest">My Learning</p>

            <a href="{{ route('learning.dashboard') }}"
               class="group flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm font-medium transition-all
                      {{ $navActive('learning.dashboard') ? 'bg-white/15 text-white' : 'text-white/60 hover:text-white hover:bg-white/8' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span class="flex-1 truncate">My Courses</span>
            </a>

            <a href="{{ route('learning.courses.show', request()->route('course') ?? '__') }}"
               style="{{ $navActive('learning.courses.show', 'learning.lessons.show') ? '' : 'display:none' }}"
               class="group flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm font-medium transition-all
                      {{ $navActive('learning.courses.show', 'learning.lessons.show') ? 'bg-white/15 text-white' : 'text-white/60 hover:text-white hover:bg-white/8' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="flex-1 truncate">{{ request()->route('course')?->title ?? 'Current Course' }}</span>
            </a>

            <p class="px-2 pt-4 pb-1.5 text-[10px] font-bold text-white/30 uppercase tracking-widest">School</p>

            <a href="{{ route('student.dashboard') }}"
               class="group flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm font-medium transition-all text-white/60 hover:text-white hover:bg-white/8">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4v-5h4v5h4a1 1 0 001-1V10"/>
                </svg>
                <span class="flex-1 truncate">Student Portal</span>
            </a>

        </nav>

        {{-- User footer --}}
        <div class="px-3 py-3 border-t shrink-0"
             style="border-color: rgba(255,255,255,0.08); background: rgba(0,0,0,0.3);">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 shrink-0 rounded-full flex items-center justify-center text-white text-xs font-bold border-2"
                     style="background-color: var(--theme-primary, #1a5632); border-color: var(--theme-secondary, #e2a024);">
                    {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-white truncate leading-tight">{{ auth()->user()->name ?? 'Student' }}</p>
                    <p class="text-[10px] text-white/35 truncate leading-tight mt-0.5">
                        {{ auth()->user()->student_code ?? auth()->user()->class_grade ?? 'Student' }}
                    </p>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                    @csrf
                    <button type="submit" title="Logout"
                            class="p-1.5 rounded-md text-white/40 hover:text-white hover:bg-white/10 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main area --}}
    <div class="relative flex flex-col flex-1 min-w-0 overflow-y-auto overflow-x-hidden">

        {{-- Mobile top bar (hamburger) --}}
        <div class="sticky top-0 z-30 flex items-center gap-3 h-14 px-4 bg-white border-b border-gray-200 lg:hidden shrink-0">
            <button type="button" @click="sidebarOpen = true"
                    class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <a href="{{ route('learning.dashboard') }}" class="flex items-center gap-2 min-w-0">
                <span class="w-7 h-7 rounded-lg border border-gray-200 bg-white p-1 shrink-0">
                    <img src="{{ $siteSettings->logoUrl() }}" alt="Logo" class="w-full h-full object-contain">
                </span>
                <span class="text-sm font-extrabold text-gray-900 truncate">E-Learning Portal</span>
            </a>
        </div>

        {{-- Flash messages --}}
        <div class="px-4 sm:px-6 lg:px-8 pt-4 space-y-2">
            @if(session('success'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">{{ session('success') }}</div>
            @endif
            @if(session('status'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">{{ session('status') }}</div>
            @endif
            @if(session('error'))
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{{ session('error') }}</div>
            @endif
        </div>

        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            @yield('content')
        </main>

        <footer class="shrink-0 px-6 py-3 border-t border-gray-100 bg-white">
            <p class="text-xs text-gray-400 text-center">&copy; {{ date('Y') }} {{ $siteSettings->localized('site_name', 'Barchhain Secondary School') }} — E-Learning</p>
        </footer>
    </div>

</div>
@stack('scripts')
</body>
</html>
