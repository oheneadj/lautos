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
    {!! JsonLd::generate() !!}
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
                <a href="{{ route('about') }}" class="text-[15px] font-bold text-gray-800 hover:text-primary transition-colors">
                    About Us
                </a>
                <a href="{{ route('contact') }}" class="text-[15px] font-bold text-gray-800 hover:text-primary transition-colors">
                    Contact
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
            <a href="{{ route('about') }}" class="block px-3 py-3 text-[15px] font-bold text-gray-800 rounded-lg hover:bg-gray-50 hover:text-primary">About Us</a>
            <a href="{{ route('contact') }}" class="block px-3 py-3 text-[15px] font-bold text-gray-800 rounded-lg hover:bg-gray-50 hover:text-primary">Contact</a>
        </div>
    </header>

    <main>{{ $slot }}</main>

    {{-- Footer --}}
    <footer class="bg-[#1e2327] text-white mt-20 font-sans border-t-4 border-primary">
        {{-- Top Section: Main Links --}}
        <div class="max-w-[90rem] mx-auto px-4 lg:px-8 pt-16 pb-12">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-x-8 gap-y-12">
                
                {{-- Column 1: Inventory & Brand --}}
                <div>
                    <h4 class="font-bold text-[15px] mb-6">Inventory</h4>
                    <ul class="space-y-4">
                        <li><a href="{{ route('cars.index') }}" class="text-[13px] text-gray-300 hover:text-white transition-colors">Browse All Cars</a></li>
                        <li><a href="{{ route('cars.index') }}?status=Available" class="text-[13px] text-gray-300 hover:text-white transition-colors">Newly Added</a></li>
                        <li><a href="{{ route('cars.index') }}?status=InTransit" class="text-[13px] text-gray-300 hover:text-white transition-colors">Coming Soon (In-Transit)</a></li>
                    </ul>

                    <h4 class="font-bold text-[15px] mt-8 mb-4">Connect With Us</h4>
                    <div class="flex items-center gap-4 text-gray-300">
                        <a href="#" class="hover:text-white transition-colors"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
                        <a href="#" class="hover:text-white transition-colors"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg></a>
                    </div>
                </div>

                {{-- Column 2: Import Process --}}
                <div>
                    <h4 class="font-bold text-[15px] mb-6">Import Process</h4>
                    <ul class="space-y-4">
                        <li><a href="{{ route('pages.how-it-works') }}" class="text-[13px] text-gray-300 hover:text-white transition-colors">How It Works</a></li>
                        <li><a href="{{ route('pages.shipping') }}" class="text-[13px] text-gray-300 hover:text-white transition-colors">Shipping & Delivery</a></li>
                        <li><a href="{{ route('pages.customs-clearance') }}" class="text-[13px] text-gray-300 hover:text-white transition-colors">Customs Clearance Guide</a></li>
                        <li><a href="{{ route('pages.payment-info') }}" class="text-[13px] text-gray-300 hover:text-white transition-colors">Payment Guide</a></li>
                    </ul>
                </div>

                {{-- Column 3: Support & Trust --}}
                <div>
                    <h4 class="font-bold text-[15px] mb-6">Support & Trust</h4>
                    <ul class="space-y-4">
                        <li><a href="{{ route('contact') }}" class="text-[13px] text-gray-300 hover:text-white transition-colors">Contact Us</a></li>
                        <li><a href="{{ route('pages.faqs') }}" class="text-[13px] text-gray-300 hover:text-white transition-colors">Frequently Asked Questions</a></li>
                        <li><a href="{{ route('pages.quality-guarantee') }}" class="text-[13px] text-gray-300 hover:text-white transition-colors">Vehicle Inspections</a></li>
                        <li><a href="{{ route('pages.fraud-awareness') }}" class="text-[13px] text-error hover:text-red-400 font-bold transition-colors">Fraud Awareness</a></li>
                    </ul>
                </div>

                {{-- Column 4: Legal & Policies --}}
                <div>
                    <h4 class="font-bold text-[15px] mb-6">Legal</h4>
                    <ul class="space-y-4">
                        <li><a href="{{ route('pages.terms') }}" class="text-[13px] text-gray-300 hover:text-white transition-colors">Terms & Conditions</a></li>
                        <li><a href="{{ route('pages.privacy') }}" class="text-[13px] text-gray-300 hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="{{ route('pages.refund-policy') }}" class="text-[13px] text-gray-300 hover:text-white transition-colors">Refund & Cancellation Policy</a></li>
                        <li><a href="{{ route('about') }}" class="text-[13px] text-gray-300 hover:text-white transition-colors">About Livingston Autos</a></li>
                    </ul>
                </div>

            </div>
        </div>

        {{-- Bottom Section --}}
        <div class="bg-[#15191c] py-6 border-t border-gray-800">
            <div class="max-w-[90rem] mx-auto px-4 lg:px-8">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="text-center md:text-left text-[12px] text-gray-400 font-medium">
                        &copy; {{ date('Y') }} Livingston Autos. All rights reserved.
                    </div>
                    <div class="flex items-center gap-6 text-[12px] text-gray-400 font-medium">
                        <a href="{{ route('pages.terms') }}" class="hover:text-white transition-colors">Terms</a>
                        <a href="{{ route('pages.privacy') }}" class="hover:text-white transition-colors">Privacy</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <x-whatsapp-button />

    @livewireScripts
</body>
</html>
