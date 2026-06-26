@php
    use App\Services\ModuleService;
    $admissionsEnabled = ModuleService::enabled('admissions');
    $learningEnabled   = ModuleService::enabled('learning');
    $cardEnabled       = ModuleService::enabled('card');

    $schoolName     = $siteSettings->localized('site_name', __('site.school_name'));
    $schoolAddress  = $siteSettings->localized('site_address', __('site.location'));
    $heroImage      = $siteSettings->imageUrl('home_hero_image', 'assets/image/default-placeholder.jpg');
    $principalImage = $siteSettings->imageUrl('home_principal_image', 'assets/image/default-placeholder.jpg');
    $primary        = $siteSettings->get('primary_color', '#1a5632');
    $dark           = $siteSettings->get('dark_color', '#0b2415');
    $accent         = $siteSettings->get('secondary_color', '#e2a024');

    $studentsCount  = number_format((int) ($homeStats['students'] ?? 470));
    $teachersCount  = number_format((int) ($homeStats['teachers'] ?? 25));
    $established    = $siteSettings->get('established_year', '2036 BS');
    $iemisUrl       = 'http://iemis.cehrd.gov.np/login';

    $quickLinks       = collect($quickLinks ?? []);
    $learningPathways = collect($learningPathways ?? []);
    $homeBanners      = collect($homeBanners ?? []);
    $keyPersons       = collect($keyPersons ?? []);
    $bannerCount      = max($homeBanners->count(), 1);
@endphp

<style>
/* ── CSS custom properties ─────────────────────────────── */
:root {
    --hp-primary: {{ $primary }};
    --hp-dark:    {{ $dark }};
    --hp-accent:  {{ $accent }};
}

/* ── Keyframe animations ───────────────────────────────── */
@keyframes hp-fadeUp    { from { opacity:0; transform:translateY(22px); } to { opacity:1; transform:translateY(0); } }
@keyframes hp-fadeIn    { from { opacity:0; }                             to { opacity:1; } }
@keyframes hp-dotPulse  { 0%,100% { transform:scale(1); opacity:.6; } 50% { transform:scale(1.4); opacity:1; } }
@keyframes hp-shimmer   { 0% { background-position:200% center; } 100% { background-position:-200% center; } }
@keyframes hp-float     { 0%,100% { transform:translateY(0); } 50% { transform:translateY(-7px); } }
@keyframes hp-barGrow   { from { width:0; } to { width:var(--w, 100%); } }
@keyframes hp-countIn   { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }

/* ── Scroll-triggered reveal ───────────────────────────── */
.hp-reveal        { opacity:0; transform:translateY(20px); transition: opacity .6s ease, transform .6s ease; }
.hp-reveal-l      { opacity:0; transform:translateX(-20px); transition: opacity .6s ease, transform .6s ease; }
.hp-reveal-r      { opacity:0; transform:translateX(20px);  transition: opacity .6s ease, transform .6s ease; }
.hp-reveal.hp-in, .hp-reveal-l.hp-in, .hp-reveal-r.hp-in { opacity:1; transform:none; }
.hp-delay-1 { transition-delay:.08s; }
.hp-delay-2 { transition-delay:.16s; }
.hp-delay-3 { transition-delay:.24s; }
.hp-delay-4 { transition-delay:.32s; }
.hp-delay-5 { transition-delay:.40s; }
.hp-delay-6 { transition-delay:.48s; }

/* ── Hero ──────────────────────────────────────────────── */
.hp-hero { min-height: min(92svh, 680px); }
@media (max-width:767px) { .hp-hero { min-height: min(100svh, 600px); } }
[x-cloak] { display:none !important; }
.hp-slide-text {
    animation: hp-fadeUp .65s ease both;
}
.hp-principal-corner {
    position: absolute;
    right: 0;
    bottom: 0;
    z-index: 20;
    width: 100%;
    max-width: 18rem;
    margin: 0;
    border-radius: 1.5rem 0 0 0;
}
.hp-principal-photo {
    height: 11.25rem;
}
@media (min-width:1280px) {
    .hp-principal-corner { max-width: 19.5rem; }
    .hp-principal-photo { height: 13.5rem; }
}

