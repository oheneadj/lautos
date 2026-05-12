<div class="max-w-7xl mx-auto px-4 lg:px-8 py-10">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-base-content/50 mb-6 flex items-center gap-2">
        <a href="{{ route('home') }}" class="hover:text-primary">Home</a>
        <span>/</span>
        <a href="{{ route('cars.index') }}" class="hover:text-primary">Cars</a>
        <span>/</span>
        <span class="text-base-content">{{ $car->year }} {{ $car->make->name }} {{ $car->carModel->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

        {{-- Images --}}
        <div>
            {{-- Main Image --}}
            <div class="rounded-2xl overflow-hidden bg-base-200 h-80 lg:h-96 mb-3">
                @if ($car->images->count())
                    <img
                        src="{{ Storage::url($car->images[$activeImageIndex]->path) }}"
                        alt="{{ $car->make->name }} {{ $car->carModel->name }}"
                        class="w-full h-full object-cover"
                    >
                @else
                    <div class="w-full h-full flex items-center justify-center text-base-content/20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.85 7h10.29l1.08 3.11H5.77L6.85 7zM19 17H5v-5h14v5z"/>
                            <circle cx="7.5" cy="14.5" r="1.5"/>
                            <circle cx="16.5" cy="14.5" r="1.5"/>
                        </svg>
                    </div>
                @endif
            </div>

            {{-- Thumbnails --}}
            @if ($car->images->count() > 1)
                <div class="flex gap-2 overflow-x-auto pb-1">
                    @foreach ($car->images as $i => $image)
                        <button
                            wire:click="setActiveImage({{ $i }})"
                            class="flex-shrink-0 w-20 h-16 rounded-lg overflow-hidden border-2 transition-all {{ $activeImageIndex === $i ? 'border-primary' : 'border-base-200 opacity-60 hover:opacity-100' }}"
                        >
                            <img src="{{ Storage::url($image->path) }}" alt="Photo {{ $i + 1 }}" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Details --}}
        <div>
            <div class="flex items-start gap-3 mb-2">
                @if ($car->make->icon_path)
                    <img src="{{ Storage::url($car->make->icon_path) }}" alt="{{ $car->make->name }}" class="h-8 w-8 object-contain mt-1">
                @endif
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold leading-tight">
                        {{ $car->year }} {{ $car->make->name }} {{ $car->carModel->name }}
                        @if ($car->carTrim)
                            <span class="text-base-content/50 font-normal">{{ $car->carTrim->name }}</span>
                        @endif
                    </h1>
                    <p class="text-base-content/50 text-sm mt-0.5">{{ $car->colour }} &bull; {{ $car->country_of_origin }}</p>
                </div>
            </div>

            {{-- Pricing --}}
            <div class="bg-primary/5 border border-primary/20 rounded-xl p-5 my-5">
                <div class="flex items-baseline gap-2 mb-1">
                    <span class="text-3xl font-bold text-primary">${{ number_format($car->price_usd_cents / 100, 0) }}</span>
                    <span class="text-base-content/50 text-sm">car price</span>
                </div>
                <div class="text-sm text-base-content/60 mb-3">
                    + ${{ number_format($car->shipping_cost_usd_cents / 100, 0) }} shipping &nbsp;=&nbsp;
                    <strong class="text-base-content">${{ number_format(($car->price_usd_cents + $car->shipping_cost_usd_cents) / 100, 0) }} total</strong>
                </div>
                @auth
                    <a href="#" class="btn btn-primary w-full">Place Order</a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-primary w-full">Create Account to Order</a>
                    <p class="text-xs text-center text-base-content/50 mt-2">Already have an account? <a href="{{ route('login') }}" class="text-primary">Login</a></p>
                @endauth
            </div>

            {{-- Specs --}}
            <div class="grid grid-cols-2 gap-3">
                @php
                    $specs = [
                        ['label' => 'Year',         'value' => $car->year],
                        ['label' => 'Engine',       'value' => $car->engine_capacity],
                        ['label' => 'Transmission', 'value' => $car->transmission],
                        ['label' => 'Fuel Type',    'value' => $car->fuel_type],
                        ['label' => 'Mileage',      'value' => number_format($car->mileage) . ' km'],
                        ['label' => 'Colour',       'value' => $car->colour],
                        ['label' => 'Origin',       'value' => $car->country_of_origin],
                    ];
                @endphp
                @foreach ($specs as $spec)
                    <div class="bg-base-200 rounded-lg px-4 py-3">
                        <div class="text-xs text-base-content/50 mb-0.5">{{ $spec['label'] }}</div>
                        <div class="font-medium text-sm">{{ $spec['value'] }}</div>
                    </div>
                @endforeach
            </div>

            {{-- Features --}}
            @if ($car->special_features)
                <div class="mt-5">
                    <h3 class="font-semibold mb-2">Special Features</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($car->special_features as $feature)
                            <span class="badge badge-outline">{{ $feature }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Enquire CTA --}}
    <div class="mt-12 bg-base-200 rounded-2xl p-8 text-center">
        <h3 class="text-lg font-bold mb-2">Have a question about this car?</h3>
        <p class="text-base-content/60 text-sm mb-4">Our team will get back to you within 24 hours.</p>
        <a href="{{ route('contact') }}?car={{ $car->uuid }}" class="btn btn-outline">Send an Enquiry</a>
    </div>

</div>
