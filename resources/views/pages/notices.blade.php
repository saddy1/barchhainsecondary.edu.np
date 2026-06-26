{{-- resources/views/pages/notices.blade.php --}}
@extends('layouts.app')

@section('title', __('site.notices.page_title'))
@section('meta_description', __('site.notices.meta_desc'))

@section('content')

{{-- ============================================================ --}}
{{-- HERO SECTION --}}
{{-- ============================================================ --}}
<section class="relative py-14 overflow-hidden bg-linear-to-br from-[#0b2415] via-[#1a5632] to-[#0b2415]">
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 30px 30px;"></div>
    <div class="absolute -top-24 -right-24 w-96 h-96 bg-[#e2a024]/20 rounded-full blur-3xl animate-[pulse_6s_infinite]"></div>
    <div class="absolute bottom-0 left-0 w-80 h-80 bg-white/10 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <nav class="flex items-center gap-2 text-green-200 text-sm font-medium mb-5" aria-label="Breadcrumb" data-aos="fade-down">
            <a href="{{ route('home') }}" class="hover:text-[#e2a024] hover:underline transition-colors">{{ __('site.common.home') }}</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-white">{{ __('site.notices.breadcrumb') }}</span>
        </nav>

        <div class="inline-flex items-center gap-2 bg-[#e2a024] text-[#0b2415] font-bold text-sm px-5 py-2 rounded-full mb-4 shadow-lg" data-aos="fade-up" data-aos-delay="50">
            📢 {{ __('site.notices.badge') }}
        </div>

        <h1 class="text-3xl lg:text-4xl xl:text-5xl font-bold text-white mb-3 tracking-tight" data-aos="fade-up" data-aos-delay="100">
            {{ __('site.notices.hero_h1') }}
        </h1>

        <p class="text-green-100/90 text-base md:text-lg font-medium leading-relaxed max-w-xl" data-aos="fade-up" data-aos-delay="150">
            {{ __('site.notices.hero_sub') }}
        </p>
    </div>
</section>