/* ── Quick-link card ───────────────────────────────────── */
.hp-ql {
    position: relative;
    transition: transform .2s ease, background .2s ease;
}
.hp-ql::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    opacity: 0;
    transition: opacity .2s ease;
    background: var(--hp-primary);
}
.hp-ql:active { transform: scale(.95); }
@media (hover:hover) {
    .hp-ql:hover { transform: translateY(-3px); background: #f9fdfb; }
    .hp-ql:hover .hp-ql-icon { background: var(--hp-primary); color: #fff; }
    .hp-ql:hover .hp-ql-label { color: var(--hp-primary); }
}
.hp-ql-icon { transition: background .2s ease, color .2s ease; }
.hp-ql-label { transition: color .2s ease; }

/* ── Stat card ─────────────────────────────────────────── */
.hp-stat { transition: transform .2s ease, box-shadow .2s ease; }
@media (hover:hover) { .hp-stat:hover { transform: translateY(-3px); box-shadow: 0 12px 28px rgba(0,0,0,.18); } }

/* ── Program card (horizontal snap on mobile) ──────────── */
.hp-programs-track {
    display: flex;
    gap: 1rem;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    padding-bottom: 4px;
}
.hp-programs-track::-webkit-scrollbar { display: none; }
.hp-prog-card {
    flex: 0 0 78%;
    scroll-snap-align: start;
    border-radius: 1.25rem;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 4px 20px rgba(0,0,0,.07);
    border: 1px solid rgba(0,0,0,.06);
    transition: transform .3s ease, box-shadow .3s ease;
}
@media (min-width:640px)  { .hp-prog-card { flex: 0 0 44%; } }
@media (min-width:1024px) {
    .hp-programs-track { display: grid; grid-template-columns: repeat(4,1fr); overflow-x: visible; }
    .hp-prog-card { flex: unset; }
}
.hp-prog-card img { transition: transform .5s ease; }
@media (hover:hover) {
    .hp-prog-card:hover { transform: translateY(-5px); box-shadow: 0 20px 44px rgba(0,0,0,.14); }
    .hp-prog-card:hover img { transform: scale(1.06); }
}

/* ── Notice list item ──────────────────────────────────── */
.hp-notice-row { transition: background .15s ease, padding-left .15s ease; border-radius:.75rem; }
@media (hover:hover) { .hp-notice-row:hover { background: #f4f7f5; padding-left: 1.1rem; } }

/* ── Gallery tile ──────────────────────────────────────── */
.hp-gal-tile img { transition: transform .45s ease, opacity .3s ease; }
@media (hover:hover) { .hp-gal-tile:hover img { transform: scale(1.07); opacity:.92; } }

/* ── CTA button shimmer ────────────────────────────────── */
.hp-btn-primary {
    background: var(--hp-primary);
    transition: filter .2s ease, transform .15s ease;
}
@media (hover:hover) { .hp-btn-primary:hover { filter: brightness(1.12); transform: translateY(-1px); } }
.hp-btn-primary:active { transform: scale(.96); }
.hp-btn-ghost {
    border: 1.5px solid rgba(255,255,255,.28);
    background: rgba(255,255,255,.1);
    backdrop-filter: blur(6px);
    transition: background .2s ease, transform .15s ease;
}
@media (hover:hover) { .hp-btn-ghost:hover { background: rgba(255,255,255,.22); } }
.hp-btn-ghost:active { transform: scale(.96); }
</style>

<main style="background:#f7faf8; font-family:var(--ff-body); color:#111827;">

{{-- ══════════════════════════════════════════════════════
     § 1  HERO BANNER SLIDER
══════════════════════════════════════════════════════ --}}
<section class="hp-hero relative overflow-hidden"
         style="background:{{ $dark }}"
         x-data="{ active:0, total:{{ $bannerCount }}, paused:false }"
         x-init="if(total>1) setInterval(()=>{ if(!paused) active=(active+1)%total },3250)">

    {{-- Background image slides --}}
    @foreach($homeBanners as $i => $banner)
    <div class="absolute inset-0"
         x-show="active==={{ $i }}"
         x-transition:enter="transition-opacity duration-700 ease"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         @if($i > 0) x-cloak @endif>
        <img src="{{ $banner->image_url }}" alt="{{ $banner->localizedTitle() }}"
             class="h-full w-full object-cover object-center">
        {{-- Layered gradient for depth + text legibility --}}
        <div class="absolute inset-0" style="background:linear-gradient(120deg,{{ $dark }}f0 0%,{{ $dark }}c0 45%,{{ $dark }}55 75%,{{ $dark }}20 100%)"></div>
        <div class="absolute inset-0" style="background:linear-gradient(to top,{{ $dark }}bb 0%,transparent 55%)"></div>
    </div>
    @endforeach

    {{-- Decorative glowing orbs --}}
    <div class="pointer-events-none absolute -top-16 -right-16 h-64 w-64 rounded-full blur-3xl opacity-15" style="background:{{ $accent }}"></div>
    <div class="pointer-events-none absolute bottom-0 left-0 h-80 w-80 rounded-full blur-3xl opacity-8" style="background:{{ $primary }}"></div>

    {{-- Content grid --}}
    <div class="relative z-10 mx-auto max-w-7xl px-5 sm:px-6 lg:px-8 h-full" style="min-height:inherit;">
        <div class="flex flex-col lg:grid lg:grid-cols-[1fr_300px] xl:grid-cols-[1fr_320px] gap-6 h-full" style="min-height:inherit;">

            {{-- ── Text column ── --}}
            <div class="relative flex flex-col justify-center py-20 lg:py-24">
                <div class="relative" style="min-height:380px;">
                @foreach($homeBanners as $i => $banner)
                @php
                    $bEyebrow  = $banner->localizedEyebrow();
                    $bTitle    = $banner->localizedTitle();
                    $bSubtitle = $banner->localizedSubtitle();
                    $bPrimary  = $banner->localizedPrimaryLabel();
                    $bSec      = $banner->localizedSecondaryLabel();
                    $pExt      = $banner->primary_url   && str_starts_with($banner->primary_url,   'http');
                    $sExt      = $banner->secondary_url && str_starts_with($banner->secondary_url, 'http');
                    $center    = $banner->text_position === 'center';
                @endphp
                <div class="absolute inset-0 flex flex-col justify-center {{ $center ? 'text-center items-center' : '' }}"
                     x-show="active==={{ $i }}"
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 translate-y-5"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @if($i > 0) x-cloak @endif>

                    {{-- Eyebrow tag --}}
                    @if($bEyebrow)
                    <div class="inline-flex w-fit items-center gap-2.5 rounded-full border border-white/20 bg-white/10 backdrop-blur-sm px-4 py-2 mb-5">
                        <span class="h-2 w-2 rounded-full" style="background:{{ $accent }};animation:hp-dotPulse 2s ease infinite;"></span>
                        <span class="text-xs font-extrabold uppercase tracking-[.16em] text-white">{{ $bEyebrow }}</span>
                    </div>
                    @endif

                    {{-- Main title --}}
                    <h1 class="font-black text-white leading-[1.07] tracking-tight mb-5 {{ $center ? 'mx-auto' : '' }}"
                        style="font-family:var(--ff-head);font-size:clamp(1.9rem,5.5vw,3.9rem);max-width:24ch;">
                        {{ $bTitle }}
                    </h1>

                    {{-- Subtitle --}}
                    @if($bSubtitle)
                    <p class="text-white/80 leading-7 mb-8 {{ $center ? 'mx-auto' : '' }}"
                       style="font-size:clamp(.92rem,2vw,1.07rem);max-width:54ch;">
                        {{ $bSubtitle }}
                    </p>
                    @endif

                    {{-- CTA buttons --}}
                    <div class="flex flex-wrap gap-3 {{ $center ? 'justify-center' : '' }}">
                        @if($bPrimary && $banner->primary_url)
                        <a href="{{ $banner->primary_url }}"
                           @if($pExt) target="_blank" rel="noopener" @endif
                           class="hp-btn-primary inline-flex items-center gap-2 rounded-xl px-6 py-3.5 text-sm font-extrabold text-white shadow-xl shadow-black/20">
                            {{ $bPrimary }}
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                        @endif
                        @if($bSec && $banner->secondary_url)
                        <a href="{{ $banner->secondary_url }}"
                           @if($sExt) target="_blank" rel="noopener" @endif
                           class="hp-btn-ghost inline-flex items-center gap-2 rounded-xl px-6 py-3.5 text-sm font-extrabold text-white">
                            {{ $bSec }}
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
                </div>

                {{-- Slide navigation --}}
                @if($homeBanners->count() > 1)
                <div class="mt-6 flex items-center gap-3">
                    <button @click="active=(active-1+total)%total;paused=true"
                            class="flex h-9 w-9 items-center justify-center rounded-full border border-white/25 bg-white/10 text-white backdrop-blur-sm transition hover:bg-white/25 active:scale-90">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <div class="flex gap-2 items-center">
                        @foreach($homeBanners as $i => $_)
                        <button @click="active={{ $i }};paused=true"
                                :class="active==={{ $i }} ? 'w-7 h-2.5 rounded-full' : 'w-2.5 h-2.5 rounded-full bg-white/35 hover:bg-white/60'"
                                :style="active==={{ $i }} ? 'background:{{ $accent }}' : ''"
                                class="transition-all duration-300 rounded-full"></button>
                        @endforeach
                    </div>
                    <button @click="active=(active+1)%total;paused=true"
                            class="flex h-9 w-9 items-center justify-center rounded-full border border-white/25 bg-white/10 text-white backdrop-blur-sm transition hover:bg-white/25 active:scale-90">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
                @endif
            </div>

        </div>
    </div>

    {{-- ── Principal card (desktop bottom-right) ── --}}
    <aside class="hp-principal-corner hidden lg:flex flex-col items-center bg-gradient-to-b from-white to-[#f0f7f3] p-4 shadow-2xl ring-1 ring-white/80">
        <p class="text-[9px] font-black uppercase tracking-[.2em] mb-2.5" style="color:{{ $primary }}">
            {{ __('site.home.portal.head_teacher') }}
        </p>
        <div class="w-full overflow-hidden rounded-xl bg-gray-100 mb-3 ring-2" style="ring-color:{{ $primary }}22;">
            <img src="{{ $principalImage }}" alt="{{ $siteSettings->get('principal_name') }}"
                 class="hp-principal-photo w-full object-cover object-top">
        </div>
        <h2 class="text-xs font-black text-center" style="color:{{ $primary }}">{{ $siteSettings->get('principal_name') }}</h2>
        <p class="text-[10px] font-semibold text-gray-500 text-center mb-2">{{ $siteSettings->localized('principal_role', __('site.home.principal_role')) }}</p>
        <p class="line-clamp-3 text-center text-[11px] leading-4 text-gray-600 mb-3">"{{ $siteSettings->localized('principal_quote', __('site.home.principal_quote')) }}"</p>
        <a href="#principal-message"
           class="w-full inline-flex items-center justify-center gap-2 rounded-xl py-2 text-xs font-extrabold text-white shadow-md"
           style="background:{{ $primary }}">
            {{ __('site.home.portal.head_teacher_message') }}
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
    </aside>
</section>

{{-- ── Mobile principal strip (floats up over hero bottom edge) ── --}}
<div class="lg:hidden px-4 -mt-8 relative z-20">
    <div class="rounded-2xl bg-white shadow-xl ring-1 ring-gray-100 overflow-hidden">
        <div class="flex items-center gap-4 p-4">
            <div class="shrink-0 h-16 w-16 rounded-xl overflow-hidden ring-2" style="ring-color:{{ $primary }}33;">
                <img src="{{ $principalImage }}" alt="{{ $siteSettings->get('principal_name') }}"
                     class="h-full w-full object-cover object-top">
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-[9px] font-black uppercase tracking-widest mb-0.5" style="color:{{ $accent }}">
                    {{ __('site.home.portal.head_teacher') }}
                </p>
                <h3 class="font-black text-sm text-gray-900 truncate">{{ $siteSettings->get('principal_name') }}</h3>
                <p class="text-xs text-gray-500 truncate">{{ $siteSettings->localized('principal_role', __('site.home.principal_role')) }}</p>
            </div>
            <a href="#principal-message"
               class="shrink-0 rounded-xl px-4 py-2.5 text-xs font-black text-white shadow-sm"
               style="background:{{ $primary }}">
                Message
            </a>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     § 2  QUICK LINKS GRID
══════════════════════════════════════════════════════ --}}
@if($quickLinks->isNotEmpty())
<section class="px-4 sm:px-6 lg:px-8 mt-5">
    <div class="mx-auto max-w-7xl">
        <div class="overflow-hidden rounded-2xl bg-white shadow-md ring-1 ring-gray-100 hp-reveal">
            <div class="grid grid-cols-3"
                 style="grid-template-columns:repeat({{ min($quickLinks->count(),3) }},1fr);">
                {{-- First row --}}
                @foreach($quickLinks->take(3) as $idx => $link)
                @php $ext = $link->url && str_starts_with($link->url,'http'); @endphp
                <a href="{{ $link->url ?: '#' }}" @if($ext) target="_blank" rel="noopener" @endif
                   class="hp-ql flex flex-col items-center gap-2 border-b border-r border-gray-100 px-2 py-5 text-center last:border-r-0">
                    <div class="hp-ql-icon flex h-11 w-11 items-center justify-center rounded-xl text-white"
                         style="background:{{ $primary }};">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $link->icon_path }}"/>
                        </svg>
                    </div>
                    <span class="hp-ql-label text-xs font-black leading-tight text-gray-800">{{ $link->localizedTitle() }}</span>
                    @if($link->localizedSubtitle())
                    <span class="hidden sm:block text-[10px] text-gray-400">{{ $link->localizedSubtitle() }}</span>
                    @endif
                </a>
                @endforeach
            </div>
            @if($quickLinks->count() > 3)
            <div class="grid border-t border-gray-100"
                 style="grid-template-columns:repeat({{ min($quickLinks->skip(3)->count(),4) }},1fr);">
                @foreach($quickLinks->skip(3) as $idx => $link)
                @php $ext = $link->url && str_starts_with($link->url,'http'); @endphp
                <a href="{{ $link->url ?: '#' }}" @if($ext) target="_blank" rel="noopener" @endif
                   class="hp-ql flex flex-col items-center gap-2 border-r border-gray-100 px-2 py-5 text-center last:border-r-0">
                    <div class="hp-ql-icon flex h-11 w-11 items-center justify-center rounded-xl text-white"
                         style="background:{{ $dark }};">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $link->icon_path }}"/>
                        </svg>
                    </div>
                    <span class="hp-ql-label text-xs font-black leading-tight text-gray-800">{{ $link->localizedTitle() }}</span>
                    @if($link->localizedSubtitle())
                    <span class="hidden sm:block text-[10px] text-gray-400">{{ $link->localizedSubtitle() }}</span>
                    @endif
                </a>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</section>
