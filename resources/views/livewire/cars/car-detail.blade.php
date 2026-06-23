<div class="max-w-7xl mx-auto px-4 lg:px-8 py-6">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-[12px] font-medium text-gray-500 mb-4">
        <a href="{{ route('home') }}" class="hover:text-gray-900 transition-colors underline decoration-1 underline-offset-2">Home</a>
        <span class="text-gray-300">/</span>
        <a href="{{ route('cars.index') }}" class="hover:text-gray-900 transition-colors underline decoration-1 underline-offset-2">Cars for Sale</a>
        <span class="text-gray-300">/</span>
        <span class="text-gray-500">{{ $car->year }} {{ $car->make->name }} {{ $car->carModel->name }}</span>
    </nav>

    {{-- Image Gallery: Hero + 2x2 Grid --}}
    <div x-data="{ lightbox: false }" @keydown.escape.window="lightbox = false">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mb-8 h-[300px] md:h-[400px]">
            {{-- Main Hero Image --}}
            <div
                class="md:col-span-2 relative rounded-xl overflow-hidden bg-gray-100 {{ $car->images->count() ? 'cursor-zoom-in' : '' }}"
                @if ($car->images->count()) @click="lightbox = true" @endif
            >
                @if ($car->images->count() > 0)
                    <img src="{{ Storage::url($car->images[$activeImageIndex]->path) }}" class="w-full h-full object-cover" alt="{{ $car->make->name }} {{ $car->carModel->name }}">
                @else
                    <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400 text-sm">No Image</div>
                @endif

                <div class="absolute top-3 left-3">
                    <x-ui.badge :type="$car->status->colour()" dot>{{ $car->status->label() }}</x-ui.badge>
                </div>
            </div>

            {{-- 4 Grid Thumbnails --}}
            <div class="hidden md:grid grid-cols-2 grid-rows-2 gap-2 h-full">
                @for ($i = 1; $i <= 4; $i++)
                    <button
                        @if ($car->images->count() > $i) wire:click="setActiveImage({{ $i }})" @click="lightbox = true" @endif
                        class="relative rounded-xl overflow-hidden bg-gray-100 {{ $car->images->count() > $i ? 'cursor-zoom-in group' : 'cursor-default' }} {{ $i == 4 ? 'col-start-2 row-start-2' : '' }}"
                    >
                        @if ($car->images->count() > $i)
                            <img src="{{ Storage::url($car->images[$i]->path) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" alt="{{ $car->make->name }} thumbnail {{ $i }}" loading="lazy">
                        @endif

                        @if ($i == 4 && $car->images->count() > 5)
                            <div class="absolute bottom-2 right-2 bg-white/90 backdrop-blur text-gray-900 text-[12px] font-bold px-3 py-1.5 rounded-full shadow-sm">
                                +{{ $car->images->count() - 5 }} more
                            </div>
                        @endif
                    </button>
                @endfor
            </div>
        </div>

        {{-- Lightbox: click to enlarge, swipe on mobile, arrow keys / buttons on desktop --}}
        @if ($car->images->count())
            <div
                x-show="lightbox"
                x-cloak
                x-transition.opacity
                x-data="{ touchX: 0 }"
                @click.self="lightbox = false"
                @keydown.arrow-left.window="lightbox && $wire.setActiveImage(({{ $activeImageIndex }} - 1 + {{ $car->images->count() }}) % {{ $car->images->count() }})"
                @keydown.arrow-right.window="lightbox && $wire.setActiveImage(({{ $activeImageIndex }} + 1) % {{ $car->images->count() }})"
                @touchstart="touchX = $event.changedTouches[0].clientX"
                @touchend="
                    let dx = $event.changedTouches[0].clientX - touchX;
                    if (dx > 50) $wire.setActiveImage(({{ $activeImageIndex }} - 1 + {{ $car->images->count() }}) % {{ $car->images->count() }});
                    if (dx < -50) $wire.setActiveImage(({{ $activeImageIndex }} + 1) % {{ $car->images->count() }});
                "
                class="fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-4"
            >
                <button @click="lightbox = false" class="absolute top-4 right-4 text-white/80 hover:text-white" aria-label="Close gallery">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                @if ($car->images->count() > 1)
                    <button
                        @click.stop="$wire.setActiveImage(({{ $activeImageIndex }} - 1 + {{ $car->images->count() }}) % {{ $car->images->count() }})"
                        class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors"
                        aria-label="Previous photo"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button
                        @click.stop="$wire.setActiveImage(({{ $activeImageIndex }} + 1) % {{ $car->images->count() }})"
                        class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors"
                        aria-label="Next photo"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </button>
                @endif

                <img
                    src="{{ Storage::url($car->images[$activeImageIndex]->path) }}"
                    class="max-h-[90vh] max-w-[90vw] object-contain rounded-lg select-none"
                    alt="{{ $car->make->name }} {{ $car->carModel->name }}"
                >

                @if ($car->images->count() > 1)
                    <div class="absolute bottom-4 text-white/70 text-[13px]">{{ $activeImageIndex + 1 }} / {{ $car->images->count() }}</div>
                @endif
            </div>
        @endif
    </div>

    {{-- Main Car Title --}}
    <div class="flex flex-wrap items-center gap-3 mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            {{ $car->year }} {{ $car->make->name }} {{ $car->carModel->name }}
            @if ($car->carTrim)
                <span class="text-gray-400 font-normal">{{ $car->carTrim->name }}</span>
            @endif
        </h1>
        @if ($this->reservationsCount > 0)
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 text-rose-600 text-[13px] font-medium border border-rose-100" title="{{ $this->reservationsCount }} {{ Str::plural('person', $this->reservationsCount) }} reserved this car">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H11.25m4.5 0v-.001A4.5 4.5 0 0 0 11.25 9h-1.5a4.5 4.5 0 0 0-4.5 4.5v3.75m9 0H7.5" /></svg>
                {{ $this->reservationsCount }} {{ Str::plural('Reservation', $this->reservationsCount) }}
            </span>
        @endif
    </div>

    {{-- Two Column Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

        {{-- Left Column (Main Details) --}}
        <div class="lg:col-span-2 space-y-10">

            {{-- PRICING & COST SUMMARY --}}
            <div>
                <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="text-3xl font-bold text-gray-900 mb-1">${{ number_format($car->price_usd, 0) }}</div>
                    <div class="text-[13px] text-gray-500 mb-5">≈ GH₵{{ number_format($car->price_ghs, 0) }} car price</div>

                    <div class="space-y-2">
                        <div class="flex justify-between text-[13px] text-gray-600">
                            <span>Car price</span>
                            <span class="text-gray-900 font-medium">${{ number_format($car->price_usd, 0) }} <span class="text-gray-400">/ GH₵{{ number_format($car->price_ghs, 0) }}</span></span>
                        </div>
                        <div class="flex justify-between text-[13px] text-gray-600">
                            <span>Shipping to Ghana</span>
                            <span class="text-gray-900 font-medium">${{ number_format($car->shipping_cost_usd, 0) }} <span class="text-gray-400">/ GH₵{{ number_format($car->shipping_cost_ghs, 0) }}</span></span>
                        </div>
                        <div class="h-px bg-gray-200 my-2"></div>
                        <div class="flex justify-between text-[14px] font-bold text-gray-900">
                            <span>Total before clearing</span>
                            <span>${{ number_format($car->total_usd_cents / 100, 0) }} <span class="text-gray-500">/ GH₵{{ number_format($car->total_ghs, 0) }}</span></span>
                        </div>
                    </div>
                </div>
                <p class="text-[11px] text-gray-500 mt-3">
                    GHS amounts are estimates based on the current exchange rate and do not include clearing fees, which are paid separately at the port.
                </p>
            </div>

            {{-- Demurrage warning --}}
            <x-demurrage-warning />

            {{-- FEATURES & SPECS --}}
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Features & specs</h2>

                @php
                    $specs = [
                        ['label' => $car->colour . ' exterior colour',        'icon' => 'M12 21a9 9 0 110-18c4.97 0 9 3.582 9 8 0 1.06-.895 1.917-2 1.917h-2.5a2.5 2.5 0 00-1.768.732l-.464.464a2.5 2.5 0 01-1.768.732H12a2 2 0 01-2-2 2 2 0 00-2-2H6a2 2 0 01-2-2'],
                        ['label' => number_format($car->mileage) . ' km',     'icon' => 'M12 8v4l3 3M3 12a9 9 0 1118 0'],
                        ['label' => $car->transmission . ' transmission',    'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                        ['label' => $car->fuel_type . ' fuel type',          'icon' => 'M3 21h12M5 21V5a1 1 0 011-1h6a1 1 0 011 1v16M9 9h0m9 12v-7a2 2 0 00-2-2h-1m3 2v5a1 1 0 01-2 0'],
                        ['label' => $car->engine_capacity . ' engine',       'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
                        ['label' => 'Sourced from ' . $car->country_of_origin, 'icon' => 'M12 21a9 9 0 100-18 9 9 0 000 18zM3.6 9h16.8M3.6 15h16.8M12 3a14.6 14.6 0 010 18 14.6 14.6 0 010-18z'],
                    ];
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8">
                    @foreach ($specs as $spec)
                        <div class="flex gap-3 text-[14px] text-gray-700">
                            <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $spec['icon'] }}"/></svg>
                            {{ $spec['label'] }}
                        </div>
                    @endforeach
                </div>

                @if ($car->special_features)
                    <div class="mt-6">
                        <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-2">Special Features</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($car->special_features as $feature)
                                <span class="inline-flex items-center gap-1.5 pl-2 pr-3 py-1.5 rounded-full bg-primary text-white text-[13px] font-semibold shadow-sm">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    {{ $feature }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

        </div>

        {{-- Right Column --}}
        <div class="lg:col-span-1 space-y-5">

            {{-- Order CTA --}}
            <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow duration-300">
                @auth
                    @if ($car->status->value === 'available')
                        <x-ui.button type="button" wire:click="openOrderModal" variant="primary" size="lg" class="w-full justify-center">Order This Car</x-ui.button>
                    @else
                        <x-ui.button type="button" disabled variant="primary" size="lg" class="w-full justify-center opacity-50 cursor-not-allowed">{{ $car->status->label() }}</x-ui.button>
                    @endif

                    <div class="mt-3">
                        <livewire:cars.save-car-button :car="$car" :with-label="true" :key="'save-car-detail-'.$car->uuid" />
                    </div>
                @else
                    <x-ui.button href="{{ route('register') }}" variant="primary" size="lg" class="w-full justify-center">Create Account to Order</x-ui.button>
                    <p class="text-[12px] text-center text-gray-400 mt-2">
                        Already have an account?
                        <a href="{{ route('login') }}" class="text-gray-900 font-semibold hover:underline">Login</a>
                    </p>
                @endauth

                @if ($car->whatsapp_enquiry_url)
                    {{-- I use WhatsApp's own brand green here, same as the floating button —
                         it should read as "the WhatsApp button", not a themed UI element. --}}
                    <a
                        href="{{ $car->whatsapp_enquiry_url }}"
                        target="_blank"
                        rel="noopener"
                        aria-label="Chat with us on WhatsApp about this car"
                        class="mt-3 w-full inline-flex items-center justify-center gap-2 rounded-xl py-3 text-[14px] font-semibold text-white transition-opacity hover:opacity-90"
                        style="background-color: #25D366;"
                    >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.498 14.382c-.301-.15-1.767-.867-2.04-.966-.273-.101-.473-.15-.673.15-.197.295-.771.964-.944 1.162-.175.195-.349.21-.646.075-.3-.15-1.263-.465-2.403-1.485-.888-.795-1.484-1.78-1.66-2.08-.173-.3-.018-.465.13-.615.134-.135.3-.345.45-.523.146-.181.194-.301.297-.496.1-.21.049-.375-.05-.524-.1-.149-.672-1.612-.922-2.206-.246-.579-.497-.5-.683-.51-.172-.008-.371-.01-.571-.01-.2 0-.522.074-.797.359-.273.3-1.045 1.02-1.045 2.475 0 1.453 1.07 2.86 1.22 3.06.149.195 2.06 3.135 5 4.275.71.255 1.265.405 1.696.52.713.18 1.36.15 1.87.09.57-.075 1.767-.72 2.016-1.41.255-.696.255-1.29.18-1.41-.074-.135-.27-.21-.57-.36z"/><path d="M12 2C6.477 2 2 6.477 2 12c0 1.91.531 3.7 1.453 5.225L2 22l4.95-1.418A9.954 9.954 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2zm0 18.001a7.96 7.96 0 01-4.075-1.119l-.292-.174-3.025.866.866-2.94-.19-.304A7.962 7.962 0 014 12c0-4.411 3.589-8 8-8s8 3.589 8 8-3.589 8-8 8z"/></svg>
                        WhatsApp Us
                    </a>
                @endif
            </div>

            {{-- Enquiry form --}}
            <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow duration-300">
                <h2 class="text-[18px] font-bold text-gray-900 mb-1">Have a question about this car?</h2>
                <p class="text-[13px] text-gray-500 mb-4">Our team will get back to you within 24 hours.</p>
                <livewire:contact.contact-form :car-uuid="$car->uuid" :key="$car->uuid" />
            </div>
        </div>

    </div>

    {{-- Order Confirmation Modal --}}
    @auth
        @if ($showOrderModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4" wire:click.self="closeOrderModal">
                <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl">
                    <h2 class="text-[18px] font-bold text-gray-900 mb-4">Confirm Your Order</h2>

                    <div class="space-y-2 text-[14px] text-gray-700 border-y border-gray-100 py-4">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Car</span>
                            <span class="font-semibold">{{ $car->year }} {{ $car->make->name }} {{ $car->carModel->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Price</span>
                            <span>${{ number_format($car->price_usd, 0) }} (GHS {{ number_format($car->price_ghs, 0) }})</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Shipping</span>
                            <span>${{ number_format($car->shipping_cost_usd, 0) }} (GHS {{ number_format($car->shipping_cost_ghs, 0) }})</span>
                        </div>
                        <div class="flex justify-between font-bold text-gray-900 pt-2 border-t border-gray-100">
                            <span>Total</span>
                            <span>${{ number_format($car->total_usd_cents / 100, 0) }} (GHS {{ number_format($car->total_ghs, 0) }})</span>
                        </div>
                    </div>

                    @if ($this->emailUnverified)
                        <div class="mt-4 rounded-lg bg-red-50 border border-red-200 text-red-800 text-[12px] px-3 py-2 flex items-center justify-between gap-3 flex-wrap">
                            <span>Please verify your email address before placing an order.</span>
                            <button type="button" wire:click="resendVerification" class="font-semibold underline hover:no-underline whitespace-nowrap cursor-pointer">
                                Resend Verification Email
                            </button>
                        </div>
                    @elseif ($this->kycIncomplete)
                        <div class="mt-4 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-[12px] px-3 py-2">
                            Your KYC isn't complete yet. You can still place this order, but you'll need to finish KYC before your car can be delivered.
                        </div>
                    @endif

                    @error('order')
                        <p class="mt-4 text-[13px] text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="flex gap-3 mt-6">
                        <x-ui.button type="button" wire:click="closeOrderModal" variant="secondary" class="w-full justify-center">Cancel</x-ui.button>
                        <x-ui.button
                            type="button"
                            wire:click="confirmOrder"
                            variant="primary"
                            class="w-full justify-center"
                            wire:loading.attr="disabled"
                            :disabled="$this->emailUnverified"
                        >Confirm Order</x-ui.button>
                    </div>
                </div>
            </div>
        @endif
    @endauth
</div>
