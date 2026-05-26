{{-- resources/views/pages/events.blade.php --}}
@extends('layouts.app')

@section('title', 'Events & Activities — Barchhain Secondary School | Barchhain, Doti')
@section('meta_description', 'View upcoming and past events at Barchhain Secondary School. Sports day, cultural programs, science exhibitions, parent-teacher meetings, and more.')

@section('content')

{{-- ============================================================ --}}
{{-- HERO SECTION --}}
{{-- ============================================================ --}}
<section class="relative py-24 overflow-hidden bg-gradient-to-br from-[#0b2415] via-[#1a5632] to-[#0b2415]">
    {{-- Decorative Background Elements --}}
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 30px 30px;"></div>
    <div class="absolute -top-20 -right-20 w-96 h-96 bg-[#e2a024]/20 rounded-full blur-3xl animate-[pulse_6s_infinite]"></div>
    <div class="absolute bottom-0 left-0 w-80 h-80 bg-white/10 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-green-200 text-sm font-medium mb-8" aria-label="Breadcrumb" data-aos="fade-down">
            <a href="{{ route('home') }}" class="hover:text-[#e2a024] hover:underline transition-colors">Home</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-white">Events</span>
        </nav>
        
        <div class="inline-flex items-center gap-2 bg-[#e2a024] text-[#0b2415] font-bold text-sm px-6 py-2.5 rounded-full mb-6 shadow-lg" data-aos="fade-up" data-aos-delay="50">
            📅 School Calendar
        </div>
        
        <h1 class="text-4xl lg:text-5xl xl:text-6xl font-bold text-white mb-6 tracking-tight" data-aos="fade-up" data-aos-delay="100">
            Events & Activities
        </h1>
        
        <div class="max-w-2xl" data-aos="fade-up" data-aos-delay="150">
            <p class="text-green-100/90 text-lg md:text-xl font-medium leading-relaxed">
                Celebrating learning, creativity, and community through exciting school events, competitions, and gatherings throughout the academic year.
            </p>
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- UPCOMING EVENTS SECTION --}}
{{-- ============================================================ --}}
<section class="py-24 bg-[#fdfbf7] relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section Header --}}
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-12 gap-6 border-b-2 border-gray-100 pb-6" data-aos="fade-up">
            <div>
                <p class="text-[#e2a024] font-bold text-sm uppercase tracking-widest mb-2 flex items-center gap-2">
                    <span class="w-6 h-0.5 bg-[#e2a024]"></span> What's Next
                </p>
                <h2 class="text-3xl lg:text-4xl font-bold text-[#0b2415]">Upcoming Events</h2>
            </div>
            <div class="inline-flex items-center gap-2 text-sm font-bold text-[#1a5632] bg-[#1a5632]/10 px-4 py-2 rounded-lg">
                Current Time: {{ now()->format('M d, Y') }}
            </div>
        </div>

        {{-- Events Grid --}}
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($upcomingEvents as $i => $event)
            @php
                $eDate = \Carbon\Carbon::parse($event->event_date);
            @endphp
            <article onclick="window.location='{{ route('news.show', $event->slug) }}'" class="bg-white rounded-3xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group flex flex-col cursor-pointer" data-aos="fade-up" data-aos-delay="{{ $i * 50 }}">
                
                <div class="relative h-56 overflow-hidden shrink-0">
                    <img src="{{ $event->image_url }}" alt="{{ $event->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" loading="lazy">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#0b2415]/80 via-transparent to-transparent"></div>
                    
                    <span class="absolute top-4 left-4 text-xs font-bold px-3 py-1.5 rounded-md shadow-sm uppercase tracking-wider bg-[#1a5632] text-white">
                        {{ $event->category }}
                    </span>
                    
                    {{-- Floating Date UI --}}
                    <div class="absolute bottom-4 right-4 bg-white/95 backdrop-blur-sm text-[#0b2415] px-3 py-2 rounded-xl text-center shadow-lg transform group-hover:scale-105 transition-transform">
                        <div class="text-lg font-black leading-none">{{ $eDate->format('d') }}</div>
                        <div class="text-[10px] font-bold uppercase tracking-widest text-[#1a5632]">{{ $eDate->format('M') }}</div>
                    </div>
                </div>

                <div class="p-6 flex-1 flex flex-col">
                    <h3 class="text-xl font-bold text-[#0b2415] mb-3 group-hover:text-[#1a5632] transition-colors leading-tight line-clamp-2">{{ $event->title }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed mb-6 flex-1 line-clamp-3">{{ $event->excerpt ?? Str::limit(strip_tags($event->content), 100) }}</p>
                    
                    <div class="space-y-2.5 pt-4 border-t border-gray-50 mt-auto">
                        <div class="flex items-start gap-3 text-sm text-gray-600 font-medium">
                            <svg class="w-5 h-5 text-[#e2a024] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ $event->event_time ?? 'Check Description' }}</span>
                        </div>
                        <div class="flex items-start gap-3 text-sm text-gray-600 font-medium">
                            <svg class="w-5 h-5 text-[#1a5632] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            <span>{{ $event->event_location ?? 'School Campus' }}</span>
                        </div>
                    </div>
                </div>
            </article>
            @empty
                <div class="col-span-full py-12 text-center bg-white rounded-3xl border-2 border-dashed border-gray-200">
                    <p class="text-gray-500 font-medium">No upcoming events scheduled at the moment.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- PAST EVENTS (ARCHIVE) SECTION --}}
{{-- ============================================================ --}}
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10 gap-6" data-aos="fade-up">
            <div>
                <p class="text-[#e2a024] font-bold text-sm uppercase tracking-widest mb-2 flex items-center gap-2">
                    <span class="w-6 h-0.5 bg-[#e2a024]"></span> Archive
                </p>
                <h2 class="text-3xl font-bold text-[#0b2415]">Past Events</h2>
            </div>
            <a href="{{ url('/gallery') }}" class="text-[#1a5632] font-bold hover:text-[#e2a024] transition-colors flex items-center gap-1 group">
                View Photo Gallery
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            @forelse($pastEvents as $i => $past)
            <article onclick="window.location='{{ route('news.show', $past->slug) }}'" class="flex items-center gap-5 bg-[#fdfbf7] rounded-2xl p-5 border border-gray-100 hover:border-[#1a5632]/30 hover:shadow-md transition-all duration-300 group cursor-pointer" data-aos="fade-up" data-aos-delay="{{ $i * 50 }}">
                <div class="relative w-24 h-24 shrink-0 rounded-xl overflow-hidden">
                    <img src="{{ $past->image_url }}" alt="{{ $past->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" loading="lazy">
                </div>
                
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-lg text-[#0b2415] mb-1 truncate group-hover:text-[#1a5632] transition-colors">{{ $past->title }}</h3>
                    <p class="text-sm font-medium text-gray-500 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-[#e2a024]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ \Carbon\Carbon::parse($past->event_date)->format('M d, Y') }}
                    </p>
                </div>
                
                <div class="hidden sm:flex items-center shrink-0 pr-2">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400 bg-gray-100 px-2.5 py-1 rounded border border-gray-200">History</span>
                </div>
            </article>
            @empty
                <p class="text-gray-400 italic">Archive is currently empty.</p>
            @endforelse
        </div>
    </div>

</section>

@endsection