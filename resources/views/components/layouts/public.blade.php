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

</head>
<body class="bg-base-100 text-base-content antialiased font-sans">

    {{-- Nav --}}
    <header class="bg-white/80 backdrop-blur-xl sticky top-0 z-50 border-b border-gray-100" x-data="{ mobileOpen: false }">
        <div class="max-w-[90rem] mx-auto px-4 lg:px-8 h-[60px] flex items-center justify-between gap-6">

            {{-- Logo --}}
            @php $headerLogoPath = \App\Models\Setting::get('site_logo_path'); @endphp
            <a wire:navigate href="{{ route('home') }}" class="flex items-center gap-1.5 hover:opacity-80 transition-opacity duration-200 flex-shrink-0">
                @if ($headerLogoPath)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($headerLogoPath) }}" alt="{{ config('app.name') }}" class="h-7 w-auto">
                @else
                    <span class="font-black text-[22px] tracking-tight text-primary">{{ config('app.name') }}</span>
                @endif
            </a>

            {{-- Desktop Nav --}}
            <nav class="hidden lg:flex items-center gap-1 flex-1 justify-center">
                <a wire:navigate href="{{ route('home') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[13px] font-medium transition-all duration-150 {{ request()->routeIs('home') ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                    {{ __('Home') }}
                </a>
                <a wire:navigate href="{{ route('cars.index') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[13px] font-medium transition-all duration-150 {{ request()->routeIs('cars.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                    {{ __('Search') }}
                </a>
                <a wire:navigate href="{{ route('pages.shipping') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[13px] font-medium transition-all duration-150 {{ request()->routeIs('pages.shipping') ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                    {{ __('Shipping') }}
                </a>
                <a wire:navigate href="{{ route('pages.how-it-works') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[13px] font-medium transition-all duration-150 {{ request()->routeIs('pages.how-it-works') ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                    {{ __('How to Buy') }}
                </a>
                <a wire:navigate href="{{ route('about') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[13px] font-medium transition-all duration-150 {{ request()->routeIs('about') ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                    {{ __('About Us') }}
                </a>
                <a wire:navigate href="{{ route('blog.index') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[13px] font-medium transition-all duration-150 {{ request()->routeIs('blog.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                    {{ __('News & Guides') }}
                </a>
                <a wire:navigate href="{{ route('contact') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[13px] font-medium transition-all duration-150 {{ request()->routeIs('contact') ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                    {{ __('Help') }}
                </a>
            </nav>

            {{-- Right Side --}}
            <div class="flex items-center gap-2 flex-shrink-0">
                @auth
                    {{-- Notification Bell --}}
                    <a wire:navigate href="{{ route('dashboard.notifications') }}" class="relative p-2 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 transition-all duration-150" title="{{ __('Notifications') }}">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="absolute top-1 right-1 flex items-center justify-center min-w-[16px] h-4 px-1 rounded-full bg-red-500 text-white text-[9px] font-bold leading-none shadow-sm">
                                {{ auth()->user()->unreadNotifications->count() > 9 ? '9+' : auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </a>

                    <div class="hidden lg:block w-px h-6 bg-gray-200"></div>

                    {{-- User Profile Dropdown --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-gray-50 transition-all duration-150 cursor-pointer">
                            <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white text-[11px] font-bold border-2 border-primary/20 shadow-sm">
                                {{ auth()->user()->initials() }}
                            </div>
                            <span class="hidden lg:block text-[13px] font-semibold text-gray-700 truncate max-w-[100px]">{{ auth()->user()->name }}</span>
                            <svg class="w-3.5 h-3.5 text-gray-400 hidden lg:block" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                        </button>

                        <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 top-full mt-2 w-52 rounded-xl border border-gray-100 bg-white shadow-xl p-1.5 z-50" style="display: none;">
                            <div class="px-3 py-2.5">
                                <p class="text-[13px] font-semibold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-[11px] text-gray-400 truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <div class="my-1 h-px bg-gray-100"></div>
                            <a wire:navigate href="{{ route('dashboard.index') }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-[13px] font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6Z" /></svg>
                                {{ __('Dashboard') }}
                            </a>
                            <a wire:navigate href="{{ route('dashboard.orders') }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-[13px] font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                                {{ __('My Orders') }}
                            </a>
                            <a wire:navigate href="{{ route('dashboard.saved-cars') }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-[13px] font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" /></svg>
                                {{ __('Saved Cars') }}
                            </a>
                            <div class="my-1 h-px bg-gray-100"></div>
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-[13px] font-medium text-red-500/70 hover:bg-red-50 hover:text-red-600 transition-colors cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" /></svg>
                                    {{ __('Log Out') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a wire:navigate href="{{ route('login') }}" class="text-[13px] font-semibold text-gray-600 hover:text-gray-900 transition-colors px-3 py-1.5">
                        {{ __('Log in') }}
                    </a>
                    <a wire:navigate href="{{ route('register') }}" class="text-[13px] font-bold text-white bg-primary hover:bg-primary/90 px-5 py-2 rounded-lg transition-all duration-150 shadow-sm">
                        {{ __('Register') }}
                    </a>
                @endauth

                {{-- Mobile toggle --}}
                <button class="lg:hidden p-2 -mr-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors" @click="mobileOpen = !mobileOpen">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div class="lg:hidden border-t border-gray-100 bg-white px-4 py-3 space-y-1 shadow-lg absolute w-full" x-show="mobileOpen" x-transition:enter="transition duration-150 ease-out" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
            <a wire:navigate href="{{ route('home') }}" class="flex items-center gap-1.5 px-3 py-2.5 mb-1">
                @if ($headerLogoPath)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($headerLogoPath) }}" alt="{{ config('app.name') }}" class="h-6 w-auto">
                @else
                    <span class="font-black text-[18px] tracking-tight text-primary">{{ config('app.name') }}</span>
                @endif
            </a>
            <a wire:navigate href="{{ route('home') }}" class="block px-3 py-2.5 text-[14px] font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-primary">{{ __('Home') }}</a>
            <a wire:navigate href="{{ route('cars.index') }}" class="block px-3 py-2.5 text-[14px] font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-primary">{{ __('Search Cars') }}</a>
            <a wire:navigate href="{{ route('pages.shipping') }}" class="block px-3 py-2.5 text-[14px] font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-primary">{{ __('Shipping') }}</a>
            <a wire:navigate href="{{ route('pages.how-it-works') }}" class="block px-3 py-2.5 text-[14px] font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-primary">{{ __('How to Buy') }}</a>
            <a wire:navigate href="{{ route('about') }}" class="block px-3 py-2.5 text-[14px] font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-primary">{{ __('About Us') }}</a>
            <a wire:navigate href="{{ route('contact') }}" class="block px-3 py-2.5 text-[14px] font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-primary">{{ __('Help') }}</a>
            <a wire:navigate href="{{ route('blog.index') }}" class="block px-3 py-2.5 text-[14px] font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-primary">{{ __('Blog') }}</a>
        </div>
    </header>

    <main class="page-transition">{{ $slot }}</main>

    {{-- Footer --}}
    <footer class="bg-[#1e2327] text-white font-sans border-t-4 border-primary">
        {{-- Top Section: Main Links --}}
        <div class="max-w-[90rem] mx-auto px-4 lg:px-8 pt-16 pb-12">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-x-8 gap-y-12">
                
                {{-- Column 1: Inventory & Brand --}}
                <div>
                    <h4 class="font-bold text-[15px] mb-6">Inventory</h4>
                    <ul class="space-y-4">
                        <li><a wire:navigate href="{{ route('cars.index') }}" class="text-[13px] text-gray-300 hover:text-white transition-colors">Browse All Cars</a></li>
                        <li><a wire:navigate href="{{ route('cars.index') }}?status=Available" class="text-[13px] text-gray-300 hover:text-white transition-colors">Newly Added</a></li>
                        <li><a wire:navigate href="{{ route('cars.index') }}?status=InTransit" class="text-[13px] text-gray-300 hover:text-white transition-colors">Coming Soon (In-Transit)</a></li>
                    </ul>

                    @php
                        $footerFacebook = \App\Models\Setting::get('facebook_url');
                        $footerInstagram = \App\Models\Setting::get('instagram_url');
                        $footerTwitter = \App\Models\Setting::get('twitter_url');
                    @endphp
                    @if ($footerFacebook || $footerInstagram || $footerTwitter)
                        <h4 class="font-bold text-[15px] mt-8 mb-4">Connect With Us</h4>
                        <div class="flex items-center gap-4 text-gray-300">
                            @if ($footerFacebook)
                                <a href="{{ $footerFacebook }}" target="_blank" class="hover:text-white transition-colors"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
                            @endif
                            @if ($footerInstagram)
                                <a href="{{ $footerInstagram }}" target="_blank" class="hover:text-white transition-colors"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg></a>
                            @endif
                            @if ($footerTwitter)
                                <a href="{{ $footerTwitter }}" target="_blank" class="hover:text-white transition-colors"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723 10.054 10.054 0 01-3.127 1.184 4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg></a>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Column 2: Import Process --}}
                <div>
                    <h4 class="font-bold text-[15px] mb-6">Import Process</h4>
                    <ul class="space-y-4">
                        <li><a wire:navigate href="{{ route('pages.how-it-works') }}" class="text-[13px] text-gray-300 hover:text-white transition-colors">How It Works</a></li>
                        <li><a wire:navigate href="{{ route('pages.shipping') }}" class="text-[13px] text-gray-300 hover:text-white transition-colors">Shipping & Delivery</a></li>
                        <li><a wire:navigate href="{{ route('pages.customs-clearance') }}" class="text-[13px] text-gray-300 hover:text-white transition-colors">Customs Clearance Guide</a></li>
                        <li><a wire:navigate href="{{ route('pages.payment-info') }}" class="text-[13px] text-gray-300 hover:text-white transition-colors">Payment Guide</a></li>
                    </ul>
                </div>

                {{-- Column 3: Support & Trust --}}
                <div>
                    <h4 class="font-bold text-[15px] mb-6">Support & Trust</h4>
                    <ul class="space-y-4">
                        <li><a wire:navigate href="{{ route('contact') }}" class="text-[13px] text-gray-300 hover:text-white transition-colors">Contact Us</a></li>
                        <li><a wire:navigate href="{{ route('pages.faqs') }}" class="text-[13px] text-gray-300 hover:text-white transition-colors">Frequently Asked Questions</a></li>
                        <li><a wire:navigate href="{{ route('pages.quality-guarantee') }}" class="text-[13px] text-gray-300 hover:text-white transition-colors">Vehicle Inspections</a></li>
                        <li><a wire:navigate href="{{ route('pages.fraud-awareness') }}" class="text-[13px] text-error hover:text-red-400 font-bold transition-colors">Fraud Awareness</a></li>
                    </ul>
                </div>

                {{-- Column 4: Legal & Policies --}}
                <div>
                    <h4 class="font-bold text-[15px] mb-6">Legal</h4>
                    <ul class="space-y-4">
                        <li><a wire:navigate href="{{ route('pages.terms') }}" class="text-[13px] text-gray-300 hover:text-white transition-colors">Terms & Conditions</a></li>
                        <li><a wire:navigate href="{{ route('pages.privacy') }}" class="text-[13px] text-gray-300 hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a wire:navigate href="{{ route('pages.refund-policy') }}" class="text-[13px] text-gray-300 hover:text-white transition-colors">Refund & Cancellation Policy</a></li>
                        <li><a wire:navigate href="{{ route('about') }}" class="text-[13px] text-gray-300 hover:text-white transition-colors">About Livingston Autos</a></li>
                    </ul>
                </div>

            </div>
        </div>

        {{-- Bottom Section --}}
        <div class="bg-[#15191c] py-6 border-t border-gray-800">
            <div class="max-w-[90rem] mx-auto px-4 lg:px-8">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="text-center md:text-left text-[12px] text-gray-400 font-medium">
                        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                    </div>
                    <div class="flex items-center gap-6 text-[12px] text-gray-400 font-medium">
                        <a wire:navigate href="{{ route('pages.terms') }}" class="hover:text-white transition-colors">Terms</a>
                        <a wire:navigate href="{{ route('pages.privacy') }}" class="hover:text-white transition-colors">Privacy</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <x-whatsapp-button />
    <x-cookie-consent />

    @livewireScripts
</body>
</html>
