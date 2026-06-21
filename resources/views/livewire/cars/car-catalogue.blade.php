<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

    {{-- Sidebar Filters --}}
    <aside class="lg:col-span-1" x-data="{ open: { make: true, price: true, year: true, fuel: true, transmission: true, country: true } }">
        <div class="bg-white border border-gray-200 rounded-lg p-5 lg:sticky lg:top-24">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-[15px] font-bold text-gray-900">Filters</h2>
                @if ($search || $makeFilter || $transmissionFilter || $fuelFilter || $countryFilter || $minYear || $maxYear || $maxPriceGhs)
                    <button wire:click="clearFilters" class="text-[12px] font-medium text-gray-600 hover:text-gray-900 transition-colors">Clear all</button>
                @endif
            </div>

            {{-- Search --}}
            <div class="mb-5">
                <label class="block text-[12px] font-bold text-gray-900 mb-2">Search</label>
                <input
                    type="text"
                    wire:model.live.debounce.400ms="search"
                    placeholder="Brand, model, colour..."
                    class="w-full border border-gray-300 rounded text-[14px] py-2 px-3 focus:ring-primary focus:border-primary"
                >
            </div>

            {{-- Make --}}
            <div class="border-t border-gray-100 py-4">
                <button @click="open.make = !open.make" class="w-full flex items-center justify-between text-[13px] font-bold text-gray-900">
                    Make
                    <svg class="w-3.5 h-3.5 text-gray-500 transition-transform" :class="open.make && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open.make" class="mt-3">
                    <select wire:model.live="makeFilter" class="w-full bg-white border border-gray-300 rounded text-[14px] py-2 px-3 focus:ring-primary focus:border-primary">
                        <option value="">All Makes</option>
                        @foreach ($makes as $make)
                            <option value="{{ $make->slug }}">{{ $make->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Price (GHS) --}}
            <div class="border-t border-gray-100 py-4">
                <button @click="open.price = !open.price" class="w-full flex items-center justify-between text-[13px] font-bold text-gray-900">
                    Car Price (GH₵)
                    <svg class="w-3.5 h-3.5 text-gray-500 transition-transform" :class="open.price && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open.price" class="mt-3">
                    <input type="number" wire:model.live.debounce.500ms="maxPriceGhs" placeholder="Maximum price" class="w-full border border-gray-300 rounded text-center py-2 text-[14px] focus:ring-primary focus:border-primary mb-3">
                    <div class="flex flex-wrap gap-2">
                        <button wire:click="$set('maxPriceGhs', 100000)" class="rounded-full px-3.5 py-1.5 text-[11px] font-semibold bg-gray-900 text-white hover:bg-black transition-colors">Under 100K</button>
                        <button wire:click="$set('maxPriceGhs', 300000)" class="rounded-full px-3.5 py-1.5 text-[11px] font-semibold bg-gray-900 text-white hover:bg-black transition-colors">Under 300K</button>
                        <button wire:click="$set('maxPriceGhs', 600000)" class="rounded-full px-3.5 py-1.5 text-[11px] font-semibold bg-gray-900 text-white hover:bg-black transition-colors">Under 600K</button>
                    </div>
                </div>
            </div>

            {{-- Year --}}
            <div class="border-t border-gray-100 py-4">
                <button @click="open.year = !open.year" class="w-full flex items-center justify-between text-[13px] font-bold text-gray-900">
                    Year
                    <svg class="w-3.5 h-3.5 text-gray-500 transition-transform" :class="open.year && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open.year" class="mt-3 flex items-center gap-3">
                    @php $yearOptions = range((int) date('Y'), 2000); @endphp
                    <select wire:model.live="minYear" class="w-full bg-white border border-gray-300 rounded text-[14px] py-2 px-2 text-center focus:ring-primary focus:border-primary">
                        <option value="">From</option>
                        @foreach ($yearOptions as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                    <span class="text-gray-400 font-bold">—</span>
                    <select wire:model.live="maxYear" class="w-full bg-white border border-gray-300 rounded text-[14px] py-2 px-2 text-center focus:ring-primary focus:border-primary">
                        <option value="">To</option>
                        @foreach ($yearOptions as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Fuel Type --}}
            <div class="border-t border-gray-100 py-4">
                <button @click="open.fuel = !open.fuel" class="w-full flex items-center justify-between text-[13px] font-bold text-gray-900">
                    Fuel Type
                    <svg class="w-3.5 h-3.5 text-gray-500 transition-transform" :class="open.fuel && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open.fuel" class="mt-3 space-y-1">
                    @foreach (\App\Models\Car::FUEL_TYPES as $fuelType)
                        <label class="flex items-center gap-2 text-[13px] text-gray-700 cursor-pointer">
                            <input type="radio" wire:model.live="fuelFilter" value="{{ $fuelType }}" class="text-primary focus:ring-primary">
                            {{ $fuelType }}
                        </label>
                    @endforeach
                    <label class="flex items-center gap-2 text-[13px] text-gray-700 cursor-pointer">
                        <input type="radio" wire:model.live="fuelFilter" value="" class="text-primary focus:ring-primary">
                        Any
                    </label>
                </div>
            </div>

            {{-- Transmission --}}
            <div class="border-t border-gray-100 py-4">
                <button @click="open.transmission = !open.transmission" class="w-full flex items-center justify-between text-[13px] font-bold text-gray-900">
                    Transmission
                    <svg class="w-3.5 h-3.5 text-gray-500 transition-transform" :class="open.transmission && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open.transmission" class="mt-3 flex flex-wrap gap-2">
                    @foreach (\App\Models\Car::TRANSMISSIONS as $transmission)
                        <button
                            wire:click="$set('transmissionFilter', '{{ $transmissionFilter === $transmission ? '' : $transmission }}')"
                            class="rounded-full px-3.5 py-1.5 text-[12px] font-semibold transition-colors {{ $transmissionFilter === $transmission ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-900 hover:text-white' }}"
                        >
                            {{ $transmission }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Country of Origin --}}
            <div class="border-t border-gray-100 py-4">
                <button @click="open.country = !open.country" class="w-full flex items-center justify-between text-[13px] font-bold text-gray-900">
                    Country of Origin
                    <svg class="w-3.5 h-3.5 text-gray-500 transition-transform" :class="open.country && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open.country" class="mt-3">
                    <select wire:model.live="countryFilter" class="w-full bg-white border border-gray-300 rounded text-[14px] py-2 px-3 focus:ring-primary focus:border-primary">
                        <option value="">Any Country</option>
                        @foreach (\App\Models\Car::COUNTRIES_OF_ORIGIN as $country)
                            <option value="{{ $country }}">{{ $country }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </aside>

    {{-- Results --}}
    <div class="lg:col-span-3">

        {{-- Header: title + sort --}}
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-[20px] font-bold text-gray-900">{{ $cars->total() }} {{ Str::plural('car', $cars->total()) }} found</h1>
            <div class="flex items-center gap-2">
                <span class="text-[13px] text-gray-500 hidden sm:inline">Sort by</span>
                <select wire:model.live="sort" class="bg-white border border-gray-300 rounded text-[14px] py-2 px-3 focus:ring-primary focus:border-primary">
                    <option value="latest">Newest First</option>
                    <option value="price_asc">Price: Low to High</option>
                    <option value="price_desc">Price: High to Low</option>
                    <option value="year_desc">Year: Newest</option>
                </select>
            </div>
        </div>

        @if ($cars->isEmpty())
            <div class="text-center py-28 bg-gray-50 rounded-2xl border border-gray-100">
                <div class="w-16 h-16 rounded-full bg-white flex items-center justify-center mx-auto mb-4 shadow-sm border border-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-300" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.85 7h10.29l1.08 3.11H5.77L6.85 7zM19 17H5v-5h14v5z"/>
                    </svg>
                </div>
                <p class="text-[16px] font-bold text-gray-700">No cars match your search</p>
                <p class="text-[14px] text-gray-500 mt-1">
                    Try adjusting your filters or
                    <button wire:click="clearFilters" class="text-primary font-bold hover:underline">clear all</button>
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach ($cars as $car)
                    @include('partials.car-card', ['car' => $car])
                @endforeach
            </div>

            <div class="mt-12">
                {{ $cars->links() }}
            </div>
        @endif
    </div>
</div>
