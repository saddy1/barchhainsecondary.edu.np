{{-- resources/views/pages/academics-secondary.blade.php --}}
@extends('layouts.app')

@php($page = __('site.academics.secondary'))

@section('title', $page['title'])
@section('meta_description', $page['hero_sub'])
@section('meta_keywords', 'Barchhain Secondary School, secondary education Doti, Grade 11 12 education management, civil engineering diploma, special education')

@section('schema')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "EducationalOrganization",
    "name": "{{ __('site.school_name') }} — {{ $page['h1'] }}",
    "description": "{{ $page['hero_sub'] }}",
    "url": "{{ route('academics.secondary') }}"
}
</script>
@endsection

@section('content')
<section class="relative overflow-hidden bg-[#0b2415] py-20 lg:py-28">
    <div class="absolute inset-0 opacity-10" style="background-image:radial-gradient(circle at 2px 2px,white 1px,transparent 0);background-size:30px 30px;"></div>
    <div class="absolute -right-28 -top-28 h-80 w-80 rounded-full bg-[#e2a024]/20 blur-3xl"></div>
    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <nav class="mb-8 flex items-center gap-2 text-sm font-medium text-green-100/80" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-[#e2a024]">{{ __('site.common.home') }}</a>
            <span>/</span>
            <span class="text-white">{{ __('site.academics.breadcrumb_academics') }}</span>
        </nav>

        <div class="grid gap-12 lg:grid-cols-[1fr_.8fr] lg:items-center">
            <div>
                <p class="mb-4 inline-flex rounded-full bg-[#e2a024] px-5 py-2 text-sm font-bold text-[#0b2415]">{{ $page['sub'] }}</p>
                <h1 class="max-w-3xl text-4xl font-black tracking-tight text-white sm:text-5xl lg:text-6xl">{{ $page['h1'] }}</h1>
                <p class="mt-6 max-w-2xl text-lg leading-8 text-green-50/85">{{ $page['hero_sub'] }}</p>
            </div>
            <div class="rounded-[2rem] border-4 border-white/10 bg-white/5 p-3 shadow-2xl">
                <img src="{{ $siteSettings->imageUrl('academics_secondary_image', 'assets/image/school_building.jpg') }}" alt="{{ $page['h1'] }}" class="h-[320px] w-full rounded-[1.5rem] object-cover">
            </div>
        </div>
    </div>
</section>

<section class="bg-[#fdfbf7] py-18 lg:py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <p class="text-sm font-bold uppercase tracking-[.25em] text-[#e2a024]">{{ __('site.academics.overview') }}</p>
            <h2 class="mt-3 text-3xl font-black text-[#0b2415]">{{ __('site.academics.program_features') }}</h2>
            <p class="mt-5 text-base leading-8 text-[#516257]">{{ $page['overview'] }}</p>
        </div>

        <div class="mt-10 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach($page['programs'] as $program)
                <article class="rounded-2xl border border-green-900/10 bg-white p-6 shadow-sm">
                    <div class="mb-5 h-12 w-12 rounded-2xl bg-[#1a5632]/10"></div>
                    <h3 class="text-xl font-bold text-[#0b2415]">{{ $program['title'] }}</h3>
                    <p class="mt-3 text-sm leading-7 text-[#65756b]">{{ $program['text'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-white py-18 lg:py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-[2rem] bg-[#0b2415] p-6 sm:p-8 lg:p-10">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[.25em] text-[#e2a024]">{{ __('site.academics.learning_focus') }}</p>
                    <h2 class="mt-3 text-2xl font-black text-white">{{ __('site.academics.subjects') }}</h2>
                </div>
                <a href="{{ route('admissions') }}" class="inline-flex items-center justify-center rounded-xl bg-[#e2a024] px-5 py-3 text-sm font-bold text-[#0b2415] hover:bg-[#f5c355]">
                    {{ __('site.academics.cta_admissions') }}
                </a>
            </div>
            <div class="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($page['focus'] as $focus)
                    <div class="rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-semibold text-green-50">{{ $focus }}</div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="bg-[#f4f5f0] py-16">
    <div class="mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8">
        <h2 class="text-3xl font-black text-[#0b2415]">{{ __('site.academics.cta_title') }}</h2>
        <p class="mt-4 text-[#65756b]">{{ __('site.academics.cta_text') }}</p>
        <a href="{{ route('contact') }}" class="mt-7 inline-flex rounded-xl bg-[#1a5632] px-6 py-3 text-sm font-bold text-white hover:bg-[#0b2415]">{{ __('site.home.contact_us') }}</a>
    </div>
</section>
@endsection
