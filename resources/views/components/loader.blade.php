{{-- Page Loader Component --}}
<div id="page-loader" class="fixed inset-0 z-[9999] flex items-center justify-center bg-gradient-to-br from-[#fdfbf7] via-white to-[#f5f0ea]">
    <div class="flex flex-col items-center justify-center">
        {{-- Logo Container --}}
        <div class="relative mb-8 animate-fade-in">
            <div class="w-24 h-24 md:w-32 md:h-32 rounded-full bg-gradient-to-br from-[#1a5632] to-[#0b2415] flex items-center justify-center shadow-2xl animate-pulse-slow">
                @if($logoUrl = isset($siteSettings) ? $siteSettings->logoUrl() : asset('assets/image/logo.png'))
                    <img src="{{ $logoUrl }}" alt="School Logo" class="w-20 h-20 md:w-28 md:h-28 object-contain filter brightness-110">
                @else
                    <svg class="w-20 h-20 md:w-28 md:h-28 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.753 2 16.5S6.5 26.747 12 26.747s10-4.5 10-10.247S17.5 6.253 12 6.253z"></path>
                    </svg>
                @endif
            </div>
            {{-- Animated Ring --}}
            <div class="absolute inset-0 rounded-full border-4 border-transparent border-t-[#1a5632] border-r-[#e2a024] animate-spin-slow"></div>
        </div>

        {{-- School Name --}}
        <h2 class="text-xl md:text-2xl font-bold text-[#0b2415] mb-2 text-center animate-fade-in-delay-1">
            {{ isset($siteSettings) ? $siteSettings->localized('site_name', 'Barchhain Secondary School') : 'Barchhain Secondary School' }}
        </h2>

        {{-- Loading Text --}}
        <p class="text-gray-600 text-sm md:text-base mb-6 animate-fade-in-delay-2">Loading...</p>

        {{-- Progress Bar --}}
        <div class="w-40 h-1 bg-gray-300 rounded-full overflow-hidden shadow-md">
            <div class="h-full bg-gradient-to-r from-[#1a5632] to-[#e2a024] rounded-full animate-progress"></div>
        </div>

        {{-- Loading Dots --}}
        <div class="flex gap-2 mt-6 animate-fade-in-delay-2">
            <span class="w-2 h-2 bg-[#1a5632] rounded-full animate-bounce" style="animation-delay: 0s"></span>
            <span class="w-2 h-2 bg-[#e2a024] rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
            <span class="w-2 h-2 bg-[#0b2415] rounded-full animate-bounce" style="animation-delay: 0.4s"></span>
        </div>
    </div>
</div>

<style>
    @keyframes fade-in {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    @keyframes fade-in-delay-1 {
        0% {
            opacity: 0;
            transform: translateY(10px);
        }
        40% {
            opacity: 0;
            transform: translateY(10px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fade-in-delay-2 {
        0% {
            opacity: 0;
            transform: translateY(10px);
        }
        60% {
            opacity: 0;
            transform: translateY(10px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes spin-slow {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    @keyframes pulse-slow {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(26, 86, 50, 0.7), 0 0 0 0 rgba(226, 160, 36, 0.7);
        }
        50% {
            box-shadow: 0 0 0 15px rgba(26, 86, 50, 0), 0 0 0 15px rgba(226, 160, 36, 0);
        }
    }

    @keyframes progress {
        0% {
            width: 0%;
        }
        50% {
            width: 70%;
        }
        100% {
            width: 100%;
        }
    }

    .animate-fade-in {
        animation: fade-in 0.6s ease-out forwards;
    }

    .animate-fade-in-delay-1 {
        animation: fade-in-delay-1 1s ease-out forwards;
    }

    .animate-fade-in-delay-2 {
        animation: fade-in-delay-2 1.2s ease-out forwards;
    }

    .animate-spin-slow {
        animation: spin-slow 3s linear infinite;
    }

    .animate-pulse-slow {
        animation: pulse-slow 2.5s ease-in-out infinite;
    }

    .animate-progress {
        animation: progress 2s ease-in-out;
        animation-iteration-count: infinite;
    }

    #page-loader {
        transition: opacity 0.5s ease-out, visibility 0.5s ease-out;
    }

    #page-loader.hidden {
        opacity: 0;
        visibility: hidden;
    }
</style>

<script>
    // Hide loader once page is fully loaded
    window.addEventListener('load', function() {
        const loader = document.getElementById('page-loader');
        if (loader) {
            setTimeout(() => {
                loader.classList.add('hidden');
            }, 500);
        }
    });

    // Also hide on DOMContentLoaded for faster response
    document.addEventListener('DOMContentLoaded', function() {
        const loader = document.getElementById('page-loader');
        if (loader && document.readyState === 'complete') {
            setTimeout(() => {
                loader.classList.add('hidden');
            }, 300);
        }
    });

    // Fallback: Hide after 5 seconds maximum (safety net)
    setTimeout(() => {
        const loader = document.getElementById('page-loader');
        if (loader && !loader.classList.contains('hidden')) {
            loader.classList.add('hidden');
        }
    }, 5000);
</script>
