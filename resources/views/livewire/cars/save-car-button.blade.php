<span>
    @auth
        @if ($withLabel)
            <button
                type="button"
                wire:click="toggle"
                wire:loading.attr="disabled"
                wire:target="toggle"
                class="w-full inline-flex items-center justify-center gap-2 rounded-xl py-2.5 text-[14px] font-semibold border transition-colors disabled:opacity-50 disabled:cursor-not-allowed {{ $this->isSaved ? 'border-red-200 bg-red-50 text-red-600' : 'border-gray-200 text-gray-700 hover:bg-gray-50' }}"
            >
                <svg class="w-4 h-4" fill="{{ $this->isSaved ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                {{ $this->isSaved ? 'Saved' : 'Save Car' }}
            </button>
        @else
            <button
                type="button"
                wire:click="toggle"
                wire:loading.attr="disabled"
                wire:target="toggle"
                aria-label="{{ $this->isSaved ? 'Remove from saved cars' : 'Save this car' }}"
                class="bg-white rounded-full p-1.5 shadow-md hover:scale-110 transition-transform pointer-events-auto disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
            >
                <svg
                    class="w-5 h-5 {{ $this->isSaved ? 'text-red-500' : 'text-gray-900' }}"
                    fill="{{ $this->isSaved ? 'currentColor' : 'none' }}"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </button>
        @endif
    @else
        @if ($withLabel)
            <button
                type="button"
                wire:click="attemptSave"
                class="w-full inline-flex items-center justify-center gap-2 rounded-xl py-2.5 text-[14px] font-semibold border border-gray-200 text-gray-700 hover:bg-gray-50 transition-colors"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                Save Car
            </button>
        @else
            <button
                type="button"
                wire:click="attemptSave"
                aria-label="Login to save this car"
                class="bg-white rounded-full p-1.5 shadow-md hover:scale-110 transition-transform pointer-events-auto inline-flex"
            >
                <svg class="w-5 h-5 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </button>
        @endif
    @endauth

    @if ($showLoginPrompt)
        <x-ui.modal closeAction="$set('showLoginPrompt', false)" maxWidth="max-w-sm">
            <h2 class="text-lg font-semibold text-base-content mb-2">{{ __('Login to Save') }}</h2>
            <p class="text-[13px] text-base-content/60 mb-6">{{ __('Log in to save cars and find them later in your dashboard.') }}</p>
            <div class="flex gap-3">
                <x-ui.button type="button" variant="outline" wire:click="$set('showLoginPrompt', false)" class="w-full justify-center">{{ __('Cancel') }}</x-ui.button>
                <x-ui.button href="{{ route('login', ['redirect_to' => $loginRedirectUrl]) }}" variant="primary" class="w-full justify-center">{{ __('Login') }}</x-ui.button>
            </div>
        </x-ui.modal>
    @endif
</span>
