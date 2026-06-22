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
        <!-- Header for mobile (hamburger) and desktop (Browse/Home links) -->
        <header class="h-16 border-b border-base-content/5 bg-white/50 backdrop-blur-md sticky top-0 z-30 flex items-center justify-between px-6 lg:justify-end">
            <!-- Mobile Toggle -->
            <button @click="mobileMenuOpen = true" class="p-2 text-base-content/60 hover:text-base-content hover:bg-base-200 rounded-lg transition-colors lg:hidden">
                <svg class="size-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>

            <!-- Desktop quick links -->
            <div class="flex items-center gap-3">
                <a href="{{ route('cars.index') }}" wire:navigate class="rounded-lg border border-base-content/10 bg-base-100 px-3 py-1.5 text-[12px] font-semibold text-base-content hover:bg-base-200 transition-all shadow-[0_1px_2px_rgba(0,0,0,0.04)] hidden lg:block">
                    {{ __('Browse Cars') }}
                </a>
                <a href="{{ route('home') }}" wire:navigate class="rounded-lg bg-base-100 px-3 py-1.5 text-[12px] font-semibold text-base-content hover:bg-base-200 transition-all shadow-[0_1px_2px_rgba(0,0,0,0.04)] hidden lg:block border border-base-content/10">
                    {{ __('Visit Homepage') }}
                </a>
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