{{-- ============================================================ --}}
{{-- NOTICES BOARD --}}
{{-- ============================================================ --}}
<section class="py-10 bg-[#fdfbf7] relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Category Filter --}}
        <div class="flex flex-wrap items-center gap-2 mb-8 border-b border-gray-200 pb-5" data-aos="fade-up">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest mr-1">{{ __('site.common.filter_by') }}:</span>

            @foreach(collect(['All'])->merge($noticeCategories ?? collect()) as $key)
            <a href="{{ route('notices', ['category' => $key]) }}"
               class="text-xs font-bold px-4 py-2 rounded-lg border transition-all duration-300 {{ $category === $key ? 'bg-[#1a5632] text-white shadow-sm border-[#1a5632]' : 'bg-white text-gray-600 hover:text-[#1a5632] hover:bg-green-50 border-gray-200' }}">
                {{ $key === 'All' ? __('site.notices.filter_all') : $key }}
            </a>
            @endforeach

            {{-- Total count --}}
            <span class="ml-auto text-xs font-semibold text-gray-400">
                {{ $notices->total() }} {{ Str::plural('notice', $notices->total()) }}
            </span>
        </div>

        {{-- Notices Grid --}}
        @if($notices->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($notices as $i => $notice)
            @php
                $nDate = \Carbon\Carbon::parse($notice->created_at);
                $catColor = match($notice->category) {
                    'Admission' => ['bar' => 'bg-[#e2a024]', 'badge' => 'bg-[#e2a024]/10 text-[#b87e15] border-[#e2a024]/25'],
                    'Academic'  => ['bar' => 'bg-blue-500',  'badge' => 'bg-blue-50 text-blue-700 border-blue-200'],
                    'Event'     => ['bar' => 'bg-[#1a5632]', 'badge' => 'bg-[#1a5632]/10 text-[#1a5632] border-[#1a5632]/25'],
                    default     => ['bar' => 'bg-gray-400',  'badge' => 'bg-gray-100 text-gray-600 border-gray-200'],
                };
            @endphp
            <article
                class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:border-[#1a5632]/40 hover:shadow-md transition-all duration-300 group relative overflow-hidden flex flex-col"
                data-aos="fade-up" data-aos-delay="{{ min($i % 3 * 60, 180) }}"
            >
                {{-- Top colour bar --}}
                <div class="h-1 w-full {{ $catColor['bar'] }} shrink-0"></div>

                <div class="p-5 flex flex-col flex-1">
                    {{-- Meta row --}}
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-md border {{ $catColor['badge'] }}">
                            {{ $notice->category ?? 'General' }}
                        </span>
                        {{-- Date block --}}
                        <div class="flex items-center gap-1 text-[11px] font-bold text-gray-400 shrink-0">
                            <span class="text-[#0b2415] text-sm font-black">{{ $nDate->format('d') }}</span>
                            <span>{{ $nDate->format('M Y') }}</span>
                        </div>
                    </div>

                    {{-- Title --}}
                    <a href="{{ route('news.show', $notice->slug) }}"
                       class="block mb-2 group-hover:text-[#1a5632] transition-colors">
                        <h2 class="text-base font-bold text-[#0b2415] leading-snug line-clamp-2">
                            {{ $notice->title }}
                        </h2>
                    </a>

                    {{-- Excerpt --}}
                    <p class="text-gray-500 text-sm leading-relaxed line-clamp-2 flex-1 mb-4">
                        {{ $notice->excerpt ?? Str::limit(strip_tags($notice->content), 120) }}
                    </p>

                    {{-- Footer row --}}
                    <div class="flex items-center justify-between pt-3 border-t border-gray-50">
                        <span class="text-[11px] text-gray-400 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $notice->created_at->diffForHumans() }}
                        </span>
                        <a href="{{ route('news.show', $notice->slug) }}"
                           class="inline-flex items-center gap-1 text-xs font-bold text-[#1a5632] hover:text-[#e2a024] transition-colors group/link">
                            {{ __('site.news.read_more') }}
                            <svg class="w-3.5 h-3.5 group-hover/link:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>
            </article>
            @endforeach
        </div>
        @else
        <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-gray-200">
            <div class="text-4xl mb-4">📭</div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">{{ __('site.notices.empty_title') }}</h3>
            <p class="text-gray-500">{{ __('site.notices.empty_sub') }}</p>
        </div>
        @endif

        {{-- Pagination --}}
        @if($notices->hasPages())
        <div class="mt-10 flex flex-col items-center gap-3" data-aos="fade-up">
            {{-- Page info --}}
            <p class="text-xs text-gray-400 font-medium">
                Showing {{ $notices->firstItem() }}–{{ $notices->lastItem() }} of {{ $notices->total() }} notices
            </p>

            {{-- Links --}}
            <div class="flex items-center gap-1.5 flex-wrap justify-center">
                {{-- Previous --}}
                @if($notices->onFirstPage())
                    <span class="px-3 py-2 rounded-lg text-sm font-semibold text-gray-300 bg-gray-50 border border-gray-100 cursor-not-allowed select-none">← Prev</span>
                @else
                    <a href="{{ $notices->previousPageUrl() }}" class="px-3 py-2 rounded-lg text-sm font-semibold text-gray-600 bg-white border border-gray-200 hover:border-[#1a5632] hover:text-[#1a5632] transition-colors">← Prev</a>
                @endif

                {{-- Page numbers --}}
                @foreach($notices->getUrlRange(1, $notices->lastPage()) as $page => $url)
                    @if($page == $notices->currentPage())
                        <span class="px-3.5 py-2 rounded-lg text-sm font-bold text-white bg-[#1a5632] border border-[#1a5632]">{{ $page }}</span>
                    @elseif(abs($page - $notices->currentPage()) <= 2 || $page == 1 || $page == $notices->lastPage())
                        <a href="{{ $url }}" class="px-3.5 py-2 rounded-lg text-sm font-semibold text-gray-600 bg-white border border-gray-200 hover:border-[#1a5632] hover:text-[#1a5632] transition-colors">{{ $page }}</a>
                    @elseif(abs($page - $notices->currentPage()) == 3)
                        <span class="px-2 py-2 text-sm text-gray-400 select-none">…</span>
                    @endif
                @endforeach

                {{-- Next --}}
                @if($notices->hasMorePages())
                    <a href="{{ $notices->nextPageUrl() }}" class="px-3 py-2 rounded-lg text-sm font-semibold text-gray-600 bg-white border border-gray-200 hover:border-[#1a5632] hover:text-[#1a5632] transition-colors">Next →</a>
                @else
                    <span class="px-3 py-2 rounded-lg text-sm font-semibold text-gray-300 bg-gray-50 border border-gray-100 cursor-not-allowed select-none">Next →</span>
                @endif
            </div>
        </div>
        @endif

    </div>
</section>

@endsection