@endif

{{-- ══════════════════════════════════════════════════════
     § 3  STATS CARDS
══════════════════════════════════════════════════════ --}}
<section class="px-4 sm:px-6 lg:px-8 mt-4">
    <div class="mx-auto max-w-7xl">
        @php
        $stats = [
            ['value' => $studentsCount.'+', 'label' => __('site.home.students'), 'icon' => 'M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4 0a4 4 0 100-8 4 4 0 000 8z', 'dark' => true],
            ['value' => $teachersCount.'+', 'label' => __('site.home.portal.teachers_staff'), 'icon' => 'M12 14l9-5-9-5-9 5 9 5zm0 0v7', 'dark' => false],
            ['value' => '100%',              'label' => __('site.home.portal.community_school'), 'icon' => 'M12 21C7 18.5 4 14.5 4 9a8 8 0 1116 0c0 5.5-3 9.5-8 12z', 'dark' => true],
            ['value' => __('site.home.portal.general'), 'label' => __('site.home.portal.ecd_grade_12'), 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13', 'dark' => false],
            ['value' => $established,        'label' => __('site.home.portal.established'), 'icon' => 'M8 7V3m8 4V3M5 11h14M6 5h12v15H6V5z', 'dark' => true],
        ];
        @endphp
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
            @foreach($stats as $idx => $stat)
            <div class="hp-stat hp-reveal hp-delay-{{ $idx + 1 }} rounded-xl overflow-hidden bg-white ring-1 ring-gray-100 shadow-sm"
                 style="border-top: 3px solid {{ $idx % 2 === 0 ? $primary : $accent }};">
                <div class="flex flex-col gap-3 p-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg"
                         style="background:{{ $primary }}14;">
                        <svg class="h-5 w-5" style="color:{{ $primary }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $stat['icon'] }}"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xl font-black leading-none" style="color:{{ $primary }};font-family:var(--ff-head)">{{ $stat['value'] }}</p>
                        <p class="mt-1 text-[11px] font-semibold leading-tight text-gray-500">{{ $stat['label'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════
     § 4  ACADEMIC PROGRAMS — horizontal snap on mobile
══════════════════════════════════════════════════════ --}}
@if($learningPathways->isNotEmpty())
<section class="mt-10 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <div class="mb-6 flex items-end justify-between hp-reveal">
            <div>
                <p class="text-xs font-black uppercase tracking-[.22em]" style="color:{{ $primary }}">
                    {{ __('site.home.academic_programs') }}
                </p>
                <h2 class="mt-1 text-2xl font-black text-gray-950" style="font-family:var(--ff-head)">
                    {{ __('site.home.portal.programs_title') }}
                </h2>
            </div>
            <p class="flex items-center gap-1 text-[11px] font-semibold text-gray-400 sm:hidden">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                Swipe
            </p>
        </div>

        <div class="hp-programs-track">
            @foreach($learningPathways as $idx => $program)
            @php $pExt = $program->url && str_starts_with($program->url,'http'); @endphp
            <a href="{{ $program->url ?: '#' }}"
               @if($pExt) target="_blank" rel="noopener" @endif
               class="hp-prog-card hp-reveal hp-delay-{{ min($idx+1,6) }}">
                {{-- Image --}}
                <div class="relative overflow-hidden" style="height:175px;">
                    <img src="{{ $program->image_url ?: $heroImage }}" alt="{{ $program->title }}"
                         class="h-full w-full object-cover">
                    <div class="absolute inset-0" style="background:linear-gradient(to top,{{ $dark }}99,transparent 55%)"></div>
                    {{-- Icon badge --}}
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 translate-y-1/2 flex h-11 w-11 items-center justify-center rounded-full border-4 border-white shadow-lg"
                         style="background:{{ $primary }}">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $program->icon_path }}"/>
                        </svg>
                    </div>
                </div>
                {{-- Text --}}
                <div class="px-5 pb-5 pt-9 text-center">
                    <h3 class="text-sm font-black leading-snug" style="color:{{ $primary }}">{{ $program->localizedTitle() }}</h3>
                    <p class="mt-1 text-xs font-bold text-gray-500">{{ $program->localizedSubtitle() }}</p>
                    <p class="mt-3 line-clamp-3 text-xs leading-5 text-gray-600">{{ $program->description }}</p>
                    <span class="mt-4 inline-flex items-center gap-1.5 text-xs font-black" style="color:{{ $primary }}">
                        {{ __('site.home.explore_program') }}
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ══════════════════════════════════════════════════════
     § 5  NOTICES / EVENTS / UPDATES — tabbed on mobile
