    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Complete Your Profile')" :description="__('Provide your KYC details for customs clearance. You need at least a Ghana Card or TIN.')" />

        <form wire:submit="submit" class="flex flex-col gap-5">
            <x-ui.input
                label="Phone Number"
                type="tel"
                id="phone"
                wire:model="phone"
                :required="true"
                autocomplete="tel"
                placeholder="+233 XX XXX XXXX"
                :error="$errors->first('phone')"
            />

            <x-ui.input
                label="Residential Address"
                type="text"
                id="address"
                wire:model="address"
                :required="true"
                autocomplete="street-address"
                placeholder="House No. / Street / City"
                :error="$errors->first('address')"
            />

            <div class="h-px bg-base-content/10 my-1"></div>

            <p class="text-[12px] font-medium text-base-content/50">
                Provide at least one: Ghana Card Number <strong>or</strong> TIN. Both are preferred.
            </p>

            <x-ui.input
                label="Ghana Card Number"
                type="text"
                id="ghana_card_number"
                wire:model="ghana_card_number"
                placeholder="GHA-XXXXXXXXX-X"
                :error="$errors->first('ghana_card_number')"
                hint="Required if no TIN provided"
            />

            <div>
                <label for="ghana_card_file" class="text-[13px] font-medium text-base-content">
                    Ghana Card Document <span class="text-base-content/40 font-normal">(optional scan/photo)</span>
                </label>
                <input
                    type="file"
                    id="ghana_card_file"
                    wire:model="ghana_card_file"
                    accept=".jpg,.jpeg,.png,.pdf"
                    class="mt-1 block w-full text-sm text-base-content/60 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-base-200 file:text-base-content/80 hover:file:bg-base-300 file:cursor-pointer"
                />
                @error('ghana_card_file')
                    <span class="text-xs text-error flex items-center gap-1 mt-1"><span>⚠</span> {{ $message }}</span>
                @enderror
            </div>

            <div class="h-px bg-base-content/10 my-1"></div>

            <x-ui.input
                label="TIN (Tax Identification Number)"
                type="text"
                id="tin_number"
                wire:model="tin_number"
                placeholder="e.g. P00XXXXXXX"
                :error="$errors->first('tin_number')"
                hint="Required if no Ghana Card provided"
            />

            <div>
                <label for="tin_file" class="text-[13px] font-medium text-base-content">
                    TIN Document <span class="text-base-content/40 font-normal">(optional scan/photo)</span>
                </label>
                <input
                    type="file"
                    id="tin_file"
                    wire:model="tin_file"
                    accept=".jpg,.jpeg,.png,.pdf"
                    class="mt-1 block w-full text-sm text-base-content/60 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-base-200 file:text-base-content/80 hover:file:bg-base-300 file:cursor-pointer"
                />
                @error('tin_file')
                    <span class="text-xs text-error flex items-center gap-1 mt-1"><span>⚠</span> {{ $message }}</span>
                @enderror
            </div>

            <div class="flex items-center justify-between gap-3 pt-2">
                <button type="button" wire:click="skip" class="text-sm text-base-content/40 hover:text-base-content/70 font-medium transition-colors cursor-pointer">
                    {{ __('Skip for now →') }}
                </button>

                <x-ui.button type="submit" variant="primary" size="lg" data-test="complete-kyc-button">
                    {{ __('Save & Continue') }}
                </x-ui.button>
            </div>
        </form>
    </div>
