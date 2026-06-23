<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-base-200 antialiased text-base-content overflow-x-hidden flex" x-data="{ mobileMenuOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="mobileMenuOpen" x-transition.opacity @click="mobileMenuOpen = false" class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm lg:hidden" style="display: none;"></div>

    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-neutral flex flex-col transition-transform duration-300 -translate-x-full lg:translate-x-0" :class="mobileMenuOpen ? '!translate-x-0' : '-translate-x-full'">
        {{-- Logo Area --}}
        <div class="p-5 border-b border-white/[0.06] flex items-center justify-between">
            <a href="{{ route('dashboard.index') }}" wire:navigate class="flex items-center gap-3 min-w-0">
                <div class="size-10 rounded-xl bg-primary/20 border border-primary/30 flex items-center justify-center shrink-0 overflow-hidden text-primary">
                    <svg class="size-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.129-1.125V11.25M12 9h4.875c.621 0 1.125-.504 1.125-1.125V5.625c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125H9.75M9 9h3M9 12h3m1.5 0h3.75M3 12h2.25" /></svg>
                </div>
                <div class="flex flex-col leading-none min-w-0">
                    <span class="text-[15px] font-bold text-white tracking-tight truncate">{{ config('app.name', 'Livingston Autos') }}</span>
                    <span class="text-[10px] font-semibold uppercase tracking-[0.12em] text-white/35 mt-0.5">{{ __('Customer Zone') }}</span>
                </div>
            </a>
            <button @click="mobileMenuOpen = false" class="p-2 lg:hidden text-white/50 hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-4 py-6 overflow-y-auto space-y-8">
            <div class="space-y-3">
                <span class="px-3 text-[10px] font-bold uppercase tracking-widest text-white/30">{{ __('MAIN') }}</span>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('dashboard.index') }}" wire:navigate @click="mobileMenuOpen = false" class="flex items-center justify-between py-3 px-4 transition-all duration-200 group relative {{ request()->routeIs('dashboard.index') ? 'bg-[#FFC926]/10 text-[#FFC926] border-l-[3px] border-[#FFC926]' : 'text-[#f3e8cc]/70 hover:bg-[#1a1a1a] hover:text-accent border-l-[3px] border-transparent' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 {{ request()->routeIs('dashboard.index') ? 'text-[#FFC926]' : 'text-[#f3e8cc]/40 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                                <span class="text-[13px] font-medium leading-none">{{ __('Dashboard Overview') }}</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard.orders') }}" wire:navigate @click="mobileMenuOpen = false" class="flex items-center justify-between py-3 px-4 transition-all duration-200 group relative {{ request()->routeIs('dashboard.orders*') ? 'bg-[#FFC926]/10 text-[#FFC926] border-l-[3px] border-[#FFC926]' : 'text-[#f3e8cc]/70 hover:bg-[#1a1a1a] hover:text-accent border-l-[3px] border-transparent' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 {{ request()->routeIs('dashboard.orders*') ? 'text-[#FFC926]' : 'text-[#f3e8cc]/40 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                                <span class="text-[13px] font-medium leading-none">{{ __('My Car Orders') }}</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard.profile') }}" wire:navigate @click="mobileMenuOpen = false" class="flex items-center justify-between py-3 px-4 transition-all duration-200 group relative {{ request()->routeIs('dashboard.profile*') ? 'bg-[#FFC926]/10 text-[#FFC926] border-l-[3px] border-[#FFC926]' : 'text-[#f3e8cc]/70 hover:bg-[#1a1a1a] hover:text-accent border-l-[3px] border-transparent' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 {{ request()->routeIs('dashboard.profile*') ? 'text-[#FFC926]' : 'text-[#f3e8cc]/40 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                                <span class="text-[13px] font-medium leading-none">{{ __('Profile & KYC Docs') }}</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard.saved-cars') }}" wire:navigate @click="mobileMenuOpen = false" class="flex items-center justify-between py-3 px-4 transition-all duration-200 group relative {{ request()->routeIs('dashboard.saved-cars') ? 'bg-[#FFC926]/10 text-[#FFC926] border-l-[3px] border-[#FFC926]' : 'text-[#f3e8cc]/70 hover:bg-[#1a1a1a] hover:text-accent border-l-[3px] border-transparent' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 {{ request()->routeIs('dashboard.saved-cars') ? 'text-[#FFC926]' : 'text-[#f3e8cc]/40 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" /></svg>
                                <span class="text-[13px] font-medium leading-none">{{ __('Saved Cars') }}</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard.invoices') }}" wire:navigate @click="mobileMenuOpen = false" class="flex items-center justify-between py-3 px-4 transition-all duration-200 group relative {{ request()->routeIs('dashboard.invoices') ? 'bg-[#FFC926]/10 text-[#FFC926] border-l-[3px] border-[#FFC926]' : 'text-[#f3e8cc]/70 hover:bg-[#1a1a1a] hover:text-accent border-l-[3px] border-transparent' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 {{ request()->routeIs('dashboard.invoices') ? 'text-[#FFC926]' : 'text-[#f3e8cc]/40 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                                <span class="text-[13px] font-medium leading-none">{{ __('Invoices & Billing') }}</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard.support') }}" wire:navigate @click="mobileMenuOpen = false" class="flex items-center justify-between py-3 px-4 transition-all duration-200 group relative {{ request()->routeIs('dashboard.support*') ? 'bg-[#FFC926]/10 text-[#FFC926] border-l-[3px] border-[#FFC926]' : 'text-[#f3e8cc]/70 hover:bg-[#1a1a1a] hover:text-accent border-l-[3px] border-transparent' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 {{ request()->routeIs('dashboard.support*') ? 'text-[#FFC926]' : 'text-[#f3e8cc]/40 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" /></svg>
                                <span class="text-[13px] font-medium leading-none">{{ __('Support & Messages') }}</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard.notifications') }}" wire:navigate @click="mobileMenuOpen = false" class="flex items-center justify-between py-3 px-4 transition-all duration-200 group relative {{ request()->routeIs('dashboard.notifications') ? 'bg-[#FFC926]/10 text-[#FFC926] border-l-[3px] border-[#FFC926]' : 'text-[#f3e8cc]/70 hover:bg-[#1a1a1a] hover:text-accent border-l-[3px] border-transparent' }}">
                            <div class="flex items-center gap-3">
                                <div class="relative">
                                    <svg class="w-5 h-5 {{ request()->routeIs('dashboard.notifications') ? 'text-[#FFC926]' : 'text-[#f3e8cc]/40 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>
                                    @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                                        <div class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-error rounded-full border-2 border-neutral"></div>
                                    @endif
                                </div>
                                <span class="text-[13px] font-medium leading-none">{{ __('Notifications') }}</span>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="space-y-3">
                <span class="px-3 text-[10px] font-bold uppercase tracking-widest text-white/30">{{ __('ACCOUNT SETTINGS') }}</span>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('profile.edit') }}" wire:navigate @click="mobileMenuOpen = false" class="flex items-center justify-between py-3 px-4 transition-all duration-200 group relative {{ request()->routeIs('profile.edit') ? 'bg-[#FFC926]/10 text-[#FFC926] border-l-[3px] border-[#FFC926]' : 'text-[#f3e8cc]/70 hover:bg-[#1a1a1a] hover:text-accent border-l-[3px] border-transparent' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 {{ request()->routeIs('profile.edit') ? 'text-[#FFC926]' : 'text-[#f3e8cc]/40 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                                <span class="text-[13px] font-medium leading-none">{{ __('Update Profile') }}</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('security.edit') }}" wire:navigate @click="mobileMenuOpen = false" class="flex items-center justify-between py-3 px-4 transition-all duration-200 group relative {{ request()->routeIs('security.edit') ? 'bg-[#FFC926]/10 text-[#FFC926] border-l-[3px] border-[#FFC926]' : 'text-[#f3e8cc]/70 hover:bg-[#1a1a1a] hover:text-accent border-l-[3px] border-transparent' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 {{ request()->routeIs('security.edit') ? 'text-[#FFC926]' : 'text-[#f3e8cc]/40 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                                <span class="text-[13px] font-medium leading-none">{{ __('Security & 2FA') }}</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('appearance.edit') }}" wire:navigate @click="mobileMenuOpen = false" class="flex items-center justify-between py-3 px-4 transition-all duration-200 group relative {{ request()->routeIs('appearance.edit') ? 'bg-[#FFC926]/10 text-[#FFC926] border-l-[3px] border-[#FFC926]' : 'text-[#f3e8cc]/70 hover:bg-[#1a1a1a] hover:text-accent border-l-[3px] border-transparent' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 {{ request()->routeIs('appearance.edit') ? 'text-[#FFC926]' : 'text-[#f3e8cc]/40 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.879-3.879a1.5 1.5 0 10-2.12-2.12l-3.879 3.879a15.998 15.998 0 00-4.648 4.764z" /></svg>
                                <span class="text-[13px] font-medium leading-none">{{ __('Appearance') }}</span>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        {{-- User Block --}}
        <div class="mt-auto p-4 border-t border-white/10">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white text-[12px] font-bold border border-white/10 shadow-sm">
                        {{ auth()->user()->initials() }}
                    </div>
                    <div class="flex flex-col overflow-hidden">
                        <span class="text-[13px] font-semibold text-white truncate max-w-[120px]">{{ auth()->user()->name }}</span>
                        <span class="text-[11px] text-white/40 leading-none">{{ __('Customer') }}</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf
                    <button type="submit" @click.prevent="$root.submit();" class="p-2 text-white/30 hover:text-primary transition-colors" title="Logout">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 min-h-screen lg:ml-64">
        <!-- Header -->
        <header class="h-14 border-b border-base-content/5 bg-white/80 backdrop-blur-xl sticky top-0 z-30">
            <div class="h-full flex items-center justify-between px-4 lg:px-6 gap-4">
                <!-- Mobile Toggle -->
                <button @click="mobileMenuOpen = true" class="p-2 text-base-content/60 hover:text-base-content hover:bg-base-200 rounded-lg transition-colors lg:hidden">
                    <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <!-- Desktop Navigation Links (centered) -->
                <nav class="hidden lg:flex items-center gap-1 flex-1">
                    <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[13px] font-medium text-base-content/60 hover:text-base-content hover:bg-base-200/60 transition-all duration-150">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                        {{ __('Home') }}
                    </a>
                    <a href="{{ route('cars.index') }}" wire:navigate class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[13px] font-medium text-base-content/60 hover:text-base-content hover:bg-base-200/60 transition-all duration-150">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                        {{ __('Search') }}
                    </a>
                    <a href="{{ route('pages.shipping') }}" wire:navigate class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[13px] font-medium text-base-content/60 hover:text-base-content hover:bg-base-200/60 transition-all duration-150">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg>
                        {{ __('Shipping') }}
                    </a>
                    <a href="{{ route('pages.how-it-works') }}" wire:navigate class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[13px] font-medium text-base-content/60 hover:text-base-content hover:bg-base-200/60 transition-all duration-150">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" /></svg>
                        {{ __('How to Buy') }}
                    </a>
                    <a href="{{ route('about') }}" wire:navigate class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[13px] font-medium text-base-content/60 hover:text-base-content hover:bg-base-200/60 transition-all duration-150">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" /></svg>
                        {{ __('About Us') }}
                    </a>
                    <a href="{{ route('contact') }}" wire:navigate class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[13px] font-medium text-base-content/60 hover:text-base-content hover:bg-base-200/60 transition-all duration-150">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" /></svg>
                        {{ __('Help') }}
                    </a>
                </nav>

                <!-- Right Side: Notifications + User Profile -->
                <div class="flex items-center gap-2">
                    {{-- Notification Bell --}}
                    <a href="{{ route('dashboard.notifications') }}" wire:navigate class="relative p-2 rounded-lg text-base-content/50 hover:text-base-content hover:bg-base-200/60 transition-all duration-150" title="{{ __('Notifications') }}">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>
                        @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                            <span class="absolute top-1 right-1 flex items-center justify-center min-w-[16px] h-4 px-1 rounded-full bg-error text-white text-[9px] font-bold leading-none shadow-sm">
                                {{ auth()->user()->unreadNotifications->count() > 9 ? '9+' : auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </a>

                    {{-- Divider --}}
                    <div class="hidden lg:block w-px h-6 bg-base-content/10"></div>

                    {{-- User Profile Menu --}}
                    <div x-data="{ profileOpen: false }" class="relative">
                        <button @click="profileOpen = !profileOpen" class="flex items-center gap-2.5 rounded-lg px-2.5 py-1.5 hover:bg-base-200/60 transition-all duration-150 cursor-pointer">
                            <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white text-[11px] font-bold border-2 border-primary/20 shadow-sm">
                                {{ auth()->user()->initials() }}
                            </div>
                            <div class="hidden lg:flex flex-col items-start leading-none">
                                <div class="flex items-center gap-1">
                                    <span class="text-[13px] font-semibold text-base-content truncate max-w-[120px]">{{ auth()->user()->name }}</span>
                                    @if (auth()->user()->kyc_status === \App\Enums\KycStatus::Verified)
                                        <svg class="size-3.5 text-emerald-500 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" title="KYC Verified"><path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0 1 12 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 0 1 3.498 1.307 4.491 4.491 0 0 1 1.307 3.497A4.49 4.49 0 0 1 21.75 12a4.49 4.49 0 0 1-1.549 3.397 4.491 4.491 0 0 1-1.307 3.497 4.491 4.491 0 0 1-3.497 1.307A4.49 4.49 0 0 1 12 21.75a4.49 4.49 0 0 1-3.397-1.549 4.49 4.49 0 0 1-3.498-1.306 4.491 4.491 0 0 1-1.307-3.498A4.49 4.49 0 0 1 2.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 0 1 1.307-3.497 4.49 4.49 0 0 1 3.497-1.307Zm7.007 6.387a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" /></svg>
                                    @endif
                                </div>
                                <span class="text-[10px] text-base-content/40 font-medium mt-0.5">{{ __('Customer') }}</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-base-content/30 hidden lg:block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                        </button>

                        {{-- Dropdown --}}
                        <div
                            x-show="profileOpen"
                            @click.outside="profileOpen = false"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 top-full mt-2 w-56 rounded-xl border border-base-content/5 bg-white shadow-xl p-1.5 z-50"
                            style="display: none;"
                        >
                            <div class="flex items-center gap-3 px-3 py-2.5">
                                <div class="w-9 h-9 rounded-full bg-primary flex items-center justify-center text-white text-[11px] font-bold flex-shrink-0">
                                    {{ auth()->user()->initials() }}
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1">
                                        <p class="text-[13px] font-semibold text-base-content truncate">{{ auth()->user()->name }}</p>
                                        @if (auth()->user()->kyc_status === \App\Enums\KycStatus::Verified)
                                            <svg class="size-3.5 text-emerald-500 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0 1 12 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 0 1 3.498 1.307 4.491 4.491 0 0 1 1.307 3.497A4.49 4.49 0 0 1 21.75 12a4.49 4.49 0 0 1-1.549 3.397 4.491 4.491 0 0 1-1.307 3.497 4.491 4.491 0 0 1-3.497 1.307A4.49 4.49 0 0 1 12 21.75a4.49 4.49 0 0 1-3.397-1.549 4.49 4.49 0 0 1-3.498-1.306 4.491 4.491 0 0 1-1.307-3.498A4.49 4.49 0 0 1 2.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 0 1 1.307-3.497 4.49 4.49 0 0 1 3.497-1.307Zm7.007 6.387a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" /></svg>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-base-content/40 truncate">{{ auth()->user()->email }}</p>
                                </div>
                            </div>

                            <div class="my-1 h-px bg-base-content/5"></div>

                            <a href="{{ route('dashboard.index') }}" wire:navigate class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-[13px] font-medium text-base-content/70 hover:bg-base-200/60 hover:text-base-content transition-colors">
                                <svg class="w-4 h-4 text-base-content/40" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" /></svg>
                                {{ __('Dashboard') }}
                            </a>
                            <a href="{{ route('dashboard.profile') }}" wire:navigate class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-[13px] font-medium text-base-content/70 hover:bg-base-200/60 hover:text-base-content transition-colors">
                                <svg class="w-4 h-4 text-base-content/40" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                                {{ __('Profile & KYC') }}
                            </a>
                            <a href="{{ route('profile.edit') }}" wire:navigate class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-[13px] font-medium text-base-content/70 hover:bg-base-200/60 hover:text-base-content transition-colors">
                                <svg class="w-4 h-4 text-base-content/40" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                {{ __('Settings') }}
                            </a>

                            <div class="my-1 h-px bg-base-content/5"></div>

                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-[13px] font-medium text-error/70 hover:bg-error/5 hover:text-error transition-colors cursor-pointer">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" /></svg>
                                    {{ __('Log Out') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto p-6 md:p-10 lg:p-16">
            <div class="max-w-7xl mx-auto space-y-6">
                {{ $slot }}
            </div>
        </main>
    </div>

    {{-- Global Toast --}}
    <div
        x-data="{ show: false, message: '' }"
        x-on:toast.window="message = $event.detail.message; show = true; setTimeout(() => show = false, 4000)"
        x-show="show"
        x-transition
        x-cloak
        class="fixed bottom-4 right-4 z-[60] w-full max-w-sm"
    >
        <x-ui.alert type="success" title="Success">
            <span x-text="message"></span>
        </x-ui.alert>
    </div>
</body>
</html>