══════════════════════════════════════════════════════ --}}
<section class="mt-10 px-4 sm:px-6 lg:px-8"
         x-data="{ tab:'notices' }">
    <div class="mx-auto max-w-7xl">

        {{-- Mobile tab bar --}}
        <div class="flex rounded-xl bg-white p-1 shadow-sm ring-1 ring-gray-100 mb-4 lg:hidden hp-reveal">
            @foreach([['notices',__('site.home.latest_notices')],['events',__('site.home.upcoming_events')],['updates',__('site.home.notice_event_label')]] as [$key,$label])
            <button @click="tab='{{ $key }}'"
                    :class="tab==='{{ $key }}' ? 'text-white shadow-sm' : 'text-gray-500'"
                    :style="tab==='{{ $key }}' ? 'background:{{ $primary }}' : ''"
                    class="flex-1 truncate rounded-lg px-2 py-2.5 text-xs font-black transition-all">
                {{ $label }}
            </button>
            @endforeach
        </div>

        <div class="grid gap-4 lg:grid-cols-[1fr_1fr_1fr_272px]">

            {{-- ─ NOTICES ─ --}}
            <div :class="tab==='notices' ? '' : 'hidden lg:block'"
                 class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 hp-reveal">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-black text-gray-900 text-sm">{{ __('site.home.latest_notices') }}</h2>
                    <a href="{{ route('notices') }}" class="text-xs font-black hover:underline" style="color:{{ $primary }}">{{ __('site.view_all') }} →</a>
                </div>
                <div class="space-y-0.5">
                    @forelse($homeNotices ?? collect() as $notice)
                    <a href="{{ route('news.show', $notice->slug) }}" class="hp-notice-row flex items-start gap-3 px-3 py-3">
                        <div class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg"
                             style="background:{{ $primary }}15; color:{{ $primary }}">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 3h9l3 3v15H6V3z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <span class="block truncate text-sm font-bold text-gray-800">{{ $notice->title }}</span>
                            <span class="text-xs text-gray-400">{{ optional($notice->created_at)->format('M d, Y') }}</span>
                        </div>
                    </a>
                    @empty
                    <p class="rounded-xl bg-gray-50 p-5 text-xs font-semibold text-gray-400 text-center">{{ __('site.home.no_notices') }}</p>
                    @endforelse
                </div>
            </div>

            {{-- ─ EVENTS ─ --}}
            <div :class="tab==='events' ? '' : 'hidden lg:block'"
                 class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 hp-reveal hp-delay-2">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-black text-gray-900 text-sm">{{ __('site.home.upcoming_events') }}</h2>
                    <a href="{{ route('events') }}" class="text-xs font-black hover:underline" style="color:{{ $primary }}">{{ __('site.home.portal.view_calendar') }} →</a>
                </div>
                <div class="space-y-0.5">
                    @forelse($homeEvents ?? collect() as $event)
                    @php $evDate = $event->event_date ? \Carbon\Carbon::parse($event->event_date) : $event->created_at; @endphp
                    <a href="{{ route('news.show', $event->slug) }}" class="hp-notice-row flex items-start gap-3 px-3 py-3">
                        <div class="flex h-11 w-10 shrink-0 flex-col items-center justify-center rounded-xl border text-center"
                             style="border-color:{{ $primary }}33; background:{{ $primary }}08;">
                            <span class="text-[9px] font-black uppercase leading-none" style="color:{{ $primary }}">{{ optional($evDate)->format('M') }}</span>
                            <span class="text-base font-black leading-tight" style="color:{{ $primary }}">{{ optional($evDate)->format('d') }}</span>
                        </div>
                        <div class="min-w-0">
                            <span class="block truncate text-sm font-bold text-gray-800">{{ $event->title }}</span>
                            <span class="line-clamp-1 text-xs text-gray-400">{{ $event->event_location ?: __('site.home.portal.school_hall') }}</span>
                        </div>
                    </a>
                    @empty
                    <p class="rounded-xl bg-gray-50 p-5 text-xs font-semibold text-gray-400 text-center">{{ __('site.home.no_events') }}</p>
                    @endforelse
                </div>
            </div>

            {{-- ─ UPDATES ─ --}}
            <div :class="tab==='updates' ? '' : 'hidden lg:block'"
                 class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 hp-reveal hp-delay-3">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-black text-gray-900 text-sm">{{ __('site.home.notice_event_label') }}</h2>
                    <a href="{{ route('news') }}" class="text-xs font-black hover:underline" style="color:{{ $primary }}">{{ __('site.view_all') }} →</a>
                </div>
                <div class="space-y-0.5">
                    @forelse($homeNews ?? collect() as $item)
                    <a href="{{ route('news.show', $item->slug) }}" class="hp-notice-row flex items-start gap-3 px-3 py-3">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg"
                             style="background:{{ $primary }}15; color:{{ $primary }}">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <span class="block truncate text-sm font-bold text-gray-800">{{ $item->title }}</span>
                            <span class="text-xs text-gray-400 capitalize">{{ $item->type }}</span>
                        </div>
                    </a>
                    @empty
                    <p class="rounded-xl bg-gray-50 p-5 text-xs font-semibold text-gray-400 text-center">{{ __('site.home.portal.no_updates') }}</p>
                    @endforelse
                </div>
            </div>

            {{-- ─ iEMIS ─ --}}
            <aside class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 hp-reveal hp-delay-4">
                <p class="text-[10px] font-black uppercase tracking-widest mb-4" style="color:{{ $primary }}">
                    {{ __('site.home.portal.iemis_live_data') }}
                </p>
                <div class="rounded-xl border border-gray-100 bg-gray-50 py-5 px-4 text-center mb-4">
                    <p class="text-4xl font-black italic" style="color:{{ $primary }}">iEMIS</p>
                    <p class="mt-1.5 text-[10px] font-bold uppercase tracking-wider text-gray-500">{{ __('site.home.portal.iemis_full') }}</p>
                </div>
                <div class="rounded-xl px-3 py-3 text-xs font-black text-center mb-4"
                     style="background:{{ $primary }}10; color:{{ $primary }}">
                    {{ __('site.home.portal.authentic_school_data') }}
                </div>
                <a href="{{ $iemisUrl }}" target="_blank" rel="noopener"
                   class="flex w-full items-center justify-center gap-1.5 rounded-xl border py-3 text-sm font-black transition hover:shadow-md"
                   style="border-color:{{ $primary }}33; color:{{ $primary }}">
                    {{ __('site.home.portal.view_iemis') }}
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </aside>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════
     § 6  PRINCIPAL MESSAGE
