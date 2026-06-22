<div class="flex flex-col md:flex-row gap-6 md:gap-10 items-start">
    <div class="w-full md:w-[240px] shrink-0">
        <nav aria-label="{{ __('Settings') }}" class="bg-base-200 p-2 rounded-xl border border-base-content/5">
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('profile.edit') }}" wire:navigate class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-[13px] font-medium transition-all {{ request()->routeIs('profile.edit') ? 'bg-base-100 text-base-content shadow-sm border border-base-content/10' : 'text-base-content/60 hover:bg-base-100/50 hover:text-base-content border border-transparent' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('profile.edit') ? 'text-primary' : 'text-base-content/60' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                        {{ __('Profile') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('security.edit') }}" wire:navigate class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-[13px] font-medium transition-all {{ request()->routeIs('security.edit') ? 'bg-base-100 text-base-content shadow-sm border border-base-content/10' : 'text-base-content/60 hover:bg-base-100/50 hover:text-base-content border border-transparent' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('security.edit') ? 'text-primary' : 'text-base-content/60' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                        {{ __('Security & 2FA') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('appearance.edit') }}" wire:navigate class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-[13px] font-medium transition-all {{ request()->routeIs('appearance.edit') ? 'bg-base-100 text-base-content shadow-sm border border-base-content/10' : 'text-base-content/60 hover:bg-base-100/50 hover:text-base-content border border-transparent' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('appearance.edit') ? 'text-primary' : 'text-base-content/60' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.879-3.879a1.5 1.5 0 10-2.12-2.12l-3.879 3.879a15.998 15.998 0 00-4.648 4.764z" /></svg>
                        {{ __('Appearance') }}
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <div class="flex-1 w-full max-w-3xl">
        <div class="bg-white border border-base-content/5 shadow-sm rounded-xl overflow-hidden">
            <div class="px-6 py-5 border-b border-base-content/5 bg-base-100">
                <h2 class="text-lg font-semibold text-base-content leading-tight">{{ $heading ?? '' }}</h2>
                <p class="text-[13px] text-base-content/50 mt-1">{{ $subheading ?? '' }}</p>
            </div>
            
            <div class="p-6 md:p-8 space-y-8 bg-base-100">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
