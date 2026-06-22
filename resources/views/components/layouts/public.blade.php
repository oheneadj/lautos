<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        // I only set a generic title here if the page hasn't already set one of its own via SEOMeta.
        if (! \Artesaos\SEOTools\Facades\SEOMeta::getTitle() && ($title ?? null)) {
            \Artesaos\SEOTools\Facades\SEOMeta::setTitle($title . ' — Quality Japanese & Korean Imports');
        }
    @endphp
    {!! SEOMeta::generate() !!}
    {!! OpenGraph::generate() !!}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        @font-face {
            font-family: 'Outfit';
            src: url('/fonts/outfit/Outfit-Regular.ttf') format('truetype');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Outfit';
            src: url('/fonts/outfit/Outfit-SemiBold.ttf') format('truetype');
            font-weight: 600;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Outfit';
            src: url('/fonts/outfit/Outfit-Bold.ttf') format('truetype');
            font-weight: 700;
            font-style: normal;
            font-display: swap;
        }
    </style>
</head>
<body class="bg-base-100 text-base-content antialiased font-sans">

    {{-- Nav --}}
    <header class="bg-white sticky top-0 z-50 shadow-sm">
        <div class="max-w-[90rem] mx-auto px-4 lg:px-8 h-[72px] flex items-center justify-between gap-6">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-1.5 hover:opacity-80 transition-opacity duration-200">
                <span class="font-black text-[24px] tracking-tight text-primary">Livingston<span class="text-gray-900">Autos</span></span>
            </a>

            {{-- Desktop Nav --}}
            <nav class="hidden lg:flex items-center gap-8">
                <a href="{{ route('cars.index') }}" class="text-[15px] font-bold text-gray-800 hover:text-primary transition-colors flex items-center gap-1.5">
                    Cars for sale 
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </a>
                <a href="{{ route('blog.index') }}" class="text-[15px] font-bold text-gray-800 hover:text-primary transition-colors flex items-center gap-1.5">
                    News & reviews 
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </a>
            </nav>

            {{-- Actions --}}
            <div class="flex items-center gap-6">
                <button class="hidden md:flex items-center justify-center text-gray-600 hover:text-primary transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </button>
                @auth
                    <a href="{{ route('dashboard.index') }}" class="flex items-center gap-2 text-[15px] font-bold text-gray-800 hover:text-primary transition-colors">
                        <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Profile
                    </a>
                @else
                    <a href="{{ route('login') }}" class="flex items-center gap-2 text-[15px] font-bold text-gray-800 hover:text-primary transition-colors">
                        <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Sign In
                    </a>
                @endauth

                {{-- Mobile toggle --}}
                <button class="lg:hidden p-2 -mr-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors" x-data @click="$dispatch('toggle-mobile-menu')">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div
            class="lg:hidden border-t border-gray-100 bg-white px-4 py-3 space-y-1 shadow-lg absolute w-full"
            x-data="{ open: false }"
            @toggle-mobile-menu.window="open = !open"
            x-show="open"
            x-transition:enter="transition duration-150 ease-out"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-cloak
        >
            <a href="{{ route('cars.index') }}" class="block px-3 py-3 text-[15px] font-bold text-gray-800 rounded-lg hover:bg-gray-50 hover:text-primary">Cars for sale</a>
            <a href="{{ route('blog.index') }}" class="block px-3 py-3 text-[15px] font-bold text-gray-800 rounded-lg hover:bg-gray-50 hover:text-primary">News & reviews</a>
        </div>
    </header>

    <main>{{ $slot }}</main>

    {{-- Footer --}}
    <footer class="bg-[#1e2327] text-white mt-20 font-sans border-t-4 border-primary">
        {{-- Top Section: Main Links --}}
        <div class="max-w-[90rem] mx-auto px-4 lg:px-8 pt-16 pb-12">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-x-8 gap-y-12">
                {{-- Column 1: Shop --}}
                <div>
                    <h4 class="font-bold text-[15px] mb-6">Shop</h4>
                    <ul class="space-y-4">
                        <li><a href="#" class="text-[13px] text-gray-300 hover:text-white transition-colors">Used Cars</a></li>
                        <li><a href="#" class="text-[13px] text-gray-300 hover:text-white transition-colors">New Cars</a></li>
                        <li><a href="#" class="text-[13px] text-gray-300 hover:text-white transition-colors">Certified Pre-Owned</a></li>
                        <li><a href="#" class="text-[13px] text-gray-300 hover:text-white transition-colors">Cars for Sale by Owner</a></li>
                        <li><a href="#" class="text-[13px] text-gray-300 hover:text-white transition-colors">Find a Dealer</a></li>
                        <li><a href="#" class="text-[13px] text-gray-300 hover:text-white transition-colors">Pickup Trucks Buying Guide</a></li>
                        <li><a href="#" class="text-[13px] text-gray-300 hover:text-white transition-colors">Electric Cars Buying Guide</a></li>
                        <li><a href="#" class="text-[13px] text-gray-300 hover:text-white transition-colors">Financing</a></li>
                    </ul>
                </div>

                {{-- Column 2: Research & News --}}
                <div>
                    <h4 class="font-bold text-[15px] mb-6">Research & News</h4>
                    <ul class="space-y-4">
                        <li><a href="#" class="text-[13px] text-gray-300 hover:text-white transition-colors">Consumer Car Reviews</a></li>
                        <li><a href="#" class="text-[13px] text-gray-300 hover:text-white transition-colors">Car News & Expert Reviews</a></li>
                        <li><a href="#" class="text-[13px] text-gray-300 hover:text-white transition-colors">Compare Cars</a></li>
                        <li><a href="#" class="text-[13px] text-gray-300 hover:text-white transition-colors">Best Cars Rankings</a></li>
                        <li><a href="#" class="text-[13px] text-gray-300 hover:text-white transition-colors">Safety & Recalls</a></li>
                        <li><a href="#" class="text-[13px] text-gray-300 hover:text-white transition-colors">American-Made Index</a></li>
                        <li><a href="#" class="text-[13px] text-gray-300 hover:text-white transition-colors">Video Reviews</a></li>
                    </ul>
                </div>

                {{-- Column 4: Tools & Services --}}
                <div>
                    <h4 class="font-bold text-[15px] mb-6">Tools & Services</h4>
                    <ul class="space-y-4">
                        <li><a href="#" class="text-[13px] text-gray-300 hover:text-white transition-colors">Car Loan Calculators</a></li>
                        <li><a href="#" class="text-[13px] text-gray-300 hover:text-white transition-colors">Car Affordability Calculator</a></li>
                        <li><a href="#" class="text-[13px] text-gray-300 hover:text-white transition-colors">Ship a Car</a></li>
                        <li><a href="#" class="text-[13px] text-gray-300 hover:text-white transition-colors">Car Warranty</a></li>
                        <li><a href="#" class="text-[13px] text-gray-300 hover:text-white transition-colors">Car Insurance</a></li>
                        <li><a href="#" class="text-[13px] text-gray-300 hover:text-white transition-colors">Car Maintenance</a></li>
                        <li><a href="#" class="text-[13px] text-gray-300 hover:text-white transition-colors">Site Map</a></li>
                    </ul>
                </div>

                {{-- Column 5: Top Metro Areas (Adapted for Ghana/Global) --}}
                <div>
                    <h4 class="font-bold text-[15px] mb-6">Top Metro Areas</h4>
                    <ul class="space-y-4">
                        <li><a href="#" class="text-[13px] text-gray-300 hover:text-white transition-colors">Accra, GH</a></li>
                        <li><a href="#" class="text-[13px] text-gray-300 hover:text-white transition-colors">Kumasi, GH</a></li>
                        <li><a href="#" class="text-[13px] text-gray-300 hover:text-white transition-colors">Takoradi, GH</a></li>
                        <li><a href="#" class="text-[13px] text-gray-300 hover:text-white transition-colors">Tema, GH</a></li>
                        <li><a href="#" class="text-[13px] text-gray-300 hover:text-white transition-colors">Tamale, GH</a></li>
                        <li><a href="#" class="text-[13px] text-gray-300 hover:text-white transition-colors">Cape Coast, GH</a></li>
                        <li><a href="#" class="text-[13px] text-gray-300 hover:text-white transition-colors">Koforidua, GH</a></li>
                    </ul>
                </div>

                {{-- Column 6: Dealers, App & Socials --}}
                <div>
                    <h4 class="font-bold text-[15px] mb-6">For Dealers</h4>
                    <ul class="space-y-4 mb-8">
                        <li><a href="#" class="text-[13px] text-gray-300 hover:text-white transition-colors flex items-center gap-1">Explore Dealer Platform ↗</a></li>
                        <li><a href="#" class="text-[13px] text-gray-300 hover:text-white transition-colors flex items-center gap-1">Log In To Your Platform ↗</a></li>
                    </ul>

                    <h4 class="font-bold text-[15px] mb-4">Our Mobile App</h4>
                    <div class="flex flex-col gap-3 mb-8">
                        <a href="#" class="block w-[130px] opacity-80 hover:opacity-100 transition-opacity">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/3/3c/Download_on_the_App_Store_Badge.svg" alt="Download on the App Store">
                        </a>
                        <a href="#" class="block w-[130px] opacity-80 hover:opacity-100 transition-opacity">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" alt="Get it on Google Play">
                        </a>
                    </div>

                    <h4 class="font-bold text-[15px] mb-4">Connect With Us</h4>
                    <div class="flex items-center gap-4 text-gray-300">
                        {{-- TikTok --}}
                        <a href="#" class="hover:text-white transition-colors"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg></a>
                        {{-- Facebook --}}
                        <a href="#" class="hover:text-white transition-colors"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
                        {{-- YouTube --}}
                        <a href="#" class="hover:text-white transition-colors"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg></a>
                        {{-- Instagram --}}
                        <a href="#" class="hover:text-white transition-colors"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg></a>
                        {{-- Pinterest --}}
                        <a href="#" class="hover:text-white transition-colors"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.401.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.951-7.252 4.184 0 7.438 2.981 7.438 6.965 0 4.159-2.619 7.51-6.262 7.51-1.223 0-2.373-.635-2.766-1.385l-.754 2.871c-.273 1.042-1.015 2.345-1.512 3.141 1.118.344 2.306.529 3.529.529 6.621 0 11.988-5.368 11.988-11.988C24.004 5.367 18.638 0 12.017 0z"/></svg></a>
                    </div>
                </div>
            </div>

            {{-- Middle Section: Popular Car Models --}}
            <div class="mt-16 pt-16 border-t border-gray-700">
                <h4 class="font-bold text-[16px] mb-8">Popular Car Models</h4>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-x-8 gap-y-12">
                    {{-- Toyota --}}
                    <div>
                        <h5 class="font-bold text-[13px] mb-4 text-white">Toyota</h5>
                        <ul class="space-y-4">
                            <li><a href="#" class="text-[12px] text-gray-400 hover:text-white transition-colors">Toyota RAV4</a></li>
                            <li><a href="#" class="text-[12px] text-gray-400 hover:text-white transition-colors">Toyota Prius</a></li>
                            <li><a href="#" class="text-[12px] text-gray-400 hover:text-white transition-colors">Toyota Tacoma</a></li>
                            <li><a href="#" class="text-[12px] text-gray-400 hover:text-white transition-colors">Toyota Camry</a></li>
                            <li><a href="#" class="text-[12px] text-gray-400 hover:text-white transition-colors">Toyota 4Runner</a></li>
                        </ul>
                    </div>

                    {{-- Honda --}}
                    <div>
                        <h5 class="font-bold text-[13px] mb-4 text-white">Honda</h5>
                        <ul class="space-y-4">
                            <li><a href="#" class="text-[12px] text-gray-400 hover:text-white transition-colors">Honda CR-V</a></li>
                            <li><a href="#" class="text-[12px] text-gray-400 hover:text-white transition-colors">Honda Civic</a></li>
                            <li><a href="#" class="text-[12px] text-gray-400 hover:text-white transition-colors">Honda Accord</a></li>
                            <li><a href="#" class="text-[12px] text-gray-400 hover:text-white transition-colors">Honda Pilot</a></li>
                            <li><a href="#" class="text-[12px] text-gray-400 hover:text-white transition-colors">Honda HR-V</a></li>
                        </ul>
                    </div>

                    {{-- Ford --}}
                    <div>
                        <h5 class="font-bold text-[13px] mb-4 text-white">Ford</h5>
                        <ul class="space-y-4">
                            <li><a href="#" class="text-[12px] text-gray-400 hover:text-white transition-colors">Ford Maverick</a></li>
                            <li><a href="#" class="text-[12px] text-gray-400 hover:text-white transition-colors">Ford Bronco</a></li>
                            <li><a href="#" class="text-[12px] text-gray-400 hover:text-white transition-colors">Ford F-150</a></li>
                            <li><a href="#" class="text-[12px] text-gray-400 hover:text-white transition-colors">Ford Mustang Mach-E</a></li>
                            <li><a href="#" class="text-[12px] text-gray-400 hover:text-white transition-colors">Ford Explorer</a></li>
                        </ul>
                    </div>

                    {{-- Jeep --}}
                    <div>
                        <h5 class="font-bold text-[13px] mb-4 text-white">Jeep</h5>
                        <ul class="space-y-4">
                            <li><a href="#" class="text-[12px] text-gray-400 hover:text-white transition-colors">Jeep Wrangler</a></li>
                            <li><a href="#" class="text-[12px] text-gray-400 hover:text-white transition-colors">Jeep Grand Cherokee</a></li>
                            <li><a href="#" class="text-[12px] text-gray-400 hover:text-white transition-colors">Jeep Wagoneer</a></li>
                            <li><a href="#" class="text-[12px] text-gray-400 hover:text-white transition-colors">Jeep Grand Wagoneer</a></li>
                            <li><a href="#" class="text-[12px] text-gray-400 hover:text-white transition-colors">Jeep Cherokee</a></li>
                        </ul>
                    </div>

                    {{-- Chevrolet --}}
                    <div>
                        <h5 class="font-bold text-[13px] mb-4 text-white">Chevrolet</h5>
                        <ul class="space-y-4">
                            <li><a href="#" class="text-[12px] text-gray-400 hover:text-white transition-colors">Chevrolet Corvette</a></li>
                            <li><a href="#" class="text-[12px] text-gray-400 hover:text-white transition-colors">Chevrolet Silverado 1500</a></li>
                            <li><a href="#" class="text-[12px] text-gray-400 hover:text-white transition-colors">Chevrolet Traverse</a></li>
                            <li><a href="#" class="text-[12px] text-gray-400 hover:text-white transition-colors">Chevrolet Tahoe</a></li>
                            <li><a href="#" class="text-[12px] text-gray-400 hover:text-white transition-colors">Chevrolet Colorado</a></li>
                        </ul>
                    </div>

                    {{-- Kia --}}
                    <div>
                        <h5 class="font-bold text-[13px] mb-4 text-white">Kia</h5>
                        <ul class="space-y-4">
                            <li><a href="#" class="text-[12px] text-gray-400 hover:text-white transition-colors">Kia Sportage</a></li>
                            <li><a href="#" class="text-[12px] text-gray-400 hover:text-white transition-colors">Kia Sorento</a></li>
                            <li><a href="#" class="text-[12px] text-gray-400 hover:text-white transition-colors">Kia Stinger</a></li>
                            <li><a href="#" class="text-[12px] text-gray-400 hover:text-white transition-colors">Kia K5</a></li>
                            <li><a href="#" class="text-[12px] text-gray-400 hover:text-white transition-colors">Kia EV6</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom Section --}}
        <div class="bg-[#15191c] py-10">
            <div class="max-w-[90rem] mx-auto px-4 lg:px-8">
                <div class="flex flex-wrap items-center justify-center gap-x-8 gap-y-4 text-[12px] font-bold text-gray-300 mb-6">
                    <a href="#" class="hover:text-white transition-colors">About Livingston Autos</a>
                    <a href="#" class="hover:text-white transition-colors">Contact Us</a>
                    <a href="#" class="hover:text-white transition-colors flex items-center gap-1">Investor Relations ↗</a>
                    <a href="#" class="hover:text-white transition-colors">Careers</a>
                    <a href="#" class="hover:text-white transition-colors flex items-center gap-1">Licensing & Rights ↗</a>
                    <a href="#" class="hover:text-white transition-colors">Fraud Awareness</a>
                    <a href="#" class="hover:text-white transition-colors">Feedback</a>
                </div>
                
                <div class="flex flex-wrap items-center justify-center gap-x-8 gap-y-4 text-[12px] font-bold text-gray-300 mb-6">
                    <a href="#" class="hover:text-white transition-colors">Terms & Conditions of Use</a>
                    <a href="#" class="hover:text-white transition-colors">Privacy Notice</a>
                    <a href="#" class="hover:text-white transition-colors">Ghana Privacy Notice</a>
                    <a href="#" class="hover:text-white transition-colors flex items-center gap-1">My Privacy Choices <span class="bg-blue-500 text-white px-1 rounded text-[9px] ml-1">✓×</span></a>
                    <a href="#" class="hover:text-white transition-colors">Cookie Preferences</a>
                    <a href="#" class="hover:text-white transition-colors flex items-center gap-1">Cookie Policy ↗</a>
                    <a href="#" class="hover:text-white transition-colors">Accessibility Statement</a>
                    <a href="#" class="hover:text-white transition-colors">Ad Choices</a>
                </div>

                <div class="text-center text-[12px] text-gray-400 font-medium">
                    &copy; {{ date('Y') }} Livingston Autos. All rights reserved.
                </div>
            </div>
        </div>
    </footer>

    <x-whatsapp-button />

    @livewireScripts
</body>
</html>
