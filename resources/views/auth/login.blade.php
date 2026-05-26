{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Admin Login | {{ $siteSettings->localized("site_name", "Barchhain Secondary School") }}</title>
    
    {{-- Fonts & Tailwind --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-50 h-screen flex overflow-hidden selection:bg-[#1a5632] selection:text-white">

    {{-- Left Side: Branding & Image (Hidden on Mobile) --}}
    <div class="hidden lg:flex lg:w-1/2 relative bg-[#0b2415] items-center justify-center overflow-hidden">
        {{-- Decorative Background --}}
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 30px 30px;"></div>
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-[#1a5632] rounded-full blur-3xl opacity-50"></div>
        <div class="absolute bottom-0 right-0 w-80 h-80 bg-[#e2a024] rounded-full blur-3xl opacity-20"></div>

        <div class="relative z-10 text-center px-12">
            <div class="w-32 h-32 mx-auto bg-white rounded-2xl flex items-center justify-center p-2 shadow-2xl mb-8">
                <img src="{{ $siteSettings->logoUrl() }}" alt="{{ $siteSettings->localized('site_name', 'Barchhain Secondary School') }} Logo" class="w-full h-full object-contain">
            </div>
            <h1 class="text-4xl font-bold text-white mb-4">{{ $siteSettings->localized("site_name", "Barchhain Secondary School") }}</h1>
            <p class="text-green-100/80 text-lg">Secure Administration Portal</p>
        </div>
    </div>

    {{-- Right Side: Login Form --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 lg:p-24 overflow-y-auto">
        <div class="w-full max-w-md">
            
            {{-- Mobile Logo (Hidden on Desktop) --}}
            <div class="lg:hidden text-center mb-10">
                <div class="w-20 h-20 mx-auto bg-white border border-gray-100 rounded-2xl flex items-center justify-center p-1 shadow-sm mb-4">
                    <img src="{{ $siteSettings->logoUrl() }}" alt="Logo" class="w-full h-full object-contain">
                </div>
                <h1 class="text-2xl font-bold text-[#0b2415]">{{ $siteSettings->localized('site_name', 'Barchhain Secondary School') }} Admin</h1>
            </div>

            <div class="mb-10">
                <h2 class="text-3xl font-bold text-[#0b2415] mb-2">Welcome Back</h2>
                <p class="text-gray-500">Please sign in to access the dashboard.</p>
            </div>

            {{-- Form Status/Errors --}}
            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-4 rounded-xl border border-green-100">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                {{-- User ID / Email --}}
                <div>
                    <label for="login" class="block text-sm font-bold text-gray-700 mb-2">User ID or Email Address</label>
                    <input id="login" type="text" name="login" value="{{ old('login') }}" required autofocus autocomplete="username"
                           class="w-full px-5 py-3.5 bg-gray-50 border @error('login') border-red-500 @else border-gray-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5632]/20 focus:border-[#1a5632] focus:bg-white transition-all">
                    @error('login')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-sm font-bold text-gray-700">Password</label>
                        @if (Route::has('password.request'))
                            <a class="text-sm font-bold text-[#1a5632] hover:text-[#e2a024] transition-colors" href="{{ route('password.request') }}">
                                Forgot password?
                            </a>
                        @endif
                    </div>
                    <input id="password" type="password" name="password" required autocomplete="current-password" 
                           class="w-full px-5 py-3.5 bg-gray-50 border @error('password') border-red-500 @else border-gray-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5632]/20 focus:border-[#1a5632] focus:bg-white transition-all">
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center">
                    <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 text-[#1a5632] bg-gray-100 border-gray-300 rounded focus:ring-[#1a5632]">
                    <label for="remember_me" class="ml-2 text-sm font-medium text-gray-600 cursor-pointer">
                        Remember me for 30 days
                    </label>
                </div>

                {{-- Submit Button --}}
                <div class="pt-2">
                    <button type="submit" class="w-full py-4 bg-[#1a5632] text-white font-bold rounded-xl hover:bg-[#0b2415] hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 text-lg flex items-center justify-center gap-2">
                        Sign In to Dashboard
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
            </form>
            <div>
                <p class="mt-6 text-center text-sm text-gray-500">
                    Back To ......
                    <a href="/" class="font-bold text-[#f86706] hover:text-[#112c04] transition-colors">HOME</a>
                </p>

            
            </div>
            
            {{-- Footer Text --}}
            <p class="mt-10 text-center text-xs text-gray-400 font-medium">
                &copy; {{ date('Y') }} {{ $siteSettings->localized("site_name", "Barchhain Secondary School") }}.<br>Secure Access Only.
            </p>
        </div>
    </div>

</body>
</html>
