<div>
    @if ($submitted)
        <div class="alert alert-success">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Thank you! We've received your message and will be in touch within 24 hours.</span>
        </div>
    @else
        <form wire:submit="submit" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label"><span class="label-text font-medium">Full Name <span class="text-error">*</span></span></label>
                    <input type="text" wire:model="name" placeholder="John Mensah" class="input input-bordered w-full @error('name') input-error @enderror" />
                    @error('name') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="label"><span class="label-text font-medium">Email <span class="text-error">*</span></span></label>
                    <input type="email" wire:model="email" placeholder="you@example.com" class="input input-bordered w-full @error('email') input-error @enderror" />
                    @error('email') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="label"><span class="label-text font-medium">Phone (optional)</span></label>
                <input type="tel" wire:model="phone" placeholder="+233 000 000 000" class="input input-bordered w-full @error('phone') input-error @enderror" />
                @error('phone') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="label"><span class="label-text font-medium">Message <span class="text-error">*</span></span></label>
                <textarea wire:model="message" rows="5" placeholder="Tell us what you're looking for, or ask us anything..." class="textarea textarea-bordered w-full @error('message') textarea-error @enderror"></textarea>
                @error('message') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn btn-primary w-full" wire:loading.attr="disabled">
                <span wire:loading.remove>Send Message</span>
                <span wire:loading class="loading loading-spinner loading-sm"></span>
            </button>
        </form>
    @endif
</div>
