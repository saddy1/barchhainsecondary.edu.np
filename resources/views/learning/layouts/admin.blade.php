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
        var _katexOpts = {
            delimiters: [
                { left: '$$', right: '$$', display: true  },
                { left: '$',  right: '$',  display: false },
                { left: '\\(', right: '\\)', display: false },
                { left: '\\[', right: '\\]', display: true  }
            ],
            throwOnError: false
        };
        // DOMContentLoaded fires after all defer scripts — renderMathInElement is available by then
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof renderMathInElement === 'function') {
                renderMathInElement(document.body, _katexOpts);
            }
        });
        // Re-render a specific element (used by the live preview button)
        function katexRender(el) {
            if (typeof renderMathInElement === 'function') {
                renderMathInElement(el, _katexOpts);
            }
        }
    </script>

    @php
        $primary      = $siteSettings->get('primary_color', '#1a5632');
        $primaryLight = $siteSettings->get('primary_light_color', '#237042');
        $secondary    = $siteSettings->get('secondary_color', '#e2a024');
        $dark         = $siteSettings->get('dark_color', '#0b2415');
        $sidebarEnd   = $siteSettings->get('sidebar_gradient_end', '#050f09');
    @endphp
    <style>
        :root {
            --theme-primary: {{ $primary }};
            --theme-primary-light: {{ $primaryLight }};
            --theme-secondary: {{ $secondary }};
            --theme-dark: {{ $dark }};
            --theme-sidebar-bg: {{ $dark }};
            --theme-sidebar-gradient-end: {{ $sidebarEnd }};
        }
        .bg-\[\#1a5632\] { background-color: var(--theme-primary) !important; }
        .text-\[\#1a5632\] { color: var(--theme-primary) !important; }
        .border-\[\#1a5632\] { border-color: var(--theme-primary) !important; }
        .ring-\[\#1a5632\] { --tw-ring-color: var(--theme-primary) !important; }
        .focus\:ring-\[\#1a5632\]\/15:focus { --tw-ring-color: color-mix(in srgb, var(--theme-primary) 15%, transparent) !important; }
        main { min-width: 0; }
    </style>
    {{-- Quill rich text editor --}}
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    @stack('styles')
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-50" x-data="{ sidebarOpen: false }">
    <div class="flex h-dvh overflow-hidden">
        @include('learning.partials.sidebar')

        <div class="relative flex flex-col flex-1 min-w-0 overflow-y-auto overflow-x-hidden">
            @include('backend.partials.module-header')

            <div class="px-4 sm:px-6 lg:px-8 pt-4 space-y-2">
                @if(session('success'))
                    <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">{{ session('success') }}</div>
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

    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <script>
    var _quills = {};
    function initQE(editorId, inputId) {
        if (_quills[editorId]) return;
        var el = document.getElementById(editorId);
        var inp = document.getElementById(inputId);
        if (!el || !inp) return;
        var q = new Quill(el, {
            theme: 'snow',
            modules: { toolbar: [
                [{ header: [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ color: [] }, { background: [] }],
                [{ align: [] }],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['blockquote', 'code-block'],
                ['link'],
                ['clean']
            ]}
        });
        if (inp.value && inp.value.trim()) q.clipboard.dangerouslyPasteHTML(inp.value);
        q.on('text-change', function () {
            inp.value = q.root.innerHTML === '<p><br></p>' ? '' : q.root.innerHTML;
        });
        var form = el.closest('form');
        if (form) form.addEventListener('submit', function () {
            inp.value = q.root.innerHTML === '<p><br></p>' ? '' : q.root.innerHTML;
        });
        _quills[editorId] = q;
    }
    function toggleMathPreview(inputId, previewId) {
        var inp = document.getElementById(inputId);
        var prev = document.getElementById(previewId);
        if (!inp || !prev) return;
        var hidden = prev.classList.contains('hidden');
        prev.classList.toggle('hidden', !hidden);
        if (hidden) { prev.innerHTML = inp.value; if (typeof katexRender === 'function') katexRender(prev); }
    }
    </script>
    @stack('scripts')
</body>
</html>
