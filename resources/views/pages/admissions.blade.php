{{-- resources/views/pages/admissions.blade.php --}}
@extends('layouts.app')

{{-- Use dynamic year in the title and meta --}}
@section('title', __('site.admissions.page_title') . ' ' . ($settings['academic_year'] ?? ''))
@section('meta_description', __('site.admissions.page_title'))

@section('content')

{{-- ============================================================ --}}
{{-- HERO SECTION --}}
{{-- ============================================================ --}}
<section class="relative py-24 overflow-hidden bg-linear-to-br from-[#0b2415] via-[#1a5632] to-[#0b2415]">
    {{-- Decorative Background --}}
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 30px 30px;"></div>
    <div class="absolute top-0 right-0 w-96 h-96 bg-[#e2a024]/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 animate-[pulse_6s_infinite]"></div>
    <div class="absolute bottom-0 left-0 w-80 h-80 bg-white/10 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-green-200 text-sm font-medium mb-8" aria-label="Breadcrumb" data-aos="fade-down">
            <a href="{{ route('home') }}" class="hover:text-[#e2a024] hover:underline transition-colors">{{ __('site.common.home') }}</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-white">{{ __('site.admissions.breadcrumb') }}</span>
        </nav>

        <div class="inline-flex items-center gap-2 bg-[#e2a024] text-[#0b2415] font-bold text-sm px-6 py-2.5 rounded-full mb-6 shadow-[0_0_15px_rgba(226,160,36,0.4)] animate-bounce" data-aos="fade-up" data-aos-delay="50">
            🎓 {{ __('site.admissions.badge') }} {{ $settings['academic_year'] }}
        </div>

        <h1 class="text-4xl lg:text-5xl xl:text-6xl font-bold text-white mb-6 tracking-tight" data-aos="fade-up" data-aos-delay="100">
            {{ __('site.admissions.hero_h1') }}
        </h1>

        <p class="text-green-100/90 text-lg md:text-xl max-w-2xl leading-relaxed" data-aos="fade-up" data-aos-delay="150">
            {{ __('site.admissions.hero_sub') }}
        </p>
    </div>
</section>

