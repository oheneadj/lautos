<section class="w-full">
    @include('partials.settings-heading')

    <h2 class="sr-only">{{ __('Profile settings') }}</h2>

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <x-ui.input
                label="Name"
                id="name"
                name="name"
                type="text"
                wire:model="name"
                :required="true"
                autofocus
                autocomplete="name"
                :error="$errors->first('name')"
            />

            <div>
                <x-ui.input
                    label="Email"
                    id="email"
                    name="email"
                    type="email"
                    wire:model="email"
                    :required="true"
                    autocomplete="email"
                    :error="$errors->first('email')"
                />

                @if ($this->hasUnverifiedEmail)
                    <div>
                        <p class="mt-3 text-sm text-base-content/60">
                            {{ __('Your email address is unverified.') }}
                            <button class="text-sm cursor-pointer underline text-primary hover:no-underline" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <x-ui.button type="submit" variant="primary">{{ __('Save') }}</x-ui.button>
            </div>
        </form>

        @if ($this->showDeleteUser)
            <livewire:settings.delete-user-form />
        @endif
    </x-settings.layout>
</section>
