<div class="space-y-10">
    {{-- Page Header --}}
    <div>
        <h1 class="text-[28px] font-semibold text-base-content leading-tight">{{ __('My Reviews') }}</h1>
        <p class="text-[14px] text-base-content/50 mt-1">{{ __('Share your experience with cars you have purchased') }}</p>
    </div>

    {{-- Delivered orders awaiting a review --}}
    @if ($this->reviewableOrders->isNotEmpty())
        <div class="space-y-4">
            <h2 class="text-[13px] font-bold uppercase tracking-widest text-base-content/40">{{ __('Awaiting Your Review') }}</h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($this->reviewableOrders as $order)
                    <x-ui.card class="p-5 flex flex-col">
                        <p class="text-[15px] font-bold text-base-content leading-tight">
                            {{ $order->car->year }} {{ $order->car->make->name }} {{ $order->car->carModel->name }}
                        </p>
                        <p class="text-[12px] text-base-content/40 mt-1">{{ __('Order') }} {{ $order->reference }}</p>
                        <button
                            wire:click="startReview('{{ $order->uuid }}')"
                            class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-[13px] font-medium text-white hover:brightness-110 transition-all duration-150"
                        >
                            {{ __('Write a Review') }}
                        </button>
                    </x-ui.card>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Submitted reviews --}}
    <div class="space-y-4">
        <h2 class="text-[13px] font-bold uppercase tracking-widest text-base-content/40">{{ __('Submitted Reviews') }}</h2>

        @if ($this->submittedReviews->isEmpty())
            <x-ui.card class="p-14 text-center">
                <svg class="mx-auto w-12 h-12 text-base-content/20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" /></svg>
                <p class="mt-3 text-[15px] font-bold text-base-content">{{ __('No reviews yet') }}</p>
                <p class="mt-1 text-[13px] text-base-content/40">{{ __('Once we deliver your car, you can leave a review for it here.') }}</p>
            </x-ui.card>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($this->submittedReviews as $review)
                    <x-ui.card class="p-5 flex flex-col">
                        <div class="flex items-center justify-between">
                            <div class="flex gap-0.5 text-secondary">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $review->rating ? '' : 'text-base-content/15' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @endfor
                            </div>
                            <x-ui.badge :type="$review->status->colour()">{{ $review->status->label() }}</x-ui.badge>
                        </div>
                        <p class="text-[12px] text-base-content/40 mt-2">
                            {{ $review->order->car->year }} {{ $review->order->car->make->name }} {{ $review->order->car->carModel->name }}
                        </p>
                        <h3 class="font-bold text-[14px] text-base-content mt-2 leading-snug">{{ $review->title }}</h3>
                        <p class="text-[13px] text-base-content/60 leading-relaxed mt-1 flex-1">{{ $review->body }}</p>
                    </x-ui.card>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Write a Review --}}
    @if ($showFormModal)
        <x-ui.modal closeAction="$set('showFormModal', false)">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-base-content">{{ __('Write a Review') }}</h2>
                <button wire:click="$set('showFormModal', false)" class="text-base-content/40 hover:text-base-content">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form wire:submit="submitReview" class="space-y-4">
                <div>
                    <label class="block text-[13px] font-medium text-base-content mb-2">{{ __('Rating') }}</label>
                    <div class="flex gap-1" x-data>
                        @for ($i = 1; $i <= 5; $i++)
                            <button type="button" wire:click="$set('rating', {{ $i }})" class="text-secondary">
                                <svg class="w-7 h-7 {{ $i <= $rating ? '' : 'text-base-content/15' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </button>
                        @endfor
                    </div>
                    @error('rating') <span class="text-[12px] text-error block mt-1">{{ $message }}</span> @enderror
                </div>

                <x-ui.input
                    label="{{ __('Title') }}"
                    id="review-title"
                    wire:model="title"
                    placeholder="{{ __('Sum up your experience') }}"
                    required
                />

                <x-ui.textarea
                    label="{{ __('Review') }}"
                    id="review-body"
                    wire:model="body"
                    placeholder="{{ __('Tell other buyers about your experience...') }}"
                    rows="5"
                    required
                />

                <div class="flex justify-end gap-3 mt-6">
                    <x-ui.button type="button" variant="outline" wire:click="$set('showFormModal', false)">{{ __('Cancel') }}</x-ui.button>
                    <x-ui.button type="submit" variant="primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="submitReview">{{ __('Submit Review') }}</span>
                        <span wire:loading wire:target="submitReview">{{ __('Submitting...') }}</span>
                    </x-ui.button>
                </div>
            </form>
        </x-ui.modal>
    @endif
</div>
