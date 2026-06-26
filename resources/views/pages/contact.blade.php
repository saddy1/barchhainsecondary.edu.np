{{-- resources/views/pages/contact.blade.php --}}
@extends('layouts.app')

@section('title', __('site.contact.page_title'))
@section('meta_description', __('site.contact.meta_desc'))

@section('content')

{{-- ============================================================ --}}
{{-- HERO SECTION --}}
{{-- ============================================================ --}}
<section class="relative py-24 overflow-hidden bg-linear-to-br from-[#0b2415] via-[#1a5632] to-[#0b2415]">
    {{-- Decorative Background Elements --}}
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 30px 30px;"></div>
    <div class="absolute -top-20 -left-20 w-80 h-80 bg-[#e2a024]/20 rounded-full blur-3xl animate-[pulse_6s_infinite]"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-white/10 rounded-full blur-3xl translate-y-1/2 translate-x-1/4"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-green-200 text-sm font-medium mb-8" aria-label="Breadcrumb" data-aos="fade-down">
            <a href="{{ route('home') }}" class="hover:text-[#e2a024] hover:underline transition-colors">{{ __('site.common.home') }}</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-white">{{ __('site.contact.breadcrumb') }}</span>
        </nav>

        <div class="inline-flex items-center gap-2 bg-[#e2a024] text-[#0b2415] font-bold text-sm px-6 py-2.5 rounded-full mb-6 shadow-lg" data-aos="fade-up" data-aos-delay="50">
            📞 {{ __('site.contact.badge') }}
        </div>

        <h1 class="text-4xl lg:text-5xl xl:text-6xl font-bold text-white mb-6 tracking-tight" data-aos="fade-up" data-aos-delay="100">
            {{ __('site.contact.hero_h1') }}
        </h1>

        <div class="max-w-2xl" data-aos="fade-up" data-aos-delay="150">
            <p class="text-green-100/90 text-lg md:text-xl font-medium leading-relaxed">
                {{ __('site.contact.hero_sub') }}
            </p>
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- CONTACT CARDS & FORM SECTION --}}
{{-- ============================================================ --}}
<section class="py-24 bg-[#fdfbf7] relative" id="contact-form">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Quick Contact Info Cards --}}
        <div class="grid sm:grid-cols-3 gap-6 mb-20 relative z-20 -mt-36">
            @foreach([
                ['icon' => '📍', 'title' => __('site.contact.visit_title'), 'lines' => [$siteSettings->localized('site_address', __('site.location'))]],
                ['icon' => '📞', 'title' => __('site.contact.call_title'),  'lines' => [$siteSettings->get('school_phone', __('site.footer.phone')), $siteSettings->get('office_hours', 'Mon-Fri 9:00 AM - 5:00 PM')]],
                ['icon' => '✉️', 'title' => __('site.contact.email_title'), 'lines' => [$siteSettings->get('school_email', 'info@barchhainsecondary.edu.np'), __('site.contact.email_line2')]],
            ] as $i => $card)
            <div class="bg-white rounded-3xl p-8 text-center shadow-xl border border-gray-100 hover:border-[#1a5632]/50 hover:-translate-y-2 transition-all duration-300 group" data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
                <div class="w-16 h-16 mx-auto bg-[#fdfbf7] rounded-2xl flex items-center justify-center text-3xl mb-5 group-hover:bg-[#1a5632]/5 group-hover:scale-110 transition-transform duration-300">
                    {{ $card['icon'] }}
                </div>
                <h3 class="text-xl font-bold text-[#0b2415] mb-3 group-hover:text-[#1a5632] transition-colors">{{ $card['title'] }}</h3>
                @foreach($card['lines'] as $line)
                <p class="text-gray-600 font-medium text-sm leading-relaxed">{{ $line }}</p>
                @endforeach
            </div>
            @endforeach
        </div>

        {{-- Main Content Grid --}}
        <div class="grid lg:grid-cols-2 gap-16">
            
            {{-- Left Column: Map & Office Hours --}}
            <div data-aos="fade-right">
                <h2 class="text-3xl font-bold text-[#0b2415] mb-6 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-[#e2a024] flex items-center justify-center shadow-md">
                        <svg class="w-4 h-4 text-[#0b2415]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </span>
                    {{ __('site.contact.map_h2') }}
                </h2>
                
                {{-- Interactive Map --}}
                @php
                    $mapLat  = $siteSettings->get('map_latitude',  '29.2844');
                    $mapLng  = $siteSettings->get('map_longitude', '81.0897');
                    $mapZoom = $siteSettings->get('map_zoom',      '16');
                    $mapSrc  = "https://maps.google.com/maps?q={$mapLat},{$mapLng}&t=&z={$mapZoom}&ie=UTF8&iwloc=&output=embed";
                @endphp
                <div class="rounded-4xl overflow-hidden shadow-lg border-4 border-white h-80 mb-8 relative group">
                    <div class="absolute inset-0 bg-[#1a5632]/10 group-hover:opacity-0 transition-opacity duration-500 pointer-events-none z-10"></div>
                    <iframe
                        src="{{ $mapSrc }}"
                        width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="{{ $siteSettings->localized('site_name', __('site.school_name')) }} Location" class="grayscale-20 group-hover:grayscale-0 transition-all duration-500">
                    </iframe>
                </div>

                {{-- Office Hours Card --}}
                @php
                    $ohDays   = $siteSettings->get('office_hours_days',   'Sunday – Friday');
                    $ohTime   = $siteSettings->get('office_hours_time',   '9:00 AM – 5:00 PM');
                    $ohClosed = $siteSettings->get('office_hours_closed', 'Saturday & Public Holidays');
                @endphp
                <div class="bg-[#1a5632] text-white rounded-4xl p-8 shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-[#e2a024] rounded-full blur-3xl opacity-20 -translate-y-1/2 translate-x-1/2"></div>
                    <h3 class="text-2xl font-bold mb-6 flex items-center gap-3">
                        <svg class="w-6 h-6 text-[#e2a024]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ __('site.contact.hours_h3') }}
                    </h3>
                    <div class="space-y-4 text-green-50 font-medium">
                        <div class="flex justify-between items-center border-b border-white/10 pb-3">
                            <span class="flex items-center gap-2"><div class="w-2 h-2 rounded-full bg-[#e2a024]"></div> {{ $ohDays }}</span>
                            <span class="text-[#e2a024] font-bold">{{ $ohTime }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-1">
                            <span class="flex items-center gap-2"><div class="w-2 h-2 rounded-full bg-red-400"></div> {{ $ohClosed }}</span>
                            <span class="text-red-300 font-bold bg-red-500/10 px-3 py-1 rounded-full text-xs">Closed</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Functional Contact Form --}}
            <div data-aos="fade-left">
                <div class="bg-white rounded-4xl p-8 sm:p-10 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100">
                    
                    {{-- ============================================================ --}}
                    {{-- ANIMATED TOP SUCCESS MESSAGE (Alpine.js) --}}
                    {{-- ============================================================ --}}
                    @if(session('contact_success'))
                    <div x-data="{ show: true }" 
                         x-show="show" 
                         x-init="setTimeout(() => show = false, 6000)"
                         x-transition:enter="transition ease-out duration-500"
                         x-transition:enter-start="opacity-0 -translate-y-8 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 -translate-y-8 scale-95"
                         class="mb-8 bg-green-50 border-2 border-green-200 rounded-2xl p-5 shadow-sm relative overflow-hidden">
                        
                        <div class="absolute top-0 left-0 w-1 h-full bg-green-500"></div>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center shrink-0 shadow-md">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-green-900 text-lg">Message Sent!</h4>
                                <p class="text-sm text-green-700 mt-1 font-medium">{{ session('contact_success') }}</p>
                            </div>
                            <button @click="show = false" class="text-green-400 hover:text-green-700 transition-colors p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>
                    @endif
                    {{-- ============================================================ --}}

                    <h2 class="text-3xl font-bold text-[#0b2415] mb-2">{{ __('site.contact.form_h2') }}</h2>
                    <p class="text-gray-500 text-sm mb-8">{{ __('site.contact.form_sub') }}</p>
                    
                    {{-- Form pointing to the contact.store route --}}
                    <form action="{{ route('contact.submit') }}#contact-form" method="POST" class="space-y-6">
                        @csrf
                        
                        <div class="grid sm:grid-cols-2 gap-6">
                            {{-- Name --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2" for="name">{{ __('site.contact.name_label') }}</label>
                                <input type="text" name="name" id="name" required value="{{ old('name') }}" placeholder="{{ __('site.contact.name_ph') }}"
                                       class="w-full px-5 py-3.5 bg-gray-50 border {{ $errors->has('name') ? 'border-red-400' : 'border-gray-200' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5632]/20 focus:border-[#1a5632] focus:bg-white transition-all placeholder-gray-400">
                                @error('name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                            </div>
                            
                            {{-- Phone --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2" for="contact_phone">{{ __('site.contact.phone_label') }}</label>
                                <input type="tel" name="phone" id="contact_phone" required value="{{ old('phone') }}" placeholder="98XXXXXXXX"
                                       class="w-full px-5 py-3.5 bg-gray-50 border {{ $errors->has('phone') ? 'border-red-400' : 'border-gray-200' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5632]/20 focus:border-[#1a5632] focus:bg-white transition-all placeholder-gray-400">
                                @error('phone') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2" for="contact_email">{{ __('site.contact.email_label') }}</label>
                            <input type="email" name="email" id="contact_email" value="{{ old('email') }}" placeholder="optional@email.com"
                                   class="w-full px-5 py-3.5 bg-gray-50 border {{ $errors->has('email') ? 'border-red-400' : 'border-gray-200' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5632]/20 focus:border-[#1a5632] focus:bg-white transition-all placeholder-gray-400">
                            @error('email') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Subject Select --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2" for="subject">{{ __('site.contact.subject_label') }}</label>
                            <div class="relative">
                                <select name="subject" id="subject" required class="w-full px-5 py-3.5 bg-gray-50 border {{ $errors->has('subject') ? 'border-red-400' : 'border-gray-200' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5632]/20 focus:border-[#1a5632] focus:bg-white transition-all appearance-none cursor-pointer">
                                    <option value="" disabled {{ old('subject') ? '' : 'selected' }}>Select a topic</option>
                                    <option value="Admission Inquiry" {{ old('subject') == 'Admission Inquiry' ? 'selected' : '' }}>Admission Inquiry</option>
                                    <option value="Fee Structure" {{ old('subject') == 'Fee Structure' ? 'selected' : '' }}>Fee Structure</option>
                                    <option value="Academic Programs" {{ old('subject') == 'Academic Programs' ? 'selected' : '' }}>Academic Programs (+2, SEE, etc.)</option>
                                    <option value="Transportation / Bus" {{ old('subject') == 'Transportation / Bus' ? 'selected' : '' }}>Transportation / Bus Service</option>
                                    <option value="General Question" {{ old('subject') == 'General Question' ? 'selected' : '' }}>Other General Question</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                            @error('subject') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Message --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2" for="contact_message">{{ __('site.contact.message_label') }}</label>
                            <textarea name="message" id="contact_message" rows="5" required placeholder="{{ __('site.contact.message_ph') }}"
                                      class="w-full px-5 py-3.5 bg-gray-50 border {{ $errors->has('message') ? 'border-red-400' : 'border-gray-200' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5632]/20 focus:border-[#1a5632] focus:bg-white transition-all resize-none placeholder-gray-400">{{ old('message') }}</textarea>
                            @error('message') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Submit Button --}}
                        <div class="pt-2">
                            <button type="submit" class="w-full py-4 bg-[#1a5632] text-white font-bold rounded-xl hover:bg-[#0b2415] hover:shadow-lg hover:-translate-y-1 transition-all duration-300 text-lg flex items-center justify-center gap-2">
                                {{ __('site.contact.submit_btn') }}
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
</section>

@endsection

@section('scripts')
{{-- Required for the animated success message --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection
