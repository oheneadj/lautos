<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Livingston Autos' }} — Quality Japanese & Korean Imports</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-base-100 text-base-content antialiased">

    {{-- Navbar --}}
    <nav class="navbar bg-base-100 border-b border-base-200 sticky top-0 z-50 px-4 lg:px-8">
        <div class="navbar-start">
            <div class="dropdown lg:hidden">
                <label tabindex="0" class="btn btn-ghost btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </label>
                <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                    <li><a href="{{ route('cars.index') }}">Browse Cars</a></li>
                    <li><a href="{{ route('blog.index') }}">Blog</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                    @auth
                        <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    @else
                        <li><a href="{{ route('login') }}">Login</a></li>
                    @endauth
                </ul>
            </div>
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-xl font-bold text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.85 7h10.29l1.08 3.11H5.77L6.85 7zM19 17H5v-5h14v5z"/>
                    <circle cx="7.5" cy="14.5" r="1.5"/>
                    <circle cx="16.5" cy="14.5" r="1.5"/>
                </svg>
                Livingston Autos
            </a>
        </div>

        <div class="navbar-center hidden lg:flex">
            <ul class="menu menu-horizontal px-1 gap-1">
                <li><a href="{{ route('cars.index') }}" class="font-medium hover:text-primary {{ request()->routeIs('cars.*') ? 'text-primary' : '' }}">Browse Cars</a></li>
                <li><a href="{{ route('blog.index') }}" class="font-medium hover:text-primary {{ request()->routeIs('blog.*') ? 'text-primary' : '' }}">Blog</a></li>
                <li><a href="{{ route('contact') }}" class="font-medium hover:text-primary {{ request()->routeIs('contact') ? 'text-primary' : '' }}">Contact</a></li>
            </ul>
        </div>

        <div class="navbar-end gap-2">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-ghost btn-sm hidden lg:inline-flex">Login</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Get Started</a>
            @endauth
        </div>
    </nav>

    {{-- Page Content --}}
    <main>
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="bg-base-200 border-t border-base-300 mt-20">
        <div class="max-w-7xl mx-auto px-4 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="font-bold text-lg mb-3 text-primary">Livingston Autos</h3>
                    <p class="text-base-content/70 text-sm leading-relaxed">
                        Ghana's trusted source for quality Japanese and Korean imported vehicles.
                        We handle everything from sourcing to delivery.
                    </p>
                </div>
                <div>
                    <h4 class="font-semibold mb-3">Quick Links</h4>
                    <ul class="space-y-2 text-sm text-base-content/70">
                        <li><a href="{{ route('cars.index') }}" class="hover:text-primary">Browse Cars</a></li>
                        <li><a href="{{ route('blog.index') }}" class="hover:text-primary">Blog & News</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-primary">Contact Us</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-3">Contact</h4>
                    <ul class="space-y-2 text-sm text-base-content/70">
                        <li>📍 Accra, Ghana</li>
                        <li>📞 <a href="tel:+233000000000" class="hover:text-primary">+233 000 000 000</a></li>
                        <li>✉️ <a href="mailto:info@livingstonautos.com" class="hover:text-primary">info@livingstonautos.com</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-base-300 mt-8 pt-6 text-center text-sm text-base-content/50">
                &copy; {{ date('Y') }} Livingston Autos. All rights reserved.
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
