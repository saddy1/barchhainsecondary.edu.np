@extends('layouts.app')

@section('title', __('site.about.page_title'))
@section('meta_description', __('site.about.meta_desc'))

@section('content')

<section class="relative overflow-hidden bg-[#0b2415] pt-28 pb-20">
    <div class="absolute inset-0 opacity-[0.08]" style="background-image:radial-gradient(circle at 2px 2px, white 1px, transparent 0);background-size:32px 32px;"></div>
    <div class="absolute -right-24 -top-24 h-80 w-80 rounded-full bg-[#e2a024]/20 blur-3xl"></div>
    <div class="absolute -bottom-32 -left-32 h-96 w-96 rounded-full bg-[#1a5632]/70 blur-3xl"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="mb-8 flex items-center gap-2 text-sm font-semibold text-green-100/75">
            <a href="{{ route('home') }}" class="hover:text-[#e2a024]">{{ __('site.common.home') }}</a>
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-white">{{ __('site.about.breadcrumb') }}</span>
        </nav>

        <div class="grid lg:grid-cols-[1.1fr_.9fr] gap-12 items-center">
            <div>
                <p class="mb-4 inline-flex items-center gap-2 rounded-full border border-[#e2a024]/40 bg-[#e2a024]/10 px-4 py-2 text-sm font-bold text-[#e2a024]">
                    <span class="h-2 w-2 rounded-full bg-[#e2a024]"></span>
                    {{ __('site.about.hero_badge') }}
                </p>
                <h1 class="text-4xl md:text-6xl font-bold leading-tight text-white" style="font-family:var(--ff-head);">
                    {{ __('site.about.hero_h1') }}
                </h1>
                <p class="mt-6 max-w-2xl text-lg leading-relaxed text-green-100/85">
                    {{ __('site.about.hero_sub') }}
                </p>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/10 p-5 shadow-2xl backdrop-blur">
                <div class="rounded-2xl bg-white p-6">
                    <div class="mb-5 flex items-center gap-4">
                        <div class="h-16 w-16 rounded-2xl bg-gray-50 p-2 shadow-inner">
                            <img src="{{ $siteSettings->logoUrl() }}" alt="{{ $siteSettings->localized('site_name', __('site.school_name')) }}" class="h-full w-full object-contain">
                        </div>
                        <div>
                            <p class="text-xl font-extrabold text-[#0b2415]">{{ $siteSettings->localized('site_name', __('site.school_name')) }}</p>
                            <p class="text-sm font-semibold text-gray-500">{{ __('site.about.official_name') }}</p>
                        </div>
                    </div>

                    <dl class="grid grid-cols-2 gap-3 text-sm">
                        @foreach(__('site.about.fact_cards') as $item)
                            <div class="rounded-2xl border border-gray-100 bg-[#fdfbf7] p-4">
                                <dt class="text-xs font-bold uppercase tracking-wider text-gray-400">{{ $item['label'] }}</dt>
                                <dd class="mt-1 font-extrabold text-[#1a5632]">{{ $item['value'] }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid gap-6 md:grid-cols-3">
            @foreach(__('site.about.identity_cards') as $card)
                <article class="rounded-3xl border border-gray-100 bg-white p-7 shadow-sm transition hover:-translate-y-1 hover:border-[#1a5632]/40 hover:shadow-xl">
                    <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-[#1a5632]/10 text-2xl">{{ $card['icon'] }}</div>
                    <h2 class="mb-3 text-xl font-extrabold text-[#0b2415]">{{ $card['title'] }}</h2>
                    <p class="text-sm leading-7 text-gray-600">{{ $card['text'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-[#fdfbf7] py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid gap-12 lg:grid-cols-[.95fr_1.05fr] lg:items-start">
            <div class="lg:sticky lg:top-32">
                <p class="mb-3 text-xs font-extrabold uppercase tracking-[.28em] text-[#e2a024]">{{ __('site.about.history_label') }}</p>
                <h2 class="text-3xl md:text-5xl font-bold leading-tight text-[#0b2415]" style="font-family:var(--ff-head);">
                    {{ __('site.about.history_title') }}
                </h2>
                <p class="mt-5 text-lg leading-8 text-gray-600">{{ __('site.about.history_intro') }}</p>
            </div>

            <div class="space-y-5">
                @foreach(__('site.about.timeline') as $event)
                    <div class="grid gap-4 rounded-3xl border border-gray-100 bg-white p-6 shadow-sm sm:grid-cols-[110px_1fr]">
                        <div class="text-2xl font-black text-[#1a5632]">{{ $event['year'] }}</div>
                        <div>
                            <h3 class="font-extrabold text-[#0b2415]">{{ $event['title'] }}</h3>
                            <p class="mt-2 text-sm leading-7 text-gray-600">{{ $event['text'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="bg-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12 max-w-3xl">
            <p class="mb-3 text-xs font-extrabold uppercase tracking-[.28em] text-[#e2a024]">{{ __('site.about.program_label') }}</p>
            <h2 class="text-3xl md:text-5xl font-bold text-[#0b2415]" style="font-family:var(--ff-head);">{{ __('site.about.program_title') }}</h2>
            <p class="mt-5 text-lg leading-8 text-gray-600">{{ __('site.about.program_intro') }}</p>
        </div>

        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
            @foreach(__('site.about.programs') as $program)
                <article class="rounded-3xl border border-gray-100 bg-[#fdfbf7] p-6">
                    <div class="mb-4 text-3xl">{{ $program['icon'] }}</div>
                    <h3 class="font-extrabold text-[#0b2415]">{{ $program['title'] }}</h3>
                    <p class="mt-3 text-sm leading-7 text-gray-600">{{ $program['text'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-[#0b2415] py-20 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach(__('site.about.stats') as $stat)
                <div class="rounded-3xl border border-white/10 bg-white/10 p-7 text-center backdrop-blur">
                    <div class="text-4xl font-black text-[#e2a024]">{{ $stat['value'] }}</div>
                    <div class="mt-2 text-sm font-bold text-green-100/80">{{ $stat['label'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-[.8fr_1.2fr]">
            <div class="rounded-3xl bg-[#1a5632] p-8 text-white">
                <p class="text-sm font-bold uppercase tracking-[.25em] text-[#e2a024]">{{ __('site.about.vision_label') }}</p>
                <h2 class="mt-4 text-3xl font-bold" style="font-family:var(--ff-head);">{{ __('site.about.vision_title') }}</h2>
                <p class="mt-5 leading-8 text-green-100/90">{{ __('site.about.vision_text') }}</p>
            </div>
            <div class="rounded-3xl border border-gray-100 bg-[#fdfbf7] p-8">
                <p class="text-sm font-bold uppercase tracking-[.25em] text-[#e2a024]">{{ __('site.about.goals_label') }}</p>
                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    @foreach(__('site.about.goals') as $goal)
                        <div class="rounded-2xl bg-white p-5 shadow-sm">
                            <p class="text-sm font-bold leading-7 text-gray-700">{{ $goal }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-[#fdfbf7] py-20">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="rounded-[2rem] border border-gray-100 bg-white p-8 md:p-12 shadow-sm">
            <p class="mb-3 text-xs font-extrabold uppercase tracking-[.28em] text-[#e2a024]">{{ __('site.about.leadership_label') }}</p>
            <h2 class="text-3xl md:text-4xl font-bold text-[#0b2415]" style="font-family:var(--ff-head);">{{ $siteSettings->localized('principal_message', __('site.about.principal_h2')) }}</h2>
            <blockquote class="mt-6 border-l-4 border-[#e2a024] pl-6 text-lg italic leading-9 text-gray-700">
                {{ $siteSettings->localized('principal_quote', __('site.about.principal_quote')) }}
            </blockquote>
            <div class="mt-8 flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-[#1a5632] text-lg font-black text-[#e2a024]">{{ $siteSettings->get('principal_initials', 'P') }}</div>
                <div>
                    <p class="font-extrabold text-[#0b2415]">{{ $siteSettings->get('principal_name') }}</p>
                    <p class="text-sm font-semibold text-gray-500">{{ $siteSettings->localized('principal_role', __('site.about.principal_role')) }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-[#1a5632] py-16">
    <div class="max-w-4xl mx-auto px-4 text-center text-white">
        <h2 class="text-3xl md:text-4xl font-bold" style="font-family:var(--ff-head);">{{ __('site.about.cta_h2') }}</h2>
        <p class="mx-auto mt-4 max-w-2xl leading-8 text-green-100/90">{{ __('site.about.cta_p') }}</p>
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="{{ route('admissions') }}" class="inline-flex items-center rounded-2xl bg-[#e2a024] px-7 py-3 font-extrabold text-[#0b2415] transition hover:bg-white">
                {{ __('site.about.cta_btn') }}
            </a>
            <a href="{{ route('contact') }}" class="inline-flex items-center rounded-2xl border border-white/25 px-7 py-3 font-extrabold text-white transition hover:bg-white/10">
                {{ __('site.nav.contact') }}
            </a>
        </div>
    </div>
</section>

@endsection
