<div>
    {{-- Filters + Search Bar --}}
    <div class="bg-base-100 border border-base-200 rounded-2xl p-5 mb-8 shadow-sm">
        <div class="flex flex-col lg:flex-row gap-3">
            {{-- Search --}}
            <div class="flex-1">
                <input
                    type="text"
                    wire:model.live.debounce.400ms="search"
                    placeholder="Search by make, model, colour, year..."
                    class="input input-bordered w-full"
                />
            </div>

            {{-- Make --}}
            <select wire:model.live="makeFilter" class="select select-bordered lg:w-44">
                <option value="">All Makes</option>
                @foreach ($makes as $make)
                    <option value="{{ $make->id }}">{{ $make->name }}</option>
                @endforeach
            </select>

            {{-- Transmission --}}
            <select wire:model.live="transmissionFilter" class="select select-bordered lg:w-40">
                <option value="">Transmission</option>
                <option value="Automatic">Automatic</option>
                <option value="Manual">Manual</option>
            </select>

            {{-- Fuel --}}
            <select wire:model.live="fuelFilter" class="select select-bordered lg:w-36">
                <option value="">Fuel Type</option>
                <option value="Petrol">Petrol</option>
                <option value="Diesel">Diesel</option>
                <option value="Hybrid">Hybrid</option>
            </select>

            {{-- Sort --}}
            <select wire:model.live="sort" class="select select-bordered lg:w-40">
                <option value="latest">Newest First</option>
                <option value="price_asc">Price: Low–High</option>
                <option value="price_desc">Price: High–Low</option>
                <option value="year_desc">Year: Newest</option>
            </select>
        </div>

        {{-- Advanced Filters Row --}}
        <div class="flex flex-wrap gap-3 mt-3 items-center">
            <input type="number" wire:model.live.debounce.400ms="minYear" placeholder="Year from" min="2000" max="{{ date('Y') }}" class="input input-bordered input-sm w-28" />
            <input type="number" wire:model.live.debounce.400ms="maxYear" placeholder="Year to" min="2000" max="{{ date('Y') }}" class="input input-bordered input-sm w-28" />
            <input type="number" wire:model.live.debounce.400ms="maxPrice" placeholder="Max price (USD)" class="input input-bordered input-sm w-40" />

            @if ($search || $makeFilter || $transmissionFilter || $fuelFilter || $minYear || $maxYear || $maxPrice)
                <button wire:click="clearFilters" class="btn btn-ghost btn-sm text-error">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Clear Filters
                </button>
            @endif
        </div>
    </div>

    {{-- Results Count --}}
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-base-content/60">
            {{ $cars->total() }} {{ Str::plural('car', $cars->total()) }} found
        </p>
    </div>

    {{-- Cars Grid --}}
    @if ($cars->isEmpty())
        <div class="text-center py-20 text-base-content/40">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 opacity-20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.85 7h10.29l1.08 3.11H5.77L6.85 7zM19 17H5v-5h14v5z"/>
                <circle cx="7.5" cy="14.5" r="1.5"/>
                <circle cx="16.5" cy="14.5" r="1.5"/>
            </svg>
            <p class="text-lg font-medium">No cars match your search</p>
            <p class="text-sm mt-1">Try adjusting your filters or <button wire:click="clearFilters" class="text-primary underline">clear all</button></p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach ($cars as $car)
                <a href="{{ route('cars.show', $car->uuid) }}" class="card bg-base-100 shadow-sm hover:shadow-md transition-all border border-base-200 group">
                    <figure class="h-48 bg-base-200 overflow-hidden relative">
                        @if ($car->images->first())
                            <img src="{{ Storage::url($car->images->first()->path) }}" alt="{{ $car->make->name }} {{ $car->carModel->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-base-content/20">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.85 7h10.29l1.08 3.11H5.77L6.85 7zM19 17H5v-5h14v5z"/>
                                    <circle cx="7.5" cy="14.5" r="1.5"/>
                                    <circle cx="16.5" cy="14.5" r="1.5"/>
                                </svg>
                            </div>
                        @endif
                        <div class="absolute top-2 right-2">
                            <span class="badge badge-primary badge-sm font-semibold shadow">{{ $car->country_of_origin }}</span>
                        </div>
                    </figure>
                    <div class="card-body p-4">
                        <h3 class="font-semibold text-base leading-tight">
                            {{ $car->year }} {{ $car->make->name }} {{ $car->carModel->name }}
                            @if ($car->carTrim)
                                <span class="text-base-content/50 font-normal text-sm">{{ $car->carTrim->name }}</span>
                            @endif
                        </h3>
                        <div class="flex flex-wrap gap-1 mt-1">
                            <span class="badge badge-ghost badge-sm">{{ $car->transmission }}</span>
                            <span class="badge badge-ghost badge-sm">{{ $car->fuel_type }}</span>
                            <span class="badge badge-ghost badge-sm">{{ $car->engine_capacity }}</span>
                            <span class="badge badge-ghost badge-sm">{{ number_format($car->mileage) }} km</span>
                        </div>
                        <div class="mt-3 pt-3 border-t border-base-200 flex items-end justify-between">
                            <div>
                                <div class="text-xl font-bold text-primary">${{ number_format($car->price_usd_cents / 100, 0) }}</div>
                                <div class="text-xs text-base-content/50">+ ${{ number_format($car->shipping_cost_usd_cents / 100, 0) }} shipping</div>
                            </div>
                            <span class="text-sm text-primary font-medium group-hover:translate-x-1 transition-transform inline-block">View →</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $cars->links() }}
        </div>
    @endif
</div>
