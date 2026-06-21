<section class="mt-10 space-y-6">
    <div class="relative mb-5">
        <h3 class="text-base font-semibold text-base-content">{{ __('Delete account') }}</h3>
        <p class="text-sm text-base-content/50">{{ __('Delete your account and all of its resources') }}</p>
    </div>

    <x-ui.button
        variant="danger"
        x-data=""
        @click="$dispatch('open-modal', 'confirm-user-deletion')"
    >
        {{ __('Delete account') }}
    </x-ui.button>

    {{-- Delete confirmation modal --}}
    <div
        x-data="{ show: @js($errors->isNotEmpty()) }"
        x-show="show"
        x-on:open-modal.window="if ($event.detail === 'confirm-user-deletion') show = true"
        x-on:close-modal.window="if ($event.detail === 'confirm-user-deletion') show = false"
        x-on:keydown.escape.window="show = false"
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center"
        style="display: none;"
    >
        <div class="fixed inset-0 bg-black/40 backdrop-blur-[2px]" @click="show = false"></div>

        <div class="relative z-10 w-full max-w-lg rounded-2xl bg-base-100 p-6 shadow-2xl border border-base-300">
            <form method="POST" wire:submit="deleteUser" class="space-y-6">
                <div>
                    <h4 class="text-lg font-bold text-base-content">{{ __('Are you sure you want to delete your account?') }}</h4>
                    <p class="text-sm text-base-content/50 mt-2">
                        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                    </p>
                </div>

                <x-ui.input
                    label="Password"
                    id="delete-password"
                    name="password"
                    type="password"
                    wire:model="password"
                    :error="$errors->first('password')"
                />

                <div class="flex justify-end gap-3">
                    <x-ui.button type="button" variant="outline" @click="show = false">{{ __('Cancel') }}</x-ui.button>
                    <x-ui.button type="submit" variant="danger">{{ __('Delete account') }}</x-ui.button>
                </div>
            </form>
        </div>
    </div>
</section>
