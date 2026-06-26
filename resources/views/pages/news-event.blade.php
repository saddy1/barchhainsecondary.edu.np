@extends('layouts.app')

@section('title', __('site.news.page_title'))
@section('meta_description', __('site.news.meta_desc'))

@section('content')

{{-- ============================================================ --}}
{{-- HERO SECTION --}}
{{-- ============================================================ --}}
<section class="relative py-16 md:py-24 overflow-hidden bg-gradient-to-br from-[#0b2415] via-[#1a5632] to-[#0b2415]">
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 30px 30px;"></div>
    <div class="absolute -top-24 -right-24 w-96 h-96 bg-[#e2a024]/20 rounded-full blur-3xl animate-[pulse_6s_infinite]"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <nav class="flex items-center gap-2 text-green-200 text-xs font-medium mb-6">
            <a href="{{ route('home') }}" class="hover:text-[#e2a024]">{{ __('site.common.home') }}</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-white">{{ __('site.news.breadcrumb') }}</span>
        </nav>

        <div class="inline-flex items-center gap-2 bg-[#e2a024] text-[#0b2415] font-bold text-xs px-4 py-1.5 rounded-full mb-4 shadow-lg">
            📰 {{ __('site.news.badge') }}
        </div>

        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-4 tracking-tight">{{ __('site.news.hero_h1') }}</h1>
        <p class="text-green-100/90 text-sm sm:text-base md:text-lg font-medium leading-relaxed max-w-2xl">
            {{ __('site.news.hero_sub') }}
        </p>
    </div>
</section>