{{-- ============================================================ --}}
{{-- CONTENT & FORM SECTION --}}
{{-- ============================================================ --}}
<section class="py-24 bg-[#fdfbf7] relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-12 gap-12 lg:gap-16">

            {{-- Info Column (Left) --}}
            <div class="lg:col-span-5">
                <div class="sticky top-32" data-aos="fade-right">
                    <p class="text-[#e2a024] font-bold text-sm uppercase tracking-widest mb-3 flex items-center gap-2">
                        <span class="w-8 h-0.5 bg-[#e2a024]"></span> {{ __('site.admissions.enroll_label') }}
                    </p>
                    <h2 class="text-3xl font-bold text-[#0b2415] mb-8 leading-tight">
                        Admission Information
                    </h2>

                    <div class="space-y-6 mb-10">
                        @foreach([
                            ['title' => __('site.admissions.classes_title'), 'content' => __('site.admissions.classes_content')],
                            ['title' => __('site.admissions.year_title'),    'content' => $settings['academic_year']],
                            ['title' => __('site.admissions.age_title'),     'content' => __('site.admissions.age_content')],
                            ['title' => __('site.admissions.medium_title'),  'content' => __('site.admissions.medium_content')],
                            ['title' => __('site.admissions.process_title'), 'content' => __('site.admissions.process_content')],
                            ['title' => __('site.admissions.docs_title'), 'content' => __('site.admissions.docs_content')],
                        ] as $i => $info)
                        <div class="flex gap-4 group" data-aos="fade-up" data-aos-delay="{{ $i * 50 }}">
                            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shrink-0 shadow-sm border border-gray-100 group-hover:bg-[#1a5632] transition-colors duration-300">
                                <svg class="w-5 h-5 text-[#e2a024]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="font-bold text-[#0b2415] text-sm md:text-base group-hover:text-[#1a5632] transition-colors">{{ $info['title'] }}</p>
                                <p class="text-gray-600 text-sm mt-1 leading-relaxed">{{ $info['content'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Contact Card (DYNAMIC) --}}
                    <div class="bg-[#1a5632] rounded-2xl p-8 relative overflow-hidden shadow-xl" data-aos="fade-up">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-[#e2a024] rounded-full blur-3xl opacity-20 -translate-y-1/2 translate-x-1/2"></div>
                        <p class="font-bold text-[#e2a024] mb-4 text-lg flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            {{ __('site.admissions.direct_inquiries') }}
                        </p>
                        <div class="space-y-3 text-white">
                            <p class="text-sm">{{ __('site.admissions.call_admin') }} <a href="tel:{{ preg_replace('/[^0-9+]/', '', $settings['phone']) }}" class="font-bold text-[#e2a024] hover:underline">{{ $settings['phone'] }}</a></p>
                            <p class="text-sm">{{ __('site.admissions.email_us') }} <a href="mailto:{{ $settings['email'] }}" class="font-bold text-[#e2a024] hover:underline">{{ $settings['email'] }}</a></p>
                            <div class="w-full h-px bg-white/20 my-4"></div>
                            <p class="text-xs text-green-100/70 font-medium">{{ __('site.admissions.office_hours') }} {{ $settings['office_hours'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Inquiry Form Column (Right) --}}
            <div class="lg:col-span-7" data-aos="fade-left">
                <div class="bg-white rounded-4xl p-6 sm:p-10 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100">
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-[#0b2415] mb-2">{{ __('site.admissions.form_h2') }}</h2>
                        <p class="text-sm text-gray-500">{{ __('site.admissions.form_sub') }}</p>
                    </div>

                    <form action="{{ route('admissions.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        @if ($errors->any())
                            <div class="bg-red-50 text-red-600 p-4 rounded-xl text-sm font-bold border border-red-100">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>• {{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="grid sm:grid-cols-2 gap-6">
                            {{-- Student Name --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2" for="student_name">{{ __('site.admissions.student_name') }}</label>
                                <input type="text" name="student_name" id="student_name" value="{{ old('student_name') }}" required placeholder="{{ __('site.admissions.student_name_ph') }}"
                                       class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5632]/20 focus:border-[#1a5632] focus:bg-white transition-all placeholder-gray-400">
                            </div>

                            {{-- Date of Birth --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2" for="dob">{{ __('site.admissions.dob') }}</label>
                                <input type="date" name="dob" id="dob" value="{{ old('dob') }}" required
                                       class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5632]/20 focus:border-[#1a5632] focus:bg-white transition-all text-gray-700">
                            </div>
                        </div>

                        <div class="grid sm:grid-cols-2 gap-6">
                            {{-- Gender --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2" for="gender">{{ __('site.admissions.gender') }}</label>
                                <div class="relative">
                                    <select name="gender" id="gender" required
                                            class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5632]/20 focus:border-[#1a5632] focus:bg-white transition-all appearance-none cursor-pointer">
                                        <option value="" disabled {{ !old('gender') ? 'selected' : '' }}>{{ __('site.admissions.gender_ph') }}</option>
                                        <option value="Male"   {{ old('gender') == 'Male'   ? 'selected' : '' }}>{{ __('site.admissions.gender_male') }}</option>
                                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>{{ __('site.admissions.gender_female') }}</option>
                                        <option value="Other"  {{ old('gender') == 'Other'  ? 'selected' : '' }}>{{ __('site.admissions.gender_other') }}</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                            </div>

                            {{-- Applying Class --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2" for="applied_grade">{{ __('site.admissions.class') }}</label>
                                <div class="relative">
                                    <select name="applied_grade" id="applied_grade" required
                                            class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5632]/20 focus:border-[#1a5632] focus:bg-white transition-all appearance-none cursor-pointer">
                                        <option value="" disabled {{ !old('applied_grade') ? 'selected' : '' }}>{{ __('site.admissions.class_ph') }}</option>
                                        <optgroup label="{{ __('site.admissions.class_kids') }}">
                                            <option value="Nursery" {{ old('applied_grade') == 'Nursery' ? 'selected' : '' }}>Nursery</option>
                                            <option value="LKG" {{ old('applied_grade') == 'LKG' ? 'selected' : '' }}>LKG</option>
                                            <option value="UKG" {{ old('applied_grade') == 'UKG' ? 'selected' : '' }}>UKG</option>
                                            <option value="Class 1" {{ old('applied_grade') == 'Class 1' ? 'selected' : '' }}>Class 1</option>
                                            <option value="Class 2" {{ old('applied_grade') == 'Class 2' ? 'selected' : '' }}>Class 2</option>
                                            <option value="Class 3" {{ old('applied_grade') == 'Class 3' ? 'selected' : '' }}>Class 3</option>
                                        </optgroup>
                                        <optgroup label="{{ __('site.admissions.class_middle') }}">
                                            @for($c = 4; $c <= 10; $c++)
                                            <option value="Class {{ $c }}" {{ old('applied_grade') == "Class $c" ? 'selected' : '' }}>Class {{ $c }}</option>
                                            @endfor
                                        </optgroup>
                                        <optgroup label="{{ __('site.admissions.class_higher') }}">
                                            <option value="11_Education" {{ old('applied_grade') == '11_Education' ? 'selected' : '' }}>Class 11 (Education)</option>
                                            <option value="11_Management" {{ old('applied_grade') == '11_Management' ? 'selected' : '' }}>Class 11 (Management)</option>
                                            <option value="Civil_Engineering_Diploma" {{ old('applied_grade') == 'Civil_Engineering_Diploma' ? 'selected' : '' }}>Diploma in Civil Engineering</option>
                                        </optgroup>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Parent Name --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2" for="guardian_name">{{ __('site.admissions.guardian_name') }}</label>
                            <input type="text" name="guardian_name" id="guardian_name" value="{{ old('guardian_name') }}" required placeholder="{{ __('site.admissions.guardian_ph') }}"
                                   class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5632]/20 focus:border-[#1a5632] focus:bg-white transition-all placeholder-gray-400">
                        </div>

                        <div class="grid sm:grid-cols-2 gap-6">
                            {{-- Phone --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2" for="phone">{{ __('site.admissions.phone') }}</label>
                                <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required placeholder="98XXXXXXXX"
                                       class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5632]/20 focus:border-[#1a5632] focus:bg-white transition-all placeholder-gray-400">
                            </div>
                            
                            {{-- Email --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2" for="email">{{ __('site.admissions.email') }}</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="optional@email.com"
                                       class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5632]/20 focus:border-[#1a5632] focus:bg-white transition-all placeholder-gray-400">
                            </div>
                        </div>

                        {{-- Address --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2" for="address">{{ __('site.admissions.address') }}</label>
                            <input type="text" name="address" id="address" value="{{ old('address') }}" required placeholder="{{ __('site.admissions.address_ph') }}"
                                   class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5632]/20 focus:border-[#1a5632] focus:bg-white transition-all placeholder-gray-400">
                        </div>

                        {{-- Previous School --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2" for="previous_school">{{ __('site.admissions.prev_school') }}</label>
                            <input type="text" name="previous_school" id="previous_school" value="{{ old('previous_school') }}" placeholder="{{ __('site.admissions.prev_school_ph') }}"
                                   class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5632]/20 focus:border-[#1a5632] focus:bg-white transition-all placeholder-gray-400">
                        </div>

                        {{-- Success Notification --}}
                        @if(session('success'))
                        <div class="bg-green-50 border border-[#1a5632]/20 text-[#1a5632] rounded-xl p-4 text-sm font-bold flex items-center gap-3 shadow-sm">
                            <span class="w-6 h-6 bg-[#1a5632] text-white rounded-full flex items-center justify-center shrink-0">✓</span>
                            {{ session('success') }}
                        </div>
                        @endif

                        {{-- Submit Button --}}
                        <div class="pt-4">
                            <button type="submit" class="w-full py-4 bg-[#1a5632] text-white font-bold rounded-xl hover:bg-[#0b2415] hover:shadow-lg hover:-translate-y-1 transition-all duration-300 text-lg flex items-center justify-center gap-2">
                                {{ __('site.admissions.submit_btn') }}
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                            <p class="text-xs text-gray-400 text-center mt-4 font-medium flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                {{ __('site.common.secure_note') }}
                            </p>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection
