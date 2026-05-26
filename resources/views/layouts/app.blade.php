<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Fetch Dynamic SEO from Database --}}
    @php
        // 1. Get the page identifier set in the child view, fallback to route name
        $seoPageName = View::getSection('seo_page_name') ?? Route::currentRouteName();
        
        // 2. Query the database
        $seo = \App\Models\SeoSetting::where('page_name', $seoPageName)->first();
        
        // 3. Define absolute fallbacks
        $defaultTitle = __('site.seo.default_title');
        $defaultDesc = __('site.seo.default_desc');
        $defaultKeywords = __('site.seo.default_keywords');

        // 4. THE LOGIC: DB wins -> then @section() -> then $default
        $finalTitle = $seo->meta_title ?? View::getSection('title') ?? $defaultTitle;
        $finalDesc = $seo->meta_description ?? View::getSection('meta_description') ?? $defaultDesc;
        $finalKeywords = $seo->meta_keywords ?? View::getSection('meta_keywords') ?? $defaultKeywords;
        $schoolName = $siteSettings->localized('site_name', __('site.school_name'));
        $schoolAddress = $siteSettings->localized('site_address', __('site.location'));
        $logoUrl = $siteSettings->logoUrl();
    @endphp

    {{-- Primary SEO Meta Tags --}}
    <title>{{ $finalTitle }}</title>
    <meta name="description" content="{{ $finalDesc }}">
    <meta name="keywords" content="{{ $finalKeywords }}">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="author" content="{{ $schoolName }}">
    <meta name="geo.region" content="NP-P7">
    <meta name="geo.placename" content="{{ $schoolAddress }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="sitemap" type="application/xml" title="Sitemap" href="{{ url('/sitemap.xml') }}">

    {{-- Open Graph / Social Media Meta Tags --}}
    <meta property="og:title" content="{{ $finalTitle }}">
    <meta property="og:description" content="{{ $finalDesc }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:image" content="@yield('og_image', $logoUrl)">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="{{ $schoolName }}">
    <meta property="og:locale" content="en_US">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $finalTitle }}">
    <meta name="twitter:description" content="{{ $finalDesc }}">
    <meta name="twitter:image" content="@yield('og_image', $logoUrl)">

    {{-- Organization + WebSite Structured Data (JSON-LD) --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@graph": [
            {
                "@type": "EducationalOrganization",
                "@id": "{{ url('/') }}/#organization",
                "name": @json($schoolName),
                "alternateName": "Barchhain Secondary School Doti",
                "url": "{{ url('/') }}",
                "logo": {
                    "@type": "ImageObject",
                    "url": "{{ $logoUrl }}",
                    "width": 200,
                    "height": 200
                },
                "description": @json(__('site.seo.default_desc')),
                "foundingDate": "2005",
                "address": {
                    "@type": "PostalAddress",
                    "streetAddress": "Barchhain",
                    "addressLocality": "Barchhain",
                    "addressRegion": "Doti",
                    "addressCountry": "NP"
                },
                "email": "{{ $siteSettings->get('school_email') }}",
                "sameAs": [
                    "{{ url('/') }}"
                ],
                "hasMap": "https://maps.google.com/?q=Barchhain+Secondary+School+Doti",
                "openingHours": "Su-Fr 10:00-16:00",
                "areaServed": {
                    "@type": "AdministrativeArea",
                    "name": "Doti"
                }
            },
            {
                "@type": "WebSite",
                "@id": "{{ url('/') }}/#website",
                "url": "{{ url('/') }}",
                "name": @json($schoolName),
                "description": @json(__('site.seo.default_desc')),
                "publisher": {
                    "@id": "{{ url('/') }}/#organization"
                },
                "potentialAction": {
                    "@type": "SearchAction",
                    "target": {
                        "@type": "EntryPoint",
                        "urlTemplate": "{{ url('/news') }}?q={search_term_string}"
                    },
                    "query-input": "required name=search_term_string"
                },
                "inLanguage": "{{ app()->getLocale() === 'ne' ? 'ne-NP' : 'en-US' }}"
            },
            {
                "@type": "WebPage",
                "@id": "{{ url()->current() }}/#webpage",
                "url": "{{ url()->current() }}",
                "name": "{{ $finalTitle }}",
                "description": "{{ $finalDesc }}",
                "isPartOf": { "@id": "{{ url('/') }}/#website" },
                "publisher": { "@id": "{{ url('/') }}/#organization" },
                "inLanguage": "{{ app()->getLocale() === 'ne' ? 'ne-NP' : 'en-US' }}",
                "dateModified": "{{ now()->toIso8601String() }}"
            }
        ]
    }
    </script>

    {{-- Dynamic Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ $siteSettings->faviconUrl() }}">
    <link rel="shortcut icon" href="{{ $siteSettings->faviconUrl() }}">
    <link rel="apple-touch-icon" href="{{ $siteSettings->faviconUrl() }}">

    {{-- Fonts (Global variables for Playfair Display and DM Sans) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,700;1,400&family=Inter:wght@400;500;600;700;800&family=Lora:wght@500;600;700&family=Merriweather:wght@700;900&family=Noto+Sans+Devanagari:wght@400;500;600;700;800&family=Playfair+Display:wght@700;900&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @php
        $themePrimary       = $siteSettings->get('primary_color',         '#1a5632');
        $themeSecondary     = $siteSettings->get('secondary_color',       '#e2a024');
        $themeDark          = $siteSettings->get('dark_color',            '#0b2415');
        $themePrimaryLight  = $siteSettings->get('primary_light_color',   '#237042');
        $themeBodyBg        = $siteSettings->get('body_bg_color',         '#fdfbf7');
        $themeBodyGradEnd   = $siteSettings->get('body_bg_gradient_end',  '#f4f5f0');
        $themeHeaderGradEnd = $siteSettings->get('header_gradient_end',   '#0f3d22');
        $themeFooterGradEnd = $siteSettings->get('footer_gradient_end',   '#0b2415');
        $themeNoticeBg      = $siteSettings->get('notice_bg_color',       '') ?: $themePrimary;
        $themeNoticeAccent  = $siteSettings->get('notice_accent_color',   '') ?: $themeSecondary;
        $themeSidebarEnd    = $siteSettings->get('sidebar_gradient_end',  '#050f09');
        $bodyGradient = ($themeBodyBg !== $themeBodyGradEnd)
            ? "linear-gradient(180deg, {$themeBodyBg} 0%, {$themeBodyGradEnd} 100%)"
            : 'none';
    @endphp
    <style>
        :root {
            --ff-head:                   {!! $siteSettings->fontFamily('heading_font', "'Playfair Display', Georgia, serif") !!};
            --ff-body:                   {!! $siteSettings->fontFamily('body_font', "'DM Sans', sans-serif") !!};
            --theme-primary:             {{ $themePrimary }};
            --theme-primary-light:       {{ $themePrimaryLight }};
            --theme-secondary:           {{ $themeSecondary }};
            --theme-secondary-light:     #f4b63e;
            --theme-dark:                {{ $themeDark }};
            --theme-body-bg:             {{ $themeBodyBg }};
            --theme-body-gradient:       {{ $bodyGradient }};
            --theme-header-gradient-end: {{ $themeHeaderGradEnd }};
            --theme-footer-gradient-end: {{ $themeFooterGradEnd }};
            --theme-notice-bg:           {{ $themeNoticeBg }};
            --theme-notice-accent:       {{ $themeNoticeAccent }};
            --theme-notice-gradient:     linear-gradient(90deg, {{ $themeNoticeBg }} 0%, {{ $themeHeaderGradEnd }} 100%);
            --theme-footer-gradient:     linear-gradient(135deg, {{ $themePrimary }} 0%, {{ $themeFooterGradEnd }} 100%);
            --theme-sidebar-bg:          {{ $themeDark }};
            --theme-sidebar-gradient-end:{{ $themeSidebarEnd }};
        }
        html, body { font-family: var(--ff-body); }
        body {
            background-color: var(--theme-body-bg);
            background-image: var(--theme-body-gradient);
            background-attachment: fixed;
        }

        /* ── Semantic theme colour helpers ── */
        .theme-bg-primary        { background-color: var(--theme-primary)   !important; }
        .theme-bg-secondary      { background-color: var(--theme-secondary) !important; }
        .theme-bg-dark           { background-color: var(--theme-dark)      !important; }
        .theme-text-primary      { color: var(--theme-primary)              !important; }
        .theme-text-secondary    { color: var(--theme-secondary)            !important; }
        .theme-border-primary    { border-color: var(--theme-primary)       !important; }
        .theme-border-secondary  { border-color: var(--theme-secondary)     !important; }

        /* ── Gradient helpers ── */
        .theme-gradient-primary  { background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-primary-light) 100%) !important; }
        .theme-gradient-header   { background: var(--theme-notice-gradient)  !important; }
        .theme-gradient-footer   { background: var(--theme-footer-gradient)  !important; }
        .theme-gradient-accent   { background: linear-gradient(135deg, var(--theme-secondary) 0%, var(--theme-secondary-light) 100%) !important; }
        .theme-gradient-sidebar  { background: linear-gradient(180deg, var(--theme-sidebar-bg) 0%, var(--theme-sidebar-gradient-end) 100%) !important; }

        /* ── Hardcoded colour overrides so admin settings always win ── */
        .bg-\[\#1a5632\]               { background-color: var(--theme-primary)   !important; }
        .bg-\[\#e2a024\]               { background-color: var(--theme-secondary) !important; }
        .bg-\[\#0b2415\]               { background-color: var(--theme-dark)      !important; }
        .text-\[\#1a5632\]             { color: var(--theme-primary)              !important; }
        .text-\[\#e2a024\]             { color: var(--theme-secondary)            !important; }
        .text-\[\#0b2415\]             { color: var(--theme-dark)                 !important; }
        .border-\[\#1a5632\]           { border-color: var(--theme-primary)       !important; }
        .border-\[\#e2a024\]           { border-color: var(--theme-secondary)     !important; }
        .border-\[\#0b2415\]           { border-color: var(--theme-dark)          !important; }
        .hover\:bg-\[\#1a5632\]:hover  { background-color: var(--theme-primary)   !important; }
        .hover\:bg-\[\#e2a024\]:hover  { background-color: var(--theme-secondary) !important; }
        .hover\:bg-\[\#0b2415\]:hover  { background-color: var(--theme-dark)      !important; }
        .hover\:text-\[\#1a5632\]:hover{ color: var(--theme-primary)              !important; }
        .hover\:text-\[\#e2a024\]:hover{ color: var(--theme-secondary)            !important; }

        /* ── Mobile ── */
        @media (max-width: 640px) { html { font-size: 15px; } }

        /* ── Selection ── */
        ::selection      { background-color: var(--theme-primary); color: #fff; }
        ::-moz-selection { background-color: var(--theme-primary); color: #fff; }
    </style>

    {{-- Tailwind & Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Alpine.js (Loaded Once for Navbars/Galleries) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    {{-- Optional Page-Specific Schema markup or styles --}}
    @yield('schema')
    @stack('styles')
</head>

{{-- Added overflow-x-hidden and w-full to prevent horizontal scrolling bugs --}}
<body class="flex flex-col min-h-dvh overflow-x-hidden w-full relative">
    
    {{-- Page Loader --}}
    @include('components.loader')
    
    {{-- Main Navigation --}}
    @include('partials.header')

    {{-- Main Content Injection (Removed pt-[112px] because header is sticky) --}}
    <main class="flex-grow w-full flex flex-col relative z-0 mt-10">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('partials.footer')

    {{-- Body-level modals (rendered outside <main> so fixed z-index works above header) --}}
    @stack('modals')

    {{-- Page-Specific Scripts --}}
    @stack('scripts')
</body>
</html>