{{-- ============================================================ --}}
{{-- EVENTS SECTION (Upcoming Split + Past Grid) --}}
{{-- ============================================================ --}}
<section class="py-8 md:py-10 bg-[#fdfbf7] border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="flex items-end justify-between mb-4">
            <div>
                <p class="text-[#e2a024] font-bold text-xs uppercase tracking-widest mb-1 flex items-center gap-2">
                    <span class="w-4 h-0.5 bg-[#e2a024]"></span> Calendar
                </p>
                <h2 class="text-xl md:text-2xl font-bold text-[#0b2415]">{{ __('site.news.events_h2') }}</h2>
            </div>
        </div>

        @if($upcomingEvents->isNotEmpty())
            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                @foreach($upcomingEvents as $event)
                    @php $date = $event->event_date ? \Carbon\Carbon::parse($event->event_date) : null; @endphp
                    <a href="{{ route('notices.show', $event->slug) }}" class="group flex items-center gap-3 rounded-xl border border-gray-100 bg-white p-3 shadow-sm transition-colors hover:border-[#1a5632]/40">
                        <div class="flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-lg bg-[#1a5632] text-white">
                            <span class="text-base font-black leading-none">{{ $date ? $date->format('d') : '-' }}</span>
                            <span class="mt-0.5 text-[8px] font-bold uppercase tracking-widest text-green-100">{{ $date ? $date->format('M') : 'TBA' }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="mb-1 flex items-center gap-2 text-[10px] font-bold text-gray-500">
                                <span class="rounded bg-green-50 px-1.5 py-0.5 text-[#1a5632]">{{ $event->category ?: 'Event' }}</span>
                                <span class="truncate">{{ $event->event_time ?? 'TBA' }}</span>
                            </div>
                            <h3 class="truncate text-sm font-black text-[#0b2415] group-hover:text-[#1a5632]">{{ $event->title }}</h3>
                            <p class="mt-1 truncate text-xs text-gray-500">{{ $event->event_location ?: 'School Campus' }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="rounded-xl border border-dashed border-gray-200 bg-white py-8 text-center">
                <h3 class="text-base font-bold text-gray-800">{{ __('site.news.no_events') }}</h3>
                <p class="text-gray-500 text-xs">Check back later for new schedules.</p>
            </div>
        @endif

        @if($pastEvents->isNotEmpty())
        <div class="mt-6 border-t border-gray-200/70 pt-5">
            <h3 class="mb-3 text-base font-black text-[#0b2415]">Recent Highlights</h3>
            
            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                @foreach($pastEvents as $past)
                    @php $pastDate = $past->event_date ? \Carbon\Carbon::parse($past->event_date) : null; @endphp
                    <a href="{{ route('notices.show', $past->slug) }}" class="group flex items-center gap-3 rounded-xl border border-gray-100 bg-white p-3 transition-colors hover:border-[#1a5632]/30 hover:bg-green-50/30">
                        <div class="flex h-11 w-11 shrink-0 flex-col items-center justify-center rounded-lg border border-gray-200 bg-gray-50 text-center">
                            <span class="text-sm font-black leading-none text-[#0b2415]">{{ $pastDate ? $pastDate->format('d') : '-' }}</span>
                            <span class="mt-0.5 text-[8px] font-bold uppercase tracking-widest text-gray-500">{{ $pastDate ? $pastDate->format('M') : 'Past' }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="mb-1 flex items-center gap-2 text-[10px] font-bold text-gray-500">
                                <span class="rounded bg-gray-100 px-1.5 py-0.5">{{ $past->category ?: 'Event' }}</span>
                                <span>{{ $pastDate ? $pastDate->format('Y') : '' }}</span>
                            </div>
                            <h4 class="truncate text-sm font-bold text-[#0b2415] group-hover:text-[#1a5632]">{{ $past->title }}</h4>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>

{{-- ============================================================ --}}
{{-- NEWS / NOTICE BOARD --}}
{{-- ============================================================ --}}
<section id="notice-board" class="py-8 md:py-10 bg-white relative scroll-mt-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-5 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <p class="mb-2 text-xs font-black uppercase tracking-[.22em] text-[#1a5632]">Updates</p>
                <h2 class="text-xl md:text-2xl font-black text-[#0b2415]">News, notices, results and resources</h2>
            </div>
            <a href="{{ route('notices') }}" class="inline-flex w-full items-center justify-center rounded-lg border border-[#1a5632]/20 px-4 py-2 text-sm font-bold text-[#1a5632] hover:bg-green-50 sm:w-auto">
                Notice Archive
                <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>

        {{-- Filters --}}
        <div class="mb-5 flex flex-wrap items-center gap-2 rounded-xl border border-gray-100 bg-[#f7faf8] p-2.5">
            <a href="{{ route('news', ['category' => 'All']) }}"
                    class="text-xs font-bold px-3.5 py-1.5 rounded-full border transition-colors {{ ($category ?? 'All') === 'All' ? 'bg-[#1a5632] text-white border-[#1a5632]' : 'bg-white text-gray-600 hover:bg-green-50 border-gray-200' }}">
                All
            </a>
            @foreach($noticeCategories as $noticeCategory)
            <a href="{{ route('news', ['category' => $noticeCategory]) }}"
                    class="text-xs font-bold px-3.5 py-1.5 rounded-full border transition-colors {{ ($category ?? 'All') === $noticeCategory ? 'bg-[#1a5632] text-white border-[#1a5632]' : 'bg-white text-gray-600 hover:bg-green-50 border-gray-200' }}">
                {{ $noticeCategory }}
            </a>
            @endforeach
        </div>

        {{-- Two Column List --}}
        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
            @forelse($notices as $notice)
            @php
                $noticeDate = $notice->created_at;
            @endphp
            <a href="{{ route('notices.show', $notice->slug) }}"
                class="group flex items-center gap-3 rounded-xl border border-gray-100 bg-white p-3 shadow-sm transition-colors hover:border-[#1a5632]/40 hover:bg-green-50/30"
            >
                <div class="flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-lg border border-gray-200 bg-[#f7faf8] text-center group-hover:border-[#1a5632]/30">
                    <span class="text-base font-black leading-none text-[#0b2415]">{{ $noticeDate->format('d') }}</span>
                    <span class="mt-0.5 text-[8px] font-black uppercase tracking-widest text-[#1a5632]">{{ $noticeDate->format('M') }}</span>
                </div>

                <div class="min-w-0 flex-1">
                    <div class="mb-1 flex items-center gap-2">
                        <span class="rounded bg-[#1a5632]/10 px-1.5 py-0.5 text-[10px] font-black uppercase tracking-wider text-[#1a5632]">{{ $notice->category ?: 'General' }}</span>
                        <span class="hidden text-[10px] font-semibold text-gray-400 sm:inline">{{ $noticeDate->diffForHumans() }}</span>
                    </div>
                    <h3 class="truncate text-sm font-black text-[#0b2415] transition-colors group-hover:text-[#1a5632]">
                        {{ $notice->title }}
                    </h3>
                    <p class="mt-1 line-clamp-1 text-xs text-gray-500">
                        {{ $notice->excerpt ?: Str::limit(strip_tags($notice->content), 90) }}
                    </p>
                </div>
                <svg class="h-4 w-4 shrink-0 text-gray-300 group-hover:text-[#1a5632]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
            @empty
            <div class="md:col-span-2 rounded-xl border border-dashed border-gray-200 bg-[#f7faf8] py-10 text-center">
                <svg class="mx-auto mb-3 h-10 w-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h8M8 11h8M8 15h5M6 3h9l3 3v15H6V3z"/></svg>
                <p class="text-sm font-bold text-gray-500">No posts found for this category.</p>
            </div>
            @endforelse
            
        </div>

        {{-- Actions --}}
        <div class="mt-8 flex flex-col items-center justify-between gap-4 sm:flex-row">
            <div class="w-full sm:w-auto overflow-x-auto">
                {{ $notices->links('pagination::tailwind') }}
            </div>
        </div>
        
    </div>
</section>

{{-- ============================================================ --}}
{{-- NEWSLETTER / CTA --}}
{{-- ============================================================ --}}
<section class="py-16 bg-[#234024] relative overflow-hidden">
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 30px 30px;"></div>
    
    <div class="max-w-xl mx-auto px-4 text-center relative z-10">
        <h2 class="text-2xl font-bold text-white mb-2">Never Miss an Update</h2>
        <p class="text-sm text-green-100/90 mb-6">Subscribe to receive academic news and event reminders.</p>
        
        <form action="#" method="POST" class="flex flex-col sm:flex-row gap-2">
            @csrf
            <input type="email" required placeholder="Email address" class="flex-grow px-4 py-2.5 bg-[#273751] border border-white/20 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-[#e2a024]" />
            <button type="submit" class="px-6 py-2.5 bg-[#e2a024] text-[#0b2415] rounded-lg hover:bg-[#f4b63e] font-bold text-sm transition-colors">Subscribe</button>
        </form>
    </div>
</section>

@endsection

@section('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection
