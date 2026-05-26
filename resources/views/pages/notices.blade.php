{{-- resources/views/pages/notices.blade.php --}}
@extends('layouts.app')

@section('title', __('site.notices.page_title'))
@section('meta_description', __('site.notices.meta_desc'))

@section('content')

{{-- ============================================================ --}}
{{-- HERO SECTION --}}
{{-- ============================================================ --}}
<section class="relative py-24 overflow-hidden bg-gradient-to-br from-[#0b2415] via-[#1a5632] to-[#0b2415]">
    {{-- Decorative Background Elements --}}
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 30px 30px;"></div>
    <div class="absolute -top-24 -right-24 w-96 h-96 bg-[#e2a024]/20 rounded-full blur-3xl animate-[pulse_6s_infinite]"></div>
    <div class="absolute bottom-0 left-0 w-80 h-80 bg-white/10 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-green-200 text-sm font-medium mb-8" aria-label="Breadcrumb" data-aos="fade-down">
            <a href="{{ route('home') }}" class="hover:text-[#e2a024] hover:underline transition-colors">{{ __('site.common.home') }}</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-white">{{ __('site.notices.breadcrumb') }}</span>
        </nav>

        <div class="inline-flex items-center gap-2 bg-[#e2a024] text-[#0b2415] font-bold text-sm px-6 py-2.5 rounded-full mb-6 shadow-lg" data-aos="fade-up" data-aos-delay="50">
            📢 {{ __('site.notices.badge') }}
        </div>

        <h1 class="text-4xl lg:text-5xl xl:text-6xl font-bold text-white mb-6 tracking-tight" data-aos="fade-up" data-aos-delay="100">
            {{ __('site.notices.hero_h1') }}
        </h1>

        <div class="max-w-2xl" data-aos="fade-up" data-aos-delay="150">
            <p class="text-green-100/90 text-lg md:text-xl font-medium leading-relaxed">
                {{ __('site.notices.hero_sub') }}
            </p>
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- NOTICES BOARD --}}
{{-- ============================================================ --}}
<section class="py-24 bg-[#fdfbf7] relative">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Category Filter (Now using real Links for SEO) --}}
        <div class="flex flex-wrap items-center gap-3 mb-12 border-b border-gray-200 pb-6" data-aos="fade-up">
            <span class="text-sm font-bold text-gray-500 uppercase tracking-widest mr-2">{{ __('site.common.filter_by') }}:</span>

            @php
                $noticeFilters = [
                    'All'       => __('site.notices.filter_all'),
                    'Admission' => __('site.notices.filter_admission'),
                    'Academic'  => __('site.notices.filter_academic'),
                    'Event'     => __('site.notices.filter_event'),
                    'General'   => __('site.notices.filter_general'),
                ];
            @endphp
            @foreach($noticeFilters as $key => $label)
            <a href="{{ route('notices', ['category' => $key]) }}"
               class="text-sm font-bold px-5 py-2.5 rounded-xl border transition-all duration-300 {{ $category === $key ? 'bg-[#1a5632] text-white shadow-md border-[#1a5632]' : 'bg-white text-gray-600 hover:text-[#1a5632] hover:bg-green-50 border-gray-200' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>

        {{-- Notices List --}}
        <div class="space-y-6 min-h-[400px]">
            @forelse($notices as $i => $notice)
            @php
                $nDate = \Carbon\Carbon::parse($notice->created_at);
            @endphp
            <article
                class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100 hover:border-[#1a5632]/40 hover:shadow-lg transition-all duration-300 group relative overflow-hidden flex flex-col md:flex-row gap-6 md:items-start"
                data-aos="fade-up" data-aos-delay="{{ $i * 50 }}"
            >
                {{-- Category Color Accent Line --}}
                <div class="absolute left-0 top-0 bottom-0 w-1.5 
                    @if($notice->category === 'Admission') bg-[#e2a024]
                    @elseif($notice->category === 'Academic') bg-blue-500
                    @elseif($notice->category === 'Event') bg-[#1a5632]
                    @else bg-gray-400
                    @endif">
                </div>

                {{-- Date Block --}}
                <div class="shrink-0 md:w-32 flex flex-row md:flex-col items-center md:items-start gap-3 md:gap-1">
                    <div class="bg-gray-50 border border-gray-100 rounded-xl p-3 md:p-4 text-center min-w-[80px]">
                        <span class="block text-2xl font-black text-[#0b2415] leading-none mb-1">{{ $nDate->format('d') }}</span>
                        <span class="block text-[10px] font-bold uppercase tracking-widest text-[#1a5632]">{{ $nDate->format('M') }}</span>
                    </div>
                </div>

                {{-- Content Block --}}
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-md
                            @if($notice->category === 'Admission') bg-[#e2a024]/10 text-[#b87e15] border border-[#e2a024]/20
                            @elseif($notice->category === 'Academic') bg-blue-50 text-blue-700 border border-blue-200
                            @elseif($notice->category === 'Event') bg-[#1a5632]/10 text-[#1a5632] border border-[#1a5632]/20
                            @else bg-gray-100 text-gray-600 border border-gray-200
                            @endif">
                            {{ $notice->category }}
                        </span>
                        <span class="text-xs font-medium text-gray-400 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Posted {{ $notice->created_at->diffForHumans() }}
                        </span>
                    </div>
                    
                    <a href="{{ route('news.show', $notice->slug) }}" class="block group-hover:text-[#1a5632] transition-colors">
                        <h2 class="text-xl sm:text-2xl font-bold text-[#0b2415] mb-3 leading-tight line-clamp-2">
                            {{ $notice->title }}
                        </h2>
                    </a>
                    
                    <p class="text-gray-600 text-sm sm:text-base leading-relaxed mb-4 line-clamp-3">
                        {{ $notice->excerpt ?? Str::limit(strip_tags($notice->content), 180) }}
                    </p>
                    
                    <a href="{{ route('news.show', $notice->slug) }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-[#1a5632] hover:text-[#e2a024] transition-colors group/link">
                        {{ __('site.news.read_more') }}
                        <svg class="w-4 h-4 group-hover/link:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            </article>
            @empty
            <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-gray-200">
                <div class="text-4xl mb-4">📭</div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">{{ __('site.notices.empty_title') }}</h3>
                <p class="text-gray-500">{{ __('site.notices.empty_sub') }}</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination (Laravel Built-in) --}}
        <div class="mt-16" data-aos="fade-up">
            {{ $notices->links() }}
        </div>
        
    </div>
</section>

@endsection

@section('scripts')
{{-- Ensure Alpine.js is loaded for the interactive category filtering --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection