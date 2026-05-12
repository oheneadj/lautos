<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <nav aria-label="{{ __('Settings') }}">
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('profile.edit') }}" wire:navigate class="flex items-center rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('profile.edit') ? 'bg-zinc-200 text-zinc-900' : 'text-zinc-600 hover:bg-zinc-200' }}">
                        {{ __('Profile') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('security.edit') }}" wire:navigate class="flex items-center rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('security.edit') ? 'bg-zinc-200 text-zinc-900' : 'text-zinc-600 hover:bg-zinc-200' }}">
                        {{ __('Security') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('appearance.edit') }}" wire:navigate class="flex items-center rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('appearance.edit') ? 'bg-zinc-200 text-zinc-900' : 'text-zinc-600 hover:bg-zinc-200' }}">
                        {{ __('Appearance') }}
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <div class="hidden md:block w-px self-stretch bg-zinc-200"></div>
    <div class="md:hidden w-full h-px bg-zinc-200"></div>

    <div class="flex-1 self-stretch max-md:pt-6 md:ps-10">
        <h2 class="text-base font-semibold text-zinc-900">{{ $heading ?? '' }}</h2>
        <p class="text-sm text-zinc-500">{{ $subheading ?? '' }}</p>

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>
</div>
