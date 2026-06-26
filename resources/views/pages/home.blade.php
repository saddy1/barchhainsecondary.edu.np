{{-- resources/views/pages/home.blade.php --}}
@extends('layouts.app')



@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">

<style>
/* ─── ROOT TOKENS ─────────────────────────────────── */
:root {
    --forest:   var(--theme-primary);
    --forest-d: var(--theme-dark);
    --forest-l: var(--theme-primary-light);
    --gold:     var(--theme-secondary);
    --gold-l:   var(--theme-secondary-light);
    --gold-d:   var(--theme-secondary);
    --cream:    var(--theme-surface);
    --ash:      var(--theme-muted-surface);
    --ink:      #111827;
    --mist:     var(--theme-muted-text);
}
body { font-family: var(--ff-body); }

/* ─── SCROLLBAR ───────────────────────────────────── */
::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: var(--ash); }
::-webkit-scrollbar-thumb { background: var(--forest); border-radius: 10px; }
.popup-scroll::-webkit-scrollbar { width: 4px; }
.popup-scroll::-webkit-scrollbar-track { background: #f3f4f6; }
.popup-scroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
.hide-sb::-webkit-scrollbar { display: none; }
.hide-sb { -ms-overflow-style: none; scrollbar-width: none; }

/* ─── MARQUEE ─────────────────────────────────────── */
@keyframes marquee { from { transform: translateX(0); } to { transform: translateX(-50%); } }
.marquee-inner { animation: marquee 28s linear infinite; white-space: nowrap; }
.marquee-inner:hover { animation-play-state: paused; }

/* ─── FLOAT BLOBS ─────────────────────────────────── */
@keyframes float-a { 0%,100% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-18px) rotate(3deg); } }
@keyframes float-b { 0%,100% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-12px) rotate(-2deg); } }
.float-a { animation: float-a 5s ease-in-out infinite; }
.float-b { animation: float-b 6.5s ease-in-out infinite .8s; }

/* ─── COUNTER ANIMATION ───────────────────────────── */
@keyframes count-up { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
.count-reveal { animation: count-up .7s ease-out forwards; }

/* ─── SECTION REVEAL ──────────────────────────────── */
.reveal { opacity:0; transform:translateY(40px); transition: opacity .8s ease, transform .8s ease; }
.reveal.visible { opacity:1; transform:translateY(0); }
.reveal-left { opacity:0; transform:translateX(-50px); transition: opacity .8s ease, transform .8s ease; }
.reveal-left.visible { opacity:1; transform:translateX(0); }
.reveal-right { opacity:0; transform:translateX(50px); transition: opacity .8s ease, transform .8s ease; }
.reveal-right.visible { opacity:1; transform:translateX(0); }

/* ─── TYPEWRITER CURSOR ───────────────────────────── */
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:0} }
.cursor { display:inline-block; width:3px; height:1em; background:var(--gold); margin-left:4px; vertical-align:middle; animation:blink 1s step-end infinite; }

/* ─── PROGRAM CARDS ───────────────────────────────── */
.prog-card { transition: transform .4s cubic-bezier(.22,.68,0,1.2), box-shadow .4s ease; }
.prog-card:hover { transform: translateY(-10px) scale(1.02); box-shadow: 0 30px 60px -15px rgba(11,36,21,.25); }

/* ─── FEATURE ICON SPIN ───────────────────────────── */
.feat-icon { transition: transform .5s cubic-bezier(.22,.68,0,1.4); }
.group:hover .feat-icon { transform: rotateY(360deg); }