══════════════════════════════════════════════════════ --}}
<section id="principal-message" class="mt-10 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <div class="overflow-hidden rounded-2xl shadow-lg hp-reveal" style="background:{{ $dark }}">
            <div class="grid lg:grid-cols-[300px_1fr]">

                {{-- Photo --}}
                <div class="relative overflow-hidden" style="min-height:220px;">
                    <img src="{{ $principalImage }}" alt="{{ $siteSettings->get('principal_name') }}"
                         class="h-full w-full object-cover object-top lg:absolute lg:inset-0">
                    {{-- Mobile gradient overlay with name --}}
                    <div class="absolute inset-0 flex flex-col justify-end p-5 lg:hidden"
                         style="background:linear-gradient(to top,{{ $dark }}ee 0%,transparent 55%)">
                        <p class="text-[10px] font-black uppercase tracking-widest mb-1" style="color:{{ $accent }}">
                            {{ $siteSettings->localized('principal_message', __('site.home.principal_message')) }}
                        </p>
                        <h3 class="text-lg font-black text-white">{{ $siteSettings->get('principal_name') }}</h3>
                        <p class="text-xs text-white/65">{{ $siteSettings->localized('principal_role', __('site.home.principal_role')) }}</p>
                    </div>
                </div>

                {{-- Quote --}}
                <div class="flex flex-col justify-center p-6 sm:p-8 lg:p-10">
                    <p class="hidden lg:block text-[10px] font-black uppercase tracking-widest mb-4" style="color:{{ $accent }}">
                        {{ $siteSettings->localized('principal_message', __('site.home.principal_message')) }}
                    </p>
                    <svg class="mb-4 h-8 w-8 opacity-30 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                    </svg>
                    <blockquote class="text-lg font-bold leading-8 text-white/90 sm:text-xl" style="font-family:var(--ff-head)">
                        "{{ $siteSettings->localized('principal_quote', __('site.home.principal_quote')) }}"
                    </blockquote>
                    {{-- Desktop author row --}}
                    <div class="hidden lg:flex items-center gap-3 mt-7 pt-6 border-t border-white/10">
                        <div class="flex h-11 w-11 items-center justify-center rounded-full font-black" style="background:{{ $primary }};color:{{ $accent }}">
                            {{ $siteSettings->get('principal_initials', 'P') }}
                        </div>
                        <div>
                            <p class="font-black text-white text-sm">{{ $siteSettings->get('principal_name') }}</p>
                            <p class="text-xs text-white/55">{{ $siteSettings->localized('principal_role', __('site.home.principal_role')) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════
     § 7  PHOTO GALLERY
══════════════════════════════════════════════════════ --}}
@php $galleryItems = ($homeGallery ?? $campusImages ?? collect())->take(6); @endphp
@if($galleryItems->isNotEmpty())
<section class="mt-10 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <div class="mb-5 flex items-end justify-between hp-reveal">
            <div>
                <p class="text-xs font-black uppercase tracking-[.22em]" style="color:{{ $primary }}">{{ __('site.nav.gallery') }}</p>
                <h2 class="mt-1 text-2xl font-black text-gray-950" style="font-family:var(--ff-head)">{{ __('site.home.portal.campus_moments') }}</h2>
            </div>
            <a href="{{ route('gallery') }}" class="rounded-xl border px-4 py-2 text-xs font-black transition hover:shadow-sm"
               style="border-color:{{ $primary }}33; color:{{ $primary }}">
               {{ __('site.home.portal.view_gallery') }}
            </a>
        </div>

        {{-- Uniform grid: all tiles the same size --}}
        <div class="grid grid-cols-2 gap-2 sm:gap-3 sm:grid-cols-3">
            @foreach($galleryItems as $idx => $img)
            <a href="{{ $img->url }}" target="_blank" rel="noopener"
               class="hp-gal-tile block overflow-hidden rounded-xl bg-gray-100 hp-reveal hp-delay-{{ min($idx+1,6) }}"
               style="aspect-ratio:4/3;">
                <img src="{{ $img->url }}" alt="{{ $img->caption ?: ($img->name ?? 'Gallery') }}"
                     class="h-full w-full object-cover">
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ══════════════════════════════════════════════════════
     § 8  CTA / ENROLLMENT
══════════════════════════════════════════════════════ --}}
<section class="mt-10 mb-8 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <div class="relative overflow-hidden rounded-2xl px-6 py-10 text-center shadow-lg hp-reveal"
             style="background:{{ $primary }}">
            {{-- Decorative circles --}}
            <div class="pointer-events-none absolute -top-14 -right-14 h-48 w-48 rounded-full opacity-20" style="background:{{ $accent }}"></div>
            <div class="pointer-events-none absolute -bottom-14 -left-14 h-48 w-48 rounded-full opacity-10 bg-white"></div>
            <div class="pointer-events-none absolute top-0 left-0 right-0 h-px opacity-20 bg-white"></div>

            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-widest mb-2" style="color:{{ $accent }}">
                    {{ __('site.home.portal.admission_contact') }}
                </p>
                <h2 class="text-2xl sm:text-3xl font-black text-white mb-3" style="font-family:var(--ff-head)">
                    {{ __('site.home.portal.admission_contact_title') }}
                </h2>
                <p class="text-sm text-white/70 mb-7">
                    {{ $siteSettings->get('school_phone') }} &nbsp;·&nbsp; {{ $siteSettings->get('school_email') }}
                </p>
                <div class="flex flex-wrap items-center justify-center gap-3">
                    @if($admissionsEnabled)
                    <a href="{{ route('admissions') }}"
                       class="inline-flex items-center gap-2 rounded-xl px-6 py-3.5 text-sm font-extrabold text-gray-900 shadow-xl shadow-black/15 transition hover:brightness-105 active:scale-95"
                       style="background:{{ $accent }}">
                        {{ __('site.home.apply_now') }}
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    @endif
                    <a href="{{ route('contact') }}"
                       class="hp-btn-ghost inline-flex items-center gap-2 rounded-xl px-6 py-3.5 text-sm font-extrabold text-white">
                        {{ __('site.home.contact_us') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════
     § 9  KEY PERSONNEL
══════════════════════════════════════════════════════ --}}
@if($keyPersons->isNotEmpty())
<section class="mt-10 mb-8 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between hp-reveal">
            <div>
                <p class="text-xs font-black uppercase tracking-[.22em]" style="color:{{ $primary }}">Key Personnel</p>
                <h2 class="mt-1 text-2xl font-black text-gray-950" style="font-family:var(--ff-head)">School Leadership</h2>
            </div>
            <p class="max-w-xl text-sm font-semibold leading-6 text-gray-500">
                Meet the people guiding daily academics, administration, and student support.
            </p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($keyPersons as $idx => $person)
            <article class="flex min-w-0 items-center gap-4 rounded-2xl bg-[#f2f8ff] p-4 shadow-sm ring-1 ring-blue-50 hp-reveal hp-delay-{{ min($idx + 1, 6) }}">
                <div class="h-24 w-28 shrink-0 overflow-hidden rounded-xl bg-gray-100 sm:h-24 sm:w-24">
                    <img src="{{ $person->photo_url }}"
                         alt="{{ $person->name }}"
                         class="h-full w-full object-cover object-top">
                </div>
                <div class="min-w-0 flex-1">
                    <h3 class="truncate text-base font-black leading-tight text-gray-950">{{ $person->name }}</h3>
                    <p class="mt-1 truncate text-sm font-bold text-gray-500">{{ $person->designation }}</p>
                    @if($person->phone || $person->email)
                    <div class="mt-3 space-y-1.5 text-sm font-black text-[#0b63b6]">
                        @if($person->phone)
                        <p class="flex min-w-0 items-center gap-2">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106a1.125 1.125 0 00-1.173.417l-.97 1.293a1.125 1.125 0 01-1.21.38 12.035 12.035 0 01-7.143-7.143 1.125 1.125 0 01.38-1.21l1.293-.97a1.125 1.125 0 00.417-1.173L6.963 3.102A1.125 1.125 0 005.872 2.25H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>
                            </svg>
                            <span class="truncate">{{ $person->phone }}</span>
                        </p>
                        @endif
                        @if($person->email)
                        <p class="flex min-w-0 items-center gap-2">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0l-7.5-4.615a2.25 2.25 0 01-1.07-1.916V6.75"/>
                            </svg>
                            <span class="truncate">{{ $person->email }}</span>
                        </p>
                        @endif
                    </div>
                    @endif
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>
@endif

</main>

<script>
(function () {
    // ── Intersection Observer for .hp-reveal / .hp-reveal-l / .hp-reveal-r ──
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('hp-in');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.08, rootMargin: '0px 0px -32px 0px' });

    document.querySelectorAll('.hp-reveal, .hp-reveal-l, .hp-reveal-r').forEach(el => {
        observer.observe(el);
    });
})();
</script>
