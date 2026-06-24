{{-- I set wire:key here (not just on the @foreach) since this partial is @included
     from inside Livewire-rendered loops — without it, morphing on re-render (filtering,
     pagination) can detach this card's Alpine slider state from the wrong DOM node. --}}
<div wire:key="car-card-{{ $car->uuid }}" class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-300 group flex flex-col h-full relative">

    {{-- Top Badges Overlay --}}
    <div class="absolute top-0 left-0 w-full z-10 p-3 flex justify-between items-start pointer-events-none">
        {{-- Status Badge --}}
        <div class="pointer-events-auto">
            {{-- The badge's normal style uses a translucent tint background, which all but
                 disappears sitting on top of a photo — I force a solid white backdrop here. --}}
            <x-ui.badge :type="$car->status->colour()" dot class="!bg-white shadow-sm">{{ $car->status->label() }}</x-ui.badge>
        </div>
        {{-- Heart Icon --}}
        <div class="pointer-events-auto">
            @livewire('cars.save-car-button', ['car' => $car], 'save-car-'.$car->uuid)
        </div>
    </div>

    {{-- Image Container --}}
    <div x-data="{ active: 0, total: {{ $car->images->count() }} }" class="relative h-[220px] bg-gray-100 overflow-hidden shrink-0">
        <a href="{{ route('cars.show', $car->slug) }}" class="absolute inset-0 block">
            @forelse ($car->images as $index => $image)
                <img
                    x-show="active === {{ $index }}"
                    src="{{ Storage::url($image->path) }}"
                    alt="{{ $car->make->name }} {{ $car->carModel->name }}"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                    loading="lazy"
                >
            @empty
                <div class="w-full h-full flex items-center justify-center bg-gray-200">
                    <svg class="h-12 w-12 text-gray-400" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.85 7h10.29l1.08 3.11H5.77L6.85 7zM19 17H5v-5h14v5z"/>
                    </svg>
                </div>
            @endforelse

            {{-- Diagonal/Corner styling accents (simulating the image design) --}}
            <div class="absolute -top-16 -right-16 w-32 h-32 bg-black transform rotate-45 opacity-90 z-0"></div>
            <div class="absolute -bottom-16 -left-16 w-32 h-32 bg-black transform rotate-45 opacity-90 z-0"></div>
        </a>

        @if ($car->images->count() > 1)
            {{-- Prev/Next Arrows — hidden until the card is hovered --}}
            <button
                @click.prevent.stop="active = (active - 1 + total) % total"
                class="absolute left-2 top-1/2 -translate-y-1/2 z-10 w-7 h-7 rounded-full bg-white/90 hover:bg-white text-gray-900 shadow-sm flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                aria-label="Previous photo"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
            </button>
            <button
                @click.prevent.stop="active = (active + 1) % total"
                class="absolute right-2 top-1/2 -translate-y-1/2 z-10 w-7 h-7 rounded-full bg-white/90 hover:bg-white text-gray-900 shadow-sm flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                aria-label="Next photo"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
            </button>

            {{-- Pagination Dots --}}
            <div class="absolute bottom-3 left-1/2 -translate-x-1/2 bg-gray-600/60 backdrop-blur-sm rounded-full px-2.5 py-1.5 flex gap-1.5 z-10">
                @foreach ($car->images as $index => $image)
                    <button
                        @click.prevent.stop="active = {{ $index }}"
                        :class="active === {{ $index }} ? 'w-3 bg-white' : 'w-1.5 bg-white/60'"
                        class="h-1.5 rounded-full transition-all"
                        aria-label="Show photo {{ $index + 1 }}"
                    ></button>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Body --}}
    <div class="p-4 flex flex-col flex-1 bg-white">
        {{-- Pricing Row --}}
        <div class="flex justify-between items-end mb-1">
            <div class="flex items-baseline gap-2">
                <span class="text-[26px] font-bold text-gray-900 leading-none">${{ number_format($car->price_usd_cents / 100, 0) }}</span>
                <span class="text-sm font-medium text-secondary flex items-center">
                    GH₵{{ number_format($car->price_ghs, 0) }}
                </span>
            </div>
            <div class="flex items-center text-sm text-gray-600 font-medium pb-0.5">
                <svg class="w-3.5 h-3.5 mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ number_format($car->mileage) }} km
            </div>
        </div>

        {{-- Shipping --}}
        <div class="text-sm text-gray-600 font-medium mb-3 flex items-center gap-1">
            <span class="underline decoration-gray-400 decoration-1 underline-offset-2">+ ${{ number_format($car->shipping_cost_usd, 0) }} shipping</span>
            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>

        {{-- Title --}}
        <a href="{{ route('cars.show', $car->slug) }}" class="text-base font-bold text-gray-900 leading-tight mb-3 hover:text-primary transition-colors block">
            {{ $car->year }} {{ $car->make->name }} {{ $car->carModel->name }} {{ $car->carTrim?->name }}
        </a>

        {{-- Badges --}}
        <div class="flex flex-wrap gap-2 mb-4">
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-primary/10 text-primary text-xs font-medium border border-primary/20">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ $car->transmission }}
            </span>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-gray-100 text-gray-600 text-xs font-medium border border-gray-200">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h12M5 21V5a1 1 0 011-1h6a1 1 0 011 1v16M9 9h0m9 12v-7a2 2 0 00-2-2h-1m3 2v5a1 1 0 01-2 0"/></svg>
                {{ $car->fuel_type }}
            </span>
            @if (($car->orders_count ?? 0) > 0)
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-rose-50 text-rose-600 text-xs font-medium border border-rose-100" title="{{ $car->orders_count }} {{ Str::plural('person', $car->orders_count) }} reserved this car">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H11.25m4.5 0v-.001A4.5 4.5 0 0 0 11.25 9h-1.5a4.5 4.5 0 0 0-4.5 4.5v3.75m9 0H7.5" /></svg>
                    {{ $car->orders_count }} {{ Str::plural('Reservation', $car->orders_count) }}
                </span>
            @endif
        </div>



        {{-- CTA Button --}}
        <div class="mt-auto">
            <a href="{{ route('cars.show', $car->slug) }}" class="inline-block bg-primary text-white text-sm font-bold py-2.5 px-5 rounded-full hover:bg-primary/90 transition-colors">
                Check Availability
            </a>
        </div>
    </div>

    {{-- Footer Bar --}}
    <div class="bg-[#f8f9fa] px-4 py-3 flex justify-between items-center mt-auto border-t border-gray-200">
        <div class="flex items-center gap-1.5 text-sm text-gray-600 font-medium">
            <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Imported from {{ $car->country_of_origin }}
        </div>
        <a href="{{ route('cars.show', $car->slug) }}" class="text-sm font-bold text-gray-900 underline decoration-2 underline-offset-2 hover:text-primary transition-colors">Quick view</a>
    </div>
</div>
