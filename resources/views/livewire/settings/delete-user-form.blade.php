<section class="mt-10 space-y-6">
    <div class="relative mb-5">
        <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Delete account') }}</h3>
        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Delete your account and all of its resources') }}</p>
    </div>

    <button
        class="btn btn-error"
        x-data=""
        @click="$dispatch('open-modal', 'confirm-user-deletion')"
    >
        {{ __('Delete account') }}
    </button>

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

        <div class="relative z-10 w-full max-w-lg rounded-xl bg-white p-6 shadow-xl dark:bg-zinc-800 dark:border dark:border-zinc-700">
            <form method="POST" wire:submit="deleteUser" class="space-y-6">
                <div>
                    <h4 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Are you sure you want to delete your account?') }}</h4>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-2">
                        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                    </p>
                </div>

                <div class="space-y-2">
                    <label for="delete-password" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Password') }}</label>
                    <input wire:model="password" id="delete-password" type="password" class="input input-bordered w-full" />
                    @error('password') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                    <button type="button" class="btn btn-outline" @click="show = false">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-error">{{ __('Delete account') }}</button>
                </div>
            </form>
        </div>
    </div>
</section>
