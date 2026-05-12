<section class="w-full">
    @include('partials.settings-heading')

    <h2 class="sr-only">{{ __('Profile settings') }}</h2>

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <div class="space-y-2">
                <label for="name" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Name') }}</label>
                <input wire:model="name" id="name" type="text" required autofocus autocomplete="name" class="input input-bordered w-full" />
                @error('name') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Email') }}</label>
                    <input wire:model="email" id="email" type="email" required autocomplete="email" class="input input-bordered w-full" />
                    @error('email') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                @if ($this->hasUnverifiedEmail)
                    <div>
                        <p class="mt-4 text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Your email address is unverified.') }}
                            <button class="text-sm cursor-pointer underline text-zinc-900 dark:text-zinc-100 hover:no-underline" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
            </div>
        </form>

        @if ($this->showDeleteUser)
            <livewire:settings.delete-user-form />
        @endif
    </x-settings.layout>
</section>
