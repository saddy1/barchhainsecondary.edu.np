<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Student Portal | {{ $siteSettings->localized('site_name', 'Barchhain Secondary School') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-50 min-h-screen lg:h-screen lg:overflow-hidden selection:bg-[#1a5632] selection:text-white">
@php
    $fieldErrors = collect($errors->getMessages())
        ->except('credentials')
        ->flatMap(fn ($messages) => $messages);

    $features = [
        ['label' => 'Digital ID Card', 'text' => 'View card details and submit update requests.'],
        ['label' => 'E-Learning', 'text' => 'Access courses, resources, lessons, and quizzes.'],
        ['label' => 'Profile Requests', 'text' => 'Request correction for personal and guardian details.'],
    ];
@endphp

<main class="min-h-screen lg:h-screen grid lg:grid-cols-[1.08fr_.92fr]">
    <section class="relative hidden overflow-hidden bg-[#0b2415] px-12 py-10 lg:flex lg:flex-col lg:justify-between">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 30px 30px;"></div>
        <div class="absolute -top-28 -left-28 h-96 w-96 rounded-full bg-[#1a5632] opacity-50 blur-3xl"></div>
        <div class="absolute bottom-0 right-0 h-80 w-80 rounded-full bg-[#e2a024] opacity-20 blur-3xl"></div>
        <div class="absolute left-10 right-10 top-10 h-px bg-white/10"></div>

        <div class="relative z-10">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-3 rounded-2xl bg-white/8 px-3 py-2 text-white/80 ring-1 ring-white/10 hover:bg-white/12">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-white p-1.5 shadow-lg">
                    <img src="{{ $siteSettings->logoUrl() }}" alt="{{ $siteSettings->localized('site_name', 'Barchhain Secondary School') }} Logo" class="h-full w-full object-contain">
                </span>
                <span>
                    <span class="block max-w-80 truncate text-sm font-extrabold text-white">{{ $siteSettings->localized('site_name', 'Barchhain Secondary School') }}</span>
                    <span class="block text-[11px] font-bold uppercase tracking-widest text-white/45">Student Portal</span>
                </span>
            </a>
        </div>

        <div class="relative z-10 max-w-xl">
            <p class="mb-4 inline-flex rounded-full bg-[#e2a024]/15 px-4 py-2 text-xs font-extrabold uppercase tracking-widest text-[#f4b63e] ring-1 ring-[#e2a024]/25">
                Unified Student Access
            </p>
            <h1 class="text-5xl font-extrabold leading-tight tracking-tight text-white">
                One secure login for every student service.
            </h1>
            <p class="mt-5 max-w-lg text-base font-medium leading-8 text-white/65">
                Sign in with your school-issued User ID or email to manage ID card requests, learning resources, and profile update requests from one place.
            </p>

            <div class="mt-8 grid gap-3">
                @foreach($features as $feature)
                    <div class="flex items-start gap-3 rounded-2xl border border-white/10 bg-white/8 p-4">
                        <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#e2a024] text-[#0b2415]">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </span>
                        <span>
                            <span class="block text-sm font-extrabold text-white">{{ $feature['label'] }}</span>
                            <span class="mt-1 block text-sm font-medium leading-6 text-white/55">{{ $feature['text'] }}</span>
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="relative z-10 flex items-center justify-between border-t border-white/10 pt-5 text-xs font-bold text-white/45">
            <span>Protected student access</span>
            <span>{{ date('Y') }}</span>
        </div>
    </section>

    <section class="flex min-h-screen items-center justify-center overflow-y-auto px-5 py-8 sm:px-8 lg:min-h-0 lg:px-14 lg:py-10">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center lg:hidden">
                <a href="{{ url('/') }}" class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-2xl border border-gray-100 bg-white p-2 shadow-sm">
                    <img src="{{ $siteSettings->logoUrl() }}" alt="Logo" class="h-full w-full object-contain">
                </a>
                <h1 class="text-2xl font-extrabold text-[#0b2415]">Student Portal</h1>
                <p class="mt-1 text-sm font-medium text-gray-500">{{ $siteSettings->localized('site_name', 'Barchhain Secondary School') }}</p>
            </div>

            <div class="mb-8">
                <p class="text-xs font-extrabold uppercase tracking-widest text-[#1a5632]">Student Login</p>
                <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-[#0b2415]">Welcome Back</h2>
                <p class="mt-2 text-sm font-medium leading-6 text-gray-500">
                    Enter your User ID or email and password provided by the school.
                </p>
            </div>

            @if($errors->has('credentials'))
                <div class="mb-5 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                    <svg class="mt-0.5 h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ $errors->first('credentials') }}</span>
                </div>
            @endif

            @if($fieldErrors->isNotEmpty())
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach($fieldErrors as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('student.login.post') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="login" class="mb-2 block text-sm font-bold text-gray-700">User ID or Email</label>
                    <input id="login"
                           type="text"
                           name="login"
                           value="{{ old('login') }}"
                           required
                           autofocus
                           autocomplete="username"
                           placeholder="e.g. STU-2083-001 or student@email.com"
                           class="w-full rounded-xl border border-gray-200 bg-gray-50 px-5 py-3.5 text-sm font-semibold text-gray-900 transition-all focus:border-[#1a5632] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1a5632]/20">
                    <p class="mt-2 text-xs font-medium text-gray-400">Use your HR-issued login ID. Email also works if registered.</p>
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-bold text-gray-700">Password</label>
                    <input id="password"
                           type="password"
                           name="password"
                           required
                           autocomplete="current-password"
                           placeholder="Enter password"
                           class="w-full rounded-xl border border-gray-200 bg-gray-50 px-5 py-3.5 text-sm font-semibold text-gray-900 transition-all focus:border-[#1a5632] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1a5632]/20">
                </div>

                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#1a5632] px-5 py-4 text-base font-extrabold text-white transition-all duration-300 hover:-translate-y-0.5 hover:bg-[#0b2415] hover:shadow-lg">
                    Sign In to Student Portal
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </form>

            <div class="mt-6 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                <div class="flex gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#1a5632]/10 text-[#1a5632]">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </span>
                    <div>
                        <p class="text-sm font-extrabold text-gray-900">Cannot sign in?</p>
                        <p class="mt-1 text-xs font-medium leading-5 text-gray-500">Contact your class teacher or administration office to reset your student portal password.</p>
                    </div>
                </div>
            </div>

            <p class="mt-8 text-center text-xs font-medium text-gray-400">
                <a href="{{ url('/') }}" class="font-extrabold text-[#1a5632] hover:text-[#e2a024]">Back to website</a>
                <span class="mx-2 text-gray-300">|</span>
                Secure access only
            </p>
        </div>
    </section>
</main>
</body>
</html>