/* ─── NOTICE ITEMS ────────────────────────────────── */
.notice-item { border-left:3px solid transparent; transition: border-color .3s, background .3s, transform .3s; }
.notice-item:hover { border-left-color:var(--gold); background:#fffdf6; transform:translateX(6px); }

/* ─── WAVE DIVIDER ────────────────────────────────── */
.wave-divider { line-height:0; }
.wave-divider svg { display:block; }

/* ─── SHIMMER BUTTON ──────────────────────────────── */
.btn-shimmer { position:relative; overflow:hidden; }
.btn-shimmer::after { content:''; position:absolute; top:0; left:-100%; width:60%; height:100%; background:linear-gradient(90deg,transparent,rgba(255,255,255,.35),transparent); transform:skewX(-20deg); transition:left .6s ease; }
.btn-shimmer:hover::after { left:160%; }

/* ─── PARALLAX IMG ────────────────────────────────── */
.parallax-img { transition:transform .1s linear; will-change:transform; }

/* ─── STAT CARD ───────────────────────────────────── */
.stat-card:hover { box-shadow:0 0 40px -10px rgba(226,160,36,.5); transform:translateY(-4px); }
.stat-card { transition:box-shadow .3s, transform .3s; }

/* ─── TESTIMONIAL ─────────────────────────────────── */
.testi-card { transition:transform .4s ease, box-shadow .4s ease; }
.testi-card:hover { transform:translateY(-8px); box-shadow:0 24px 48px -12px rgba(11,36,21,.15); }

/* ─── PROGRESS BAR ────────────────────────────────── */
@keyframes progress-in { from{width:0%} to{width:100%} }
.progress-bar { animation:progress-in 6s linear forwards; }

/* ─── POPUP ───────────────────────────────────────── */
/*
   FIX: The popup modal must:
   1. Never exceed viewport height (use dvh with px fallback)
   2. Close button stays inside the modal card (not the overlay)
   3. Image fills available space, footer is always visible
   4. No empty dead space below the image
*/
.popup-overlay {
    position: fixed;
    inset: 0;
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: max(18px, env(safe-area-inset-top)) 18px max(18px, env(safe-area-inset-bottom));
    background: rgba(11,36,21,.92);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
}
.popup-card {
    position: relative;
    width: 100%;
    max-width: min(860px, calc(100vw - 36px));
    max-height: calc(100dvh - 36px);
    max-height: calc(100vh - 36px);
    display: flex;
    flex-direction: column;
    border-radius: 1.35rem;
    overflow: hidden;
    background: #0b2415;
    border: 1px solid rgba(255,255,255,0.18);
    box-shadow: 0 36px 90px rgba(0,0,0,0.55);
}
.popup-img-area {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    min-height: 0;
    background: #eef3ee;
    padding: 18px;
    display: flex;
    align-items: flex-start;
    justify-content: center;
}
.popup-img-area a { display: block; width: 100%; }
.popup-img-area img {
    display: block;
    width: auto;
    max-width: 100%;
    height: auto;
    margin: 0 auto;
    border-radius: 10px;
    background: white;
    box-shadow: 0 10px 30px rgba(11,36,21,.16);
}
.popup-footer {
    flex-shrink: 0;
    background: #0b2415;
    padding: 14px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    border-top: 1px solid rgba(255,255,255,.1);
}
.popup-close {
    position: absolute;
    top: 14px;
    right: 14px;
    z-index: 10;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: rgba(11,36,21,0.84);
    border: 1px solid rgba(255,255,255,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    cursor: pointer;
    transition: background 0.2s, transform 0.2s;
    backdrop-filter: blur(4px);
}
.popup-close:hover { background: #e2a024; color:#0b2415; transform: scale(1.06); }

/* Hide scrollbar on popup-img-area */
.popup-img-area::-webkit-scrollbar { width: 4px; }
.popup-img-area::-webkit-scrollbar-track { background: #f3f4f6; }
.popup-img-area::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
@media (max-width: 640px) {
    .popup-overlay { padding: 10px; }
    .popup-card {
        max-width: calc(100vw - 20px);
        max-height: calc(100dvh - 20px);
        max-height: calc(100vh - 20px);
        border-radius: 1rem;
    }
    .popup-img-area { padding: 10px; }
    .popup-footer {
        align-items: stretch;
        flex-direction: column;
        padding: 12px;
    }
    .popup-footer > div:last-child { justify-content: flex-end; }
}
</style>

@push('modals')
{{-- ═══════════════════════════════════════════════════════
     POPUP MODAL — rendered at body level so z-index works above fixed header
═══════════════════════════════════════════════════════ --}}
@if(isset($popups) && $popups->count() > 0)
<div x-data="{
    open: true,
    currentIndex: 0,
    total: {{ $popups->count() }},
    next() {
        if (this.currentIndex < this.total - 1) {
            this.currentIndex++;
            this.$nextTick(() => {
                const el = this.$refs['imgarea_' + this.currentIndex];
                if (el) el.scrollTop = 0;
            });
        } else {
            this.open = false;
        }
    }
}"
     x-show="open"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 scale-95"
     x-transition:enter-end="opacity-100 scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-95"
     class="popup-overlay"
     style="display:none;"
     @keydown.escape.window="open = false">

    <div class="popup-card">

        {{-- Close button — inside the card, always visible --}}
        <button @click="open = false" class="popup-close" aria-label="Close">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        {{-- Progress bar --}}
        <div class="absolute top-0 left-0 right-0 h-1 z-20 bg-white/20">
            <div class="h-full bg-[#e2a024] progress-bar" :key="currentIndex"></div>
        </div>

        {{-- Slides: stack on top of each other, only active one visible --}}
        <template x-for="(popup, index) in {{ $popups->toJson() }}" :key="index">
            <div x-show="currentIndex === index"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-4"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 class="contents">
            </div>
        </template>

        @foreach($popups as $index => $popup)
        <div x-show="currentIndex === {{ $index }}"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="flex flex-col min-h-0 flex-1">

            {{-- Image area: scrollable, fills space, NO fixed height --}}
            <div class="popup-img-area" x-ref="imgarea_{{ $index }}">
                @if($popup->link_url)
                <a href="{{ $popup->link_url }}" target="_blank" rel="noopener">
                    <img src="{{ asset($popup->image_path) }}"
                         alt="{{ $popup->title }}"
                         loading="{{ $index === 0 ? 'eager' : 'lazy' }}">
                </a>
                @else
                <img src="{{ asset($popup->image_path) }}"
                     alt="{{ $popup->title }}"
                     loading="{{ $index === 0 ? 'eager' : 'lazy' }}">
                @endif
            </div>

            {{-- Footer: always pinned --}}
            <div class="popup-footer">
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-white text-sm truncate mb-1.5">{{ $popup->title }}</h3>
                    {{-- Dot progress indicators --}}
                    <div class="flex gap-1.5 items-center">
                        @foreach($popups as $di => $dp)
                        <div class="h-1 rounded-full transition-all duration-500"
                             :class="currentIndex === {{ $di }}
                                 ? 'w-6 bg-[#e2a024]'
                                 : (currentIndex > {{ $di }} ? 'w-2 bg-white/20' : 'w-2 bg-white/40')"></div>
                        @endforeach
                        <span class="ml-1 text-[10px] text-white/50 font-semibold tracking-widest uppercase">
                            {{ $index + 1 }} / {{ $popups->count() }}
                        </span>
                    </div>
                </div>

                <div class="flex gap-2 shrink-0">
                    @if($popup->link_url)
                    <a href="{{ $popup->link_url }}" target="_blank" rel="noopener"
                       class="px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold border border-white/20 flex items-center gap-1.5 transition">
                        Open
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                    @endif
                    <button @click.prevent="next()"
                            class="px-4 py-2 rounded-xl bg-[#e2a024] hover:bg-[#f5c355] text-[#0b2415] text-xs font-bold flex items-center gap-1.5 transition">
                        <span x-text="currentIndex < total - 1 ? 'Next' : 'Close'"></span>
                        <svg x-show="currentIndex < total - 1"
                             class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        @endforeach

    </div>
</div>
@endif
@endpush


{{-- ═══════════════════════════════════════════════════════
     HERO
═══════════════════════════════════════════════════════ --}}
@include('pages.partials.home-portal')
@if(false)
<section class="relative overflow-hidden bg-[#fdfbf7]">

    <div class="absolute inset-x-0 top-0 h-1.5 bg-[#1a5632]"></div>
    <div class="absolute right-0 top-0 hidden h-full w-[38%] bg-[#0b2415] lg:block"></div>

    <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24 z-10">
        <div class="grid lg:grid-cols-[0.92fr_1.08fr] gap-10 lg:gap-14 items-center">

            <div class="reveal-left visible min-w-0">
                <div class="inline-flex items-center gap-3 rounded-full border border-[#1a5632]/15 bg-white px-4 py-2 text-sm font-bold text-[#1a5632] shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-[#e26a4b] shrink-0"></span>
                    {{ __('site.home.eyebrow') }}
                </div>

                <div class="mt-8 flex items-center gap-4 rounded-2xl border border-[#1a5632]/10 bg-white/80 p-4 shadow-sm backdrop-blur">
                    <img src="{{ $siteSettings->logoUrl() }}" alt="{{ __('site.school_name') }}" class="h-14 w-14 rounded-xl object-contain ring-1 ring-[#1a5632]/10">
                    <div class="min-w-0">
                        <p class="text-sm font-black uppercase tracking-[.22em] text-[#e2a024]">Community Government School</p>
                        <p class="mt-1 truncate text-lg font-black text-[#0b2415]">{{ __('site.school_name') }}</p>
                    </div>
                </div>

                <h1 class="mt-8 max-w-3xl font-bold leading-[1.04] tracking-tight text-[#0b2415]"
                    style="font-family:var(--ff-head);font-size:clamp(2.45rem,4.8vw,4.55rem);">
                    {{ __('site.home.hero_prefix') }}
                </h1>

                <p class="mt-5 text-xl font-black leading-relaxed text-[#1a5632]">
                    {{ __('site.home.hero_word') }}
                </p>

                <p class="mt-5 max-w-2xl text-base sm:text-lg leading-8 text-[#516257]">
                    {{ __('site.home.hero_subtitle') }}
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ url('/admissions') }}"
                       class="btn-shimmer inline-flex items-center gap-2.5 rounded-xl bg-[#e26a4b] px-6 py-3.5 text-base font-black text-white shadow-lg shadow-[#e26a4b]/20 transition-colors duration-300 hover:bg-[#d95738]">
                        {{ __('site.home.enroll_now') }}
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                    <a href="{{ url('/about') }}"
                       class="inline-flex items-center gap-2.5 rounded-xl border border-[#1a5632]/20 bg-white px-6 py-3.5 text-base font-black text-[#1a5632] transition-all duration-300 hover:border-[#1a5632]/40 hover:shadow-sm">
                        {{ __('site.home.explore_school') }}
                    </a>
                </div>
            </div>

            <div class="reveal-right visible relative">
                <div class="relative rounded-[2rem] border border-white/80 bg-white p-3 shadow-2xl lg:ml-2">
                    <div class="overflow-hidden rounded-[1.55rem]">
                        <img src="{{ $siteSettings->imageUrl('home_hero_image', 'assets/image/default-placeholder.jpg') }}" alt="{{ __('site.school_name') }} Campus"
                             class="h-[280px] w-full object-cover sm:h-[380px] lg:h-[460px] parallax-img" id="hero-img">
                    </div>
                    <div class="absolute inset-x-8 bottom-8 rounded-2xl border border-white/20 bg-[#0b2415]/88 p-4 text-white shadow-xl backdrop-blur">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-xs font-black uppercase tracking-[.22em] text-[#e2a024]">{{ __('site.home.pass_rate') }}</p>
                                <p class="mt-1 text-lg font-black">{{ __('site.home.top') }} {{ __('site.home.academics') }}</p>
                            </div>
                            <div class="grid grid-cols-3 gap-3 text-center">
                                @foreach([['470', __('site.home.students')], ['25', __('site.home.teachers')], ['65+', __('site.home.years')]] as $s)
                                <div class="rounded-xl bg-white/10 px-3 py-2">
                                    <div class="text-lg font-black text-[#e2a024]" style="font-family:var(--ff-head);">{{ $s[0] }}</div>
                                    <div class="text-[10px] font-bold uppercase leading-tight text-white/70">{{ $s[1] }}</div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="wave-divider absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            <path d="M0 80 L0 40 Q180 0 360 40 Q540 80 720 40 Q900 0 1080 40 Q1260 80 1440 40 L1440 80 Z" fill="#ffffff"/>
        </svg>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     CAMPUS DESIGNS (Homepage preview)
═══════════════════════════════════════════════════════ --}}
<section class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-[#e2a024] font-bold text-xs uppercase tracking-[.25em] mb-2">{{ __('site.home.campus_label') }}</p>
                    <h2 class="text-2xl font-bold text-[#0b2415]">{{ __('site.home.campus_title') }}</h2>
                    <p class="mt-2 text-sm text-[#65756b]">{{ __('site.home.campus_subtitle') }}</p>
                </div>
                <a href="{{ route('gallery') }}" class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-xl bg-[#1a5632] px-4 py-2 text-sm font-bold text-white hover:bg-[#0b2415]">{{ __('site.home.campus_cta') }}</a>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            @forelse($campusImages ?? collect() as $img)
                <a href="{{ $img->url }}" target="_blank" class="group block relative aspect-[4/3] rounded-xl overflow-hidden border border-gray-100 bg-gray-50 shadow-sm hover:shadow-md transition">
                    <img src="{{ $img->url }}" alt="{{ $img->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"> 
                    <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/20 to-transparent opacity-100 sm:opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="absolute inset-x-0 bottom-0 p-3 sm:p-4">
                        <p class="text-[10px] uppercase tracking-[.28em] text-[#e2a024] mb-1">{{ __('site.home.campus_preview_label') }}</p>
                        <p class="text-sm font-semibold text-white leading-tight line-clamp-2">{{ $img->caption ?: pathinfo($img->name, PATHINFO_FILENAME) }}</p>
                    </div>
                </a>
            @empty
                <div class="col-span-full py-12 text-center rounded-2xl border border-dashed border-gray-200 bg-[#fdfbf7]">
                    <div class="text-lg font-semibold text-[#65756b]">{{ __('site.home.no_campus_images') }}</div>
                    <div class="text-sm text-[#9aa19a] mt-2">{{ __('site.home.add_campus_help') }}</div>
                </div>
            @endforelse
        </div>
    </div>
</section>

<section class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-[.9fr_1.1fr] lg:items-center">
            <div>
                <p class="text-[#e2a024] font-bold text-xs uppercase tracking-[.25em] mb-3">{{ __('site.home.strengths') }}</p>
                <h2 class="text-3xl lg:text-4xl font-bold text-[#0b2415]" style="font-family:var(--ff-head);">{{ __('site.home.profile_title') }}</h2>
                <p class="mt-5 text-[#516257] leading-8">{{ __('site.home.profile_intro') }}</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach(__('site.home.profile_cards') as $card)
                <div class="rounded-2xl border border-[#1a5632]/10 bg-[#fdfbf7] p-5">
                    <div class="text-2xl font-black text-[#1a5632]" style="font-family:var(--ff-head);">{{ $card['value'] }}</div>
                    <div class="mt-2 text-sm font-semibold text-[#65756b]">{{ $card['label'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════════════════
     NOTICES & EVENTS
═══════════════════════════════════════════════════════ --}}
<section class="bg-[#f4f5f0] py-18 lg:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-10 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <p class="text-[#e2a024] font-bold text-xs uppercase tracking-[.25em] mb-3">{{ __('site.home.notice_event_label') }}</p>
                <h2 class="text-3xl lg:text-4xl font-bold text-[#0b2415]" style="font-family:var(--ff-head);">{{ __('site.home.notice_event_title') }}</h2>
                <p class="mt-4 text-[#65756b] leading-7">{{ __('site.home.notice_event_intro') }}</p>
            </div>
            <a href="{{ route('news') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#1a5632] px-5 py-3 text-sm font-bold text-white hover:bg-[#0b2415]">
                {{ __('site.view_all') }}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-3xl border border-green-900/10 bg-white p-5 shadow-sm sm:p-6">
                <div class="mb-5 flex items-center justify-between gap-4">
                    <h3 class="text-xl font-bold text-[#0b2415]">{{ __('site.home.latest_notices') }}</h3>
                    <a href="{{ route('notices') }}" class="text-sm font-bold text-[#1a5632] hover:text-[#e2a024]">{{ __('site.home.view_all_notices') }}</a>
                </div>

                <div class="space-y-3">
                    @forelse($homeNotices ?? collect() as $notice)
                        <a href="{{ route('news.show', $notice->slug) }}" class="notice-item block rounded-2xl border border-gray-100 bg-[#fdfbf7] p-4">
                            <div class="flex items-start gap-4">
                                <div class="flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-xl bg-[#1a5632] text-white">
                                    <span class="text-xs font-bold uppercase">{{ optional($notice->created_at)->format('M') }}</span>
                                    <span class="text-lg font-black leading-none">{{ optional($notice->created_at)->format('d') }}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="line-clamp-1 font-bold text-[#0b2415]">{{ $notice->title }}</p>
                                    <p class="mt-1 line-clamp-2 text-sm leading-6 text-[#65756b]">{{ $notice->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($notice->content), 110) }}</p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-2xl border border-dashed border-gray-200 bg-[#fdfbf7] p-8 text-center text-sm font-semibold text-[#65756b]">
                            {{ __('site.home.no_notices') }}
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-3xl border border-green-900/10 bg-white p-5 shadow-sm sm:p-6">
                <div class="mb-5 flex items-center justify-between gap-4">
                    <h3 class="text-xl font-bold text-[#0b2415]">{{ __('site.home.upcoming_events') }}</h3>
                    <a href="{{ route('events') }}" class="text-sm font-bold text-[#1a5632] hover:text-[#e2a024]">{{ __('site.home.view_all_events') }}</a>
                </div>

                <div class="space-y-3">
                    @forelse($homeEvents ?? collect() as $event)
                        @php
                            $eventDate = $event->event_date
                                ? \Carbon\Carbon::parse($event->event_date)
                                : $event->created_at;
                        @endphp
                        <a href="{{ route('news.show', $event->slug) }}" class="notice-item block rounded-2xl border border-gray-100 bg-[#fdfbf7] p-4">
                            <div class="flex items-start gap-4">
                                <div class="flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-xl bg-[#e2a024] text-[#0b2415]">
                                    <span class="text-xs font-black uppercase">{{ optional($eventDate)->format('M') }}</span>
                                    <span class="text-lg font-black leading-none">{{ optional($eventDate)->format('d') }}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="line-clamp-1 font-bold text-[#0b2415]">{{ $event->title }}</p>
                                    <p class="mt-1 text-xs font-bold uppercase tracking-wider text-[#1a5632]">
                                        {{ $event->event_location ?: __('site.location') }}
                                    </p>
                                    <p class="mt-1 line-clamp-1 text-sm text-[#65756b]">{{ $event->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($event->content), 90) }}</p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-2xl border border-dashed border-gray-200 bg-[#fdfbf7] p-8 text-center text-sm font-semibold text-[#65756b]">
                            {{ __('site.home.no_events') }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>



{{-- ═══════════════════════════════════════════════════════
     ACADEMIC PROGRAMS
═══════════════════════════════════════════════════════ --}}
<section class="py-20 bg-[#fdfbf7] overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-14 reveal">
            <p class="text-[#e2a024] font-bold text-xs uppercase tracking-[.25em] mb-3">{{ __('site.home.curriculum') }}</p>
            <h2 class="text-4xl lg:text-5xl font-bold text-[#0b2415] mb-4" style="font-family:var(--ff-head);">{{ __('site.home.academic_programs') }}</h2>
            <div class="w-16 h-1 mx-auto rounded-full bg-[#e2a024]"></div>
            <p class="text-[#6b7c72] text-lg mt-5 max-w-xl mx-auto">{{ __('site.home.program_intro') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch w-full min-w-0">
            @php
            $programs = [
                ['route'=>route('academics.elementary'),'label'=>__('site.academics.elementary.h1'),'sub'=>__('site.academics.elementary.sub'),'desc'=>__('site.academics.elementary.hero_sub'),'color'=>'#1a5632','num'=>'01','svg'=>'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                ['route'=>route('academics.primary'),'label'=>__('site.academics.primary.h1'),'sub'=>__('site.academics.primary.sub'),'desc'=>__('site.academics.primary.hero_sub'),'color'=>'#0b2415','num'=>'02','svg'=>'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z'],
                ['route'=>route('academics.secondary'),'label'=>__('site.academics.secondary.h1'),'sub'=>__('site.academics.secondary.sub'),'desc'=>__('site.academics.secondary.hero_sub'),'color'=>'#1a5632','num'=>'03','svg'=>'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z'],
            ];
            @endphp

            @foreach($programs as $i => $prog)
            <a href="{{ $prog['route'] }}"
               class="prog-card group flex flex-col rounded-3xl overflow-hidden shadow-md border border-gray-100 bg-white reveal min-w-0 w-full"
               style="transition-delay:{{ $i * 120 }}ms;">
                <div class="h-1.5 w-full shrink-0" style="background:{{ $prog['color'] }};"></div>
                <div class="flex flex-col flex-1 p-5 sm:p-7 min-w-0">
                    <div class="flex items-start justify-between mb-5 min-w-0 gap-2">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-sm shrink-0"
                             style="background:{{ $prog['color'] }}18;">
                            <svg class="w-6 h-6" fill="none" stroke="{{ $prog['color'] }}" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $prog['svg'] }}"/>
                            </svg>
                        </div>
                        <span class="text-4xl sm:text-5xl font-bold opacity-10 select-none leading-none shrink-0"
                              style="font-family:var(--ff-head);color:{{ $prog['color'] }};">{{ $prog['num'] }}</span>
                    </div>
                    <p class="text-[#e2a024] font-bold text-xs uppercase tracking-widest mb-1.5">{{ $prog['sub'] }}</p>
                    <h3 class="text-xl font-bold text-[#0b2415] mb-3" style="font-family:var(--ff-head);">{{ $prog['label'] }}</h3>
                    <p class="text-[#6b7c72] text-sm leading-relaxed flex-1">{{ $prog['desc'] }}</p>
                    <span class="inline-flex items-center gap-2 text-sm font-bold text-[#1a5632] group-hover:gap-3 transition-all mt-6">
                        {{ __('site.home.explore_program') }}
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </span>
                </div>
            </a>
            @endforeach
        </div>

    </div>
</section>




{{-- ═══════════════════════════════════════════════════════
     WHY CHOOSE US
═══════════════════════════════════════════════════════ --}}
<section class="py-20 bg-[#f4f5f0]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-14 reveal">
            <p class="text-[#e2a024] font-bold text-xs uppercase tracking-[.25em] mb-3">{{ __('site.home.strengths') }}</p>
            <h2 class="text-4xl lg:text-5xl font-bold text-[#0b2415] mb-4" style="font-family:var(--ff-head);">{{ __('site.home.why') }}</h2>
            <div class="w-16 h-1 mx-auto rounded-full bg-[#e2a024]"></div>
            <p class="text-[#6b7c72] text-lg mt-5 max-w-xl mx-auto">{{ __('site.home.why_intro') }}</p>
        </div>

        @php
        $featureIcons = [
            'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
            'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z',
            'M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11',
            'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
            'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7',
            'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        ];
        $features = collect(__('site.home.feature_cards'))->map(function ($item, $i) use ($featureIcons) {
            return ['icon' => $featureIcons[$i] ?? $featureIcons[0], 'title' => $item['title'], 'desc' => $item['desc']];
        });
        @endphp

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($features as $i => $feat)
            <div class="group bg-white rounded-2xl p-6 border border-gray-100 hover:border-[#1a5632]/30 hover:shadow-xl transition-all duration-300 reveal"
                 style="transition-delay:{{ ($i % 3) * 100 }}ms;">
                <div class="feat-icon w-12 h-12 rounded-xl bg-[#1a5632]/8 flex items-center justify-center mb-5 group-hover:bg-[#1a5632] transition-colors duration-300">
                    <svg class="w-6 h-6 text-[#1a5632] group-hover:text-[#e2a024] transition-colors duration-300"
                         fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $feat['icon'] }}"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-[#0b2415] mb-2">{{ $feat['title'] }}</h3>
                <p class="text-sm text-[#6b7c72] leading-relaxed">{{ $feat['desc'] }}</p>
            </div>
            @endforeach
        </div>

    </div>
</section>


{{-- ═══════════════════════════════════════════════════════
     PRINCIPAL MESSAGE
═══════════════════════════════════════════════════════ --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative bg-[#0b2415] rounded-[2rem] overflow-hidden shadow-2xl reveal">
            <div class="absolute inset-0 opacity-[0.04]"
                 style="background-image:radial-gradient(circle at 2px 2px,white 1px,transparent 0);background-size:28px 28px;"></div>
            <div class="absolute top-0 right-0 w-[45%] h-full bg-gradient-to-bl from-[#1a5632] to-transparent opacity-50"></div>
            <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-[#e2a024]/10 rounded-full blur-3xl"></div>

            <div class="relative grid lg:grid-cols-5 z-10">
                <div class="lg:col-span-2 relative min-h-[260px] lg:min-h-0">
                    <img src="{{ $siteSettings->imageUrl('home_principal_image', 'assets/image/default-placeholder.jpg') }}" alt="{{ $siteSettings->get('principal_name') }}"
                         class="absolute inset-0 w-full h-full object-cover object-top" loading="lazy">
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent to-[#0b2415] hidden lg:block"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-[#0b2415] to-transparent lg:hidden"></div>
                </div>

                <div class="lg:col-span-3 p-7 lg:p-12 flex flex-col justify-center">
                    <p class="text-[#e2a024] font-bold text-xs uppercase tracking-[.25em] mb-3">{{ __('site.home.leader_word') }}</p>
                    <h2 class="text-2xl lg:text-3xl font-bold text-white mb-6" style="font-family:var(--ff-head);">
                        {{ $siteSettings->localized('principal_message', __('site.home.principal_message')) }}
                    </h2>
                    <div class="relative pl-6 mb-7">
                        <div class="absolute top-0 left-0 text-6xl text-[#e2a024]/30 leading-none font-serif">"</div>
                        <blockquote class="text-green-100/85 leading-loose text-base italic">
                            {{ $siteSettings->localized('principal_quote', __('site.home.principal_quote')) }}
                        </blockquote>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-[#1a5632] flex items-center justify-center text-[#e2a024] font-bold text-lg border-2 border-[#e2a024]/40 shrink-0">{{ $siteSettings->get('principal_initials', 'P') }}</div>
                        <div>
                            <p class="font-bold text-white">{{ $siteSettings->get('principal_name') }}</p>
                            <p class="text-sm text-green-200/60 font-medium">{{ $siteSettings->localized('principal_role', __('site.home.principal_role')) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════════════════
     TESTIMONIALS
═══════════════════════════════════════════════════════ --}}
@if(isset($testimonials) && $testimonials->count() > 0)
<section class="py-20 bg-white" aria-label="Parent Testimonials">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14 reveal">
            <p class="text-[#e2a024] font-bold text-xs uppercase tracking-widest mb-3">Community Voices</p>
            <h2 class="text-3xl lg:text-4xl font-bold text-[#0b2415]" style="font-family:var(--ff-head);">What Parents & Students Say</h2>
            <div class="w-16 h-1 bg-[#e2a024] mx-auto mt-4 rounded-full"></div>
        </div>

        <div class="flex overflow-x-auto snap-x snap-mandatory hide-sb gap-5 pb-6 -mx-4 px-4 sm:mx-0 sm:px-0">
            @foreach($testimonials as $test)
            @php $initials = collect(explode(' ', $test->name))->map(fn($n) => substr($n, 0, 1))->take(2)->implode(''); @endphp
            <div class="min-w-[88vw] sm:min-w-[360px] md:min-w-[calc(50%-10px)] lg:min-w-[calc(33.333%-14px)] snap-center">
                <div class="bg-[#fdfbf7] border border-gray-100 rounded-3xl p-6 shadow-sm hover:shadow-lg transition-shadow duration-300 h-full flex flex-col">
                    <div class="flex gap-1 mb-5">
                        @for($s = 0; $s < 5; $s++)
                        <svg class="w-4 h-4 text-[#e2a024]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        @endfor
                    </div>
                    <p class="text-gray-700 leading-relaxed mb-6 italic flex-grow text-sm">"{{ $test->content }}"</p>
                    <div class="flex items-center gap-3 mt-auto">
                        <div class="w-10 h-10 rounded-full bg-[#1a5632] flex items-center justify-center text-[#e2a024] font-bold text-sm shrink-0">
                            {{ strtoupper($initials) }}
                        </div>
                        <div>
                            <p class="font-bold text-[#0b2415] text-sm">{{ $test->name }}</p>
                            <p class="text-xs font-medium text-gray-500">{{ $test->role }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif


{{-- ═══════════════════════════════════════════════════════
     CTA BANNER
═══════════════════════════════════════════════════════ --}}
<section class="py-16 bg-[#fdfbf7]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 reveal">
        <div class="rounded-3xl border border-[#1a5632]/10 bg-white px-6 py-10 shadow-sm sm:px-10 lg:flex lg:items-center lg:justify-between lg:gap-10">
            <div class="max-w-2xl">
                <p class="text-[#e2a024] font-bold text-xs uppercase tracking-[.25em] mb-3">{{ __('site.home.community') }}</p>
                <h2 class="font-bold text-[#0b2415] leading-tight" style="font-family:var(--ff-head);font-size:clamp(1.8rem,3.5vw,3rem);">
                    {{ __('site.home.quality_education') }}
                </h2>
                <p class="mt-4 text-[#65756b] text-base leading-7">
                    {{ __('site.home.admissions_open') }}
                </p>
            </div>
            <div class="mt-7 flex flex-col gap-3 sm:flex-row lg:mt-0 lg:shrink-0">
            <a href="{{ route('admissions') }}"
               class="btn-shimmer inline-flex items-center justify-center gap-2.5 px-7 py-3.5 bg-[#1a5632] text-white font-bold rounded-xl text-base hover:bg-[#0b2415] transition-all duration-300 shadow-sm">
                {{ __('site.home.apply_now') }}
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
            <a href="{{ route('contact') }}"
               class="inline-flex items-center justify-center gap-2.5 px-7 py-3.5 border border-[#1a5632]/20 text-[#1a5632] font-bold rounded-xl text-base hover:bg-[#f4f5f0] transition-all duration-300">
                {{ __('site.home.contact_us') }}
            </a>
            </div>
        </div>
    </div>
</section>
@endif

@endsection

@push('scripts')
<script>
/* ── INTERSECTION OBSERVER → reveal ──────────────────── */
const revealEls = document.querySelectorAll('.reveal, .reveal-left, .reveal-right');
const revealObs = new IntersectionObserver((entries) => {
    entries.forEach(e => {
        if (e.isIntersecting) { e.target.classList.add('visible'); revealObs.unobserve(e.target); }
    });
}, { threshold: 0.10 });
revealEls.forEach(el => revealObs.observe(el));

/* ── HERO PARALLAX ───────────────────────────────────── */
const heroImg = document.getElementById('hero-img');
if (heroImg) {
    window.addEventListener('scroll', () => {
        const y = window.scrollY;
        if (y < window.innerHeight) heroImg.style.transform = `translateY(${y * 0.12}px)`;
    }, { passive: true });
}
</script>
@endpush
