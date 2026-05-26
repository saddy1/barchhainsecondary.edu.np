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
<section class="py-12 md:py-20 bg-[#fdfbf7] border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="flex items-end justify-between mb-6 md:mb-8">
            <div>
                <p class="text-[#e2a024] font-bold text-xs uppercase tracking-widest mb-1 flex items-center gap-2">
                    <span class="w-4 h-0.5 bg-[#e2a024]"></span> Calendar
                </p>
                <h2 class="text-2xl md:text-3xl font-bold text-[#0b2415]">{{ __('site.news.events_h2') }}</h2>
            </div>
            <a href="#" class="hidden sm:inline-flex items-center gap-1 text-xs font-bold text-[#1a5632] hover:text-[#e2a024] transition-colors bg-green-50 px-3 py-1.5 rounded-lg">
                View Calendar <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        {{-- UPCOMING EVENTS --}}
        @if($upcomingEvents->isNotEmpty())
            @php
                $featuredEvent = $upcomingEvents->first();
                $otherEvents = $upcomingEvents->skip(1)->take(4);
                $featDate = $featuredEvent->event_date ? \Carbon\Carbon::parse($featuredEvent->event_date) : null;
                $featImg = str_starts_with($featuredEvent->featured_image ?? '', 'http') ? $featuredEvent->featured_image : asset($featuredEvent->featured_image);
            @endphp

            <div class="grid lg:grid-cols-12 gap-4 lg:gap-6 items-stretch">
                
                {{-- Featured Event (Left) --}}
                <div class="lg:col-span-5 h-[300px] lg:h-[420px]">
                    <a href="{{ route('notices.show', $featuredEvent->slug) }}" class="group block relative w-full h-full rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all border border-gray-100">
                        
                        @if($featuredEvent->featured_image)
                            <img src="{{ $featImg }}" alt="{{ $featuredEvent->title }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        @else
                            <div class="absolute inset-0 bg-[#1a5632] flex items-center justify-center">
                                <svg class="w-16 h-16 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif
                        
                        <div class="absolute inset-0 bg-gradient-to-t from-[#0b2415] via-[#0b2415]/50 to-transparent"></div>

                        @if($featDate)
                        <div class="absolute top-4 left-4 bg-white/95 backdrop-blur-md text-center px-3 py-1.5 rounded-lg shadow-sm">
                            <span class="block text-xl font-black text-[#0b2415] leading-none">{{ $featDate->format('d') }}</span>
                            <span class="block text-[9px] font-bold uppercase tracking-widest text-[#1a5632]">{{ $featDate->format('M Y') }}</span>
                        </div>
                        @endif

                        <div class="absolute bottom-0 left-0 w-full p-4 sm:p-6">
                            <span class="inline-block text-[9px] font-bold uppercase tracking-widest bg-[#e2a024] text-[#0b2415] px-2 py-0.5 rounded mb-2">
                                {{ $featuredEvent->category ?? 'Event' }}
                            </span>
                            <h3 class="text-xl sm:text-2xl font-bold text-white mb-2 leading-tight group-hover:text-[#e2a024] transition-colors line-clamp-2">
                                {{ $featuredEvent->title }}
                            </h3>
                            <div class="flex flex-wrap gap-x-3 gap-y-1 text-xs text-green-50/80 font-medium">
                                <span class="flex items-center gap-1"><svg class="w-3 h-3 text-[#e2a024]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> {{ $featuredEvent->event_time ?? 'TBA' }}</span>
                                <span class="flex items-center gap-1"><svg class="w-3 h-3 text-[#e2a024]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg> {{ $featuredEvent->event_location ?? 'Campus' }}</span>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- Compact List (Right) --}}
                <div class="lg:col-span-7 flex flex-col gap-3">
                    @forelse($otherEvents as $event)
                        @php
                            $date = $event->event_date ? \Carbon\Carbon::parse($event->event_date) : null;
                        @endphp
                        <a href="{{ route('notices.show', $event->slug) }}" class="group flex items-center gap-3 sm:gap-4 p-3 bg-white rounded-xl shadow-sm border border-gray-100 hover:border-[#1a5632]/40 transition-colors">
                            <div class="shrink-0 w-12 h-12 sm:w-14 sm:h-14 rounded-lg bg-gray-50 border border-gray-100 flex flex-col items-center justify-center group-hover:bg-[#1a5632] transition-colors">
                                <span class="text-lg font-black text-[#0b2415] leading-none group-hover:text-white">{{ $date ? $date->format('d') : '-' }}</span>
                                <span class="text-[8px] font-bold uppercase tracking-widest text-gray-500 group-hover:text-green-200 mt-0.5">{{ $date ? $date->format('M') : 'TBA' }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm sm:text-base font-bold text-[#0b2415] truncate group-hover:text-[#1a5632] transition-colors">{{ $event->title }}</h4>
                                <div class="flex items-center gap-2 text-[10px] sm:text-xs text-gray-500 font-medium mt-1 truncate">
                                    <span class="bg-gray-100 px-1.5 py-0.5 rounded">{{ $event->category }}</span>
                                    <span>• {{ $event->event_time ?? 'TBA' }}</span>
                                </div>
                            </div>
                            <svg class="hidden sm:block w-4 h-4 text-gray-300 group-hover:text-[#e2a024] shrink-0 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @empty
                        <div class="flex-1 flex flex-col items-center justify-center p-6 bg-white rounded-xl border border-dashed border-gray-200">
                            <span class="text-2xl mb-1">🎉</span>
                            <p class="text-gray-500 text-xs font-medium">No more upcoming events right now.</p>
                        </div>
                    @endforelse
                    
                    <a href="#" class="sm:hidden mt-2 text-center w-full py-2 bg-white border border-gray-200 text-[#0b2415] font-bold text-xs rounded-lg shadow-sm">View Calendar &rarr;</a>
                </div>
            </div>
        @else
            <div class="text-center py-10 bg-white border border-gray-100 rounded-2xl shadow-sm">
                <div class="text-3xl mb-2">📅</div>
                <h3 class="text-base font-bold text-gray-800">{{ __('site.news.no_events') }}</h3>
                <p class="text-gray-500 text-xs">Check back later for new schedules.</p>
            </div>
        @endif

        {{-- PAST EVENTS / HIGHLIGHTS (Dense Grid) --}}
        @if($pastEvents->isNotEmpty())
        <div class="mt-10 pt-8 border-t border-gray-200/60">
            <h3 class="text-lg md:text-xl font-bold text-[#0b2415] mb-4">Recent Highlights</h3>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
                @foreach($pastEvents as $past)
                    @php
                        $pastDate = $past->event_date ? \Carbon\Carbon::parse($past->event_date) : null;
                        $pastImg = str_starts_with($past->featured_image ?? '', 'http') ? $past->featured_image : asset($past->featured_image);
                    @endphp
                    <a href="{{ route('notices.show', $past->slug) }}" class="group block bg-white rounded-xl border border-gray-100 overflow-hidden hover:shadow-md transition-all">
                        <div class="h-24 sm:h-28 overflow-hidden relative bg-gray-100">
                            @if($past->featured_image)
                                <img src="{{ $pastImg }}" alt="{{ $past->title }}" class="w-full h-full object-cover grayscale-[30%] group-hover:grayscale-0 group-hover:scale-105 transition-all duration-300">
                            @endif
                            <div class="absolute top-2 left-2 bg-black/60 backdrop-blur-sm px-1.5 py-0.5 rounded text-[8px] font-bold uppercase tracking-widest text-white">Past</div>
                        </div>
                        <div class="p-3">
                            <p class="text-[10px] font-bold text-gray-400 mb-0.5">{{ $pastDate ? $pastDate->format('M d, Y') : '' }}</p>
                            <h4 class="text-xs sm:text-sm font-bold text-[#0b2415] group-hover:text-[#1a5632] line-clamp-2 leading-tight">{{ $past->title }}</h4>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>

{{-- ============================================================ --}}
{{-- COMPACT NOTICE BOARD --}}
{{-- ============================================================ --}}
<section id="notice-board" class="py-12 md:py-20 bg-white relative scroll-mt-24">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ activeCategory: 'All' }">

        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-6 md:mb-8">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-[#0b2415]">Notice Board</h2>
                <div class="w-12 h-1 bg-[#1a5632] mt-2 rounded-full"></div>
            </div>
        </div>

        {{-- Compact Filters --}}
        <div class="flex flex-wrap items-center gap-2 mb-6">
            <button @click="activeCategory = 'All'"
                    :class="activeCategory === 'All' ? 'bg-[#1a5632] text-white border-[#1a5632]' : 'bg-gray-50 text-gray-600 hover:bg-green-50 border-gray-200'"
                    class="text-xs font-bold px-4 py-1.5 rounded-full border transition-colors">
                All
            </button>
            @foreach($noticeCategories as $category)
            <button @click="activeCategory = '{{ $category }}'"
                    :class="activeCategory === '{{ $category }}' ? 'bg-[#1a5632] text-white border-[#1a5632]' : 'bg-gray-50 text-gray-600 hover:bg-green-50 border-gray-200'"
                    class="text-xs font-bold px-4 py-1.5 rounded-full border transition-colors">
                {{ $category }}
            </button>
            @endforeach
        </div>

        {{-- Dense Notice List --}}
        <div class="flex flex-col gap-2.5 min-h-[300px]">
            @forelse($notices as $notice)
            <a href="{{ route('notices.show', $notice->slug) }}"
                x-show="activeCategory === 'All' || activeCategory === '{{ $notice->category }}'"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="group flex items-center gap-3 sm:gap-4 bg-[#fdfbf7] p-3 rounded-xl border border-gray-100 hover:border-[#1a5632]/40 hover:shadow-sm transition-all"
            >
                {{-- Tiny Date Box --}}
                <div class="shrink-0 flex flex-col items-center justify-center w-12 h-12 bg-white rounded-lg border border-gray-200 shadow-sm group-hover:bg-[#1a5632] group-hover:border-[#1a5632] transition-colors">
                    <span class="text-sm font-black text-[#0b2415] leading-none group-hover:text-white">{{ $notice->created_at->format('d') }}</span>
                    <span class="text-[8px] font-bold uppercase tracking-widest text-gray-500 group-hover:text-green-200 mt-0.5">{{ $notice->created_at->format('M') }}</span>
                </div>

                {{-- Content Block --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="text-[8px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-gray-200/50 text-gray-600">
                            {{ $notice->category }}
                        </span>
                        <span class="text-[10px] text-gray-400 font-medium hidden sm:inline-block">
                            {{ $notice->created_at->diffForHumans() }}
                        </span>
                    </div>
                    <h3 class="text-sm sm:text-base font-bold text-[#0b2415] truncate group-hover:text-[#1a5632] transition-colors">
                        {{ $notice->title }}
                    </h3>
                    {{-- Hidden on very small screens to save space --}}
                    <p class="hidden sm:block text-gray-500 text-xs truncate mt-0.5">
                        {{ Str::limit(strip_tags($notice->content), 80) }}
                    </p>
                </div>
                
                {{-- Arrow --}}
                <div class="shrink-0 text-gray-300 group-hover:text-[#1a5632] transition-colors px-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>
            @empty
            <div class="text-center py-10 bg-white rounded-xl border border-dashed border-gray-200">
                <span class="text-2xl mb-2 block">📭</span>
                <p class="text-gray-500 text-xs font-bold">No official notices published.</p>
            </div>
            @endforelse
            
            {{-- Empty State for Alpine --}}
            <div x-show="activeCategory !== 'All' && !{{ $noticeCategories->toJson() }}.includes(activeCategory)" style="display: none;" class="text-center py-10 bg-[#fdfbf7] rounded-xl border border-dashed border-gray-200">
                <span class="text-2xl mb-2 block">📭</span>
                <p class="text-gray-500 text-xs font-bold">No notices in this category.</p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="w-full sm:w-auto overflow-x-auto">
                {{ $notices->links('pagination::tailwind') }}
            </div>
            <a href="#" class="w-full sm:w-auto text-center px-5 py-2 bg-white border border-gray-200 text-[#0b2415] font-bold text-xs rounded-lg hover:bg-gray-50 shadow-sm transition-colors">
                View Notice Archive &rarr;
            </a>
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