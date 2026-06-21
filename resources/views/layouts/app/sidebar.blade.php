<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white" x-data="{ sidebarOpen: false }">
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-black/40 backdrop-blur-[2px] lg:hidden" @click="sidebarOpen = false"></div>

        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-e border-zinc-200 bg-zinc-50 transition-transform duration-200 lg:translate-x-0">
            <div class="flex h-16 items-center justify-between px-4 border-b border-zinc-200">
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <button @click="sidebarOpen = false" class="lg:hidden p-1 rounded-md hover:bg-zinc-200 text-zinc-500">
                    <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto p-4">
                <div class="mb-2">
                    <span class="px-3 text-[11px] font-bold uppercase tracking-widest text-zinc-400">{{ __('Platform') }}</span>
                </div>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-zinc-200 text-zinc-900' : 'text-zinc-600 hover:bg-zinc-200' }}">
                            <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                            {{ __('Dashboard') }}
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="flex-1"></div>

            <nav class="p-4 border-t border-zinc-200">
                <ul class="space-y-1">
                    <li>
                        <a href="https://github.com/laravel/livewire-starter-kit" target="_blank" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-zinc-600 hover:bg-zinc-200 transition-colors">
                            <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" /></svg>
                            {{ __('Repository') }}
                        </a>
                    </li>
                    <li>
                        <a href="https://laravel.com/docs/starter-kits#livewire" target="_blank" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-zinc-600 hover:bg-zinc-200 transition-colors">
                            <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.331 0 4.472.89 6.064 2.346m0-12.804A8.966 8.966 0 0118 3.75c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.346" /></svg>
                            {{ __('Documentation') }}
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="hidden lg:block border-t border-zinc-200 p-4">
                <x-desktop-user-menu :name="auth()->user()->name" />
            </div>
        </aside>

        <header class="sticky top-0 z-30 flex items-center gap-4 border-b border-zinc-200 bg-white px-4 py-3 lg:hidden">
            <button @click="sidebarOpen = true" class="p-1.5 rounded-md hover:bg-zinc-200 text-zinc-500">
                <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
            </button>
            <div class="flex-1"></div>
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm hover:bg-zinc-100">
                    <span class="flex size-8 items-center justify-center rounded-full bg-zinc-200 text-xs font-medium text-zinc-700">{{ auth()->user()->initials() }}</span>
                    <svg class="size-4 text-zinc-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 z-50 mt-2 min-w-56 rounded-xl border border-zinc-200 bg-white p-1.5 shadow-xl">
                    <div class="flex items-center gap-2 px-3 py-2">
                        <span class="flex size-8 items-center justify-center rounded-full bg-zinc-200 text-xs font-medium text-zinc-700">{{ auth()->user()->initials() }}</span>
                        <div class="grid text-start text-sm leading-tight">
                            <span class="truncate font-semibold text-zinc-900">{{ auth()->user()->name }}</span>
                            <span class="truncate text-xs text-zinc-500">{{ auth()->user()->email }}</span>
                        </div>
                    </div>
                    <div class="my-1 h-px bg-zinc-200"></div>
                    <a href="{{ route('profile.edit') }}" wire:navigate class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-100">
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        {{ __('Settings') }}
                    </a>
                    <div class="my-1 h-px bg-zinc-200"></div>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-100 cursor-pointer" data-test="logout-button">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" /></svg>
                            {{ __('Log out') }}
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <div class="lg:ml-64">
            {{ $slot }}
        </div>

        {{-- I listen for the 'toast' Livewire event here so any component on this layout can
             show a brief confirmation message without needing its own toast markup. --}}
        <div
            x-data="{ show: false, message: '' }"
            x-on:toast.window="message = $event.detail.message; show = true; setTimeout(() => show = false, 4000)"
            x-show="show"
            x-transition
            x-cloak
            class="fixed bottom-4 right-4 z-[60] w-full max-w-sm"
        >
            <x-ui.alert type="success">
                <span x-text="message"></span>
            </x-ui.alert>
        </div>
    </body>
</html>
