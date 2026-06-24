<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

    {{-- Sidebar Filters — a normal static sidebar on desktop, a slide-in
         drawer with a backdrop on mobile, controlled by $showMobileFilters --}}
    @php
        $hasActiveFilters = $search || $makeFilter || $modelFilter || $bodyTypeFilter || $transmissionFilter || $fuelFilter || $countryFilter || $minYear || $maxYear || $maxPriceGhs || $maxMileage;
    @endphp

    {{-- Most sections default to open, matching the reference design — only
         the ones with the fewest/least-used options would default closed,
         and we don't have any of those here. --}}
    <aside
        class="{{ $showMobileFilters ? 'fixed inset-0 z-50 flex' : 'hidden' }} lg:flex lg:static lg:inset-auto lg:z-auto lg:col-span-1"
        x-data="{ open: { make: true, model: true, bodyType: true, price: true, mileage: true, year: true, fuel: true, transmission: true, country: true } }"
    >
        @if ($showMobileFilters)
            <div class="fixed inset-0 bg-black/40 lg:hidden" wire:click="$set('showMobileFilters', false)"></div>
        @endif

        <div class="relative z-10 ml-auto w-full max-w-xs h-full overflow-y-auto bg-white border border-gray-200 rounded-lg p-5 lg:ml-0 lg:w-auto lg:max-w-none lg:h-auto lg:overflow-visible lg:sticky lg:top-24">
            <div class="flex items-center justify-between mb-1">
                <h2 class="text-[15px] font-bold text-gray-900">{{ $cars->total() }} {{ Str::plural('result', $cars->total()) }}</h2>
                <div class="flex items-center gap-3">
                    @if ($hasActiveFilters)
                        <button wire:click="clearFilters" class="text-[12px] font-medium text-gray-600 hover:text-gray-900 transition-colors">Clear all</button>
                    @endif
                    <button wire:click="$set('showMobileFilters', false)" class="lg:hidden text-gray-400 hover:text-gray-700" aria-label="Close filters">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>

            {{-- Active Filter Chips --}}
            @if ($hasActiveFilters)
                <div class="flex flex-wrap gap-2 mb-4 pb-4 border-b border-gray-100">
                    @foreach ($makeFilter as $slug)
                        @php $makeLabel = $makes->firstWhere('slug', $slug)?->name ?? $slug; @endphp
                        <button wire:click="removeMake('{{ $slug }}')" class="inline-flex items-center gap-1 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-800 text-[12px] font-semibold px-3 py-1.5 transition-colors">
                            {{ $makeLabel }}
                            <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    @endforeach
                    @foreach ($modelFilter as $modelName)
                        <button wire:click="removeModel('{{ $modelName }}')" class="inline-flex items-center gap-1 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-800 text-[12px] font-semibold px-3 py-1.5 transition-colors">
                            {{ $modelName }}
                            <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    @endforeach
                    @foreach ($bodyTypeFilter as $bodyTypeValue)
                        <button wire:click="removeBodyType('{{ $bodyTypeValue }}')" class="inline-flex items-center gap-1 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-800 text-[12px] font-semibold px-3 py-1.5 transition-colors">
                            {{ \App\Enums\CarBodyType::from($bodyTypeValue)->label() }}
                            <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    @endforeach
                    @if ($transmissionFilter)
                        <button wire:click="$set('transmissionFilter', '')" class="inline-flex items-center gap-1 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-800 text-[12px] font-semibold px-3 py-1.5 transition-colors">
                            {{ $transmissionFilter }}
                            <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    @endif
                    @if ($fuelFilter)
                        <button wire:click="$set('fuelFilter', '')" class="inline-flex items-center gap-1 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-800 text-[12px] font-semibold px-3 py-1.5 transition-colors">
                            {{ $fuelFilter }}
                            <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    @endif
                    @if ($countryFilter)
                        <button wire:click="$set('countryFilter', '')" class="inline-flex items-center gap-1 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-800 text-[12px] font-semibold px-3 py-1.5 transition-colors">
                            {{ $countryFilter }}
                            <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    @endif
                    @if ($minYear || $maxYear)
                        <button wire:click="$set('minYear', '');$set('maxYear', '')" class="inline-flex items-center gap-1 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-800 text-[12px] font-semibold px-3 py-1.5 transition-colors">
                            {{ $minYear ?: 'Any' }}&ndash;{{ $maxYear ?: 'Any' }}
                            <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    @endif
                    @if ($maxPriceGhs)
                        <button wire:click="$set('maxPriceGhs', '')" class="inline-flex items-center gap-1 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-800 text-[12px] font-semibold px-3 py-1.5 transition-colors">
                            Under GH₵{{ number_format($maxPriceGhs) }}
                            <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    @endif
                    @if ($maxMileage)
                        <button wire:click="$set('maxMileage', '')" class="inline-flex items-center gap-1 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-800 text-[12px] font-semibold px-3 py-1.5 transition-colors">
                            Under {{ number_format($maxMileage) }} km
                            <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    @endif
                </div>
            @endif

            {{-- Search --}}
            <div class="mb-5">
                <label class="block text-[12px] font-bold text-gray-900 mb-2">Search</label>
                <input
                    type="text"
                    wire:model.live.debounce.400ms="search"
                    placeholder="Brand, model, colour..."
                    class="w-full bg-gray-100 border-none rounded-lg p-3 text-[14px] text-gray-800 focus:ring-2 focus:ring-primary outline-none font-medium"
                >
            </div>

            {{-- Make --}}
            <div class="border-t border-gray-100 py-4">
                <button @click="open.make = !open.make" class="w-full flex items-center justify-between text-[13px] font-bold text-gray-900">
                    Make
                    <svg class="w-3.5 h-3.5 text-gray-500 transition-transform" :class="open.make && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open.make" class="mt-3 space-y-1.5 max-h-56 overflow-y-auto">
                    @foreach ($makes as $make)
                        <label class="flex items-center justify-between gap-2 text-[13px] text-gray-700 cursor-pointer">
                            <span class="flex items-center gap-2">
                                <input type="checkbox" wire:model.live="makeFilter" value="{{ $make->slug }}" class="rounded border-gray-300 text-primary focus:ring-primary">
                                {{ $make->name }}
                            </span>
                            <span class="text-gray-400 text-[12px]">({{ $makeCounts[$make->id] ?? 0 }})</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Model — only shown once at least one make is selected --}}
            @if (! empty($makeFilter))
                <div class="border-t border-gray-100 py-4">
                    <button @click="open.model = !open.model" class="w-full flex items-center justify-between text-[13px] font-bold text-gray-900">
                        Model
                        <svg class="w-3.5 h-3.5 text-gray-500 transition-transform" :class="open.model && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open.model" class="mt-3 space-y-1.5 max-h-56 overflow-y-auto">
                        @forelse ($models as $model)
                            <label class="flex items-center justify-between gap-2 text-[13px] text-gray-700 cursor-pointer">
                                <span class="flex items-center gap-2">
                                    <input type="checkbox" wire:model.live="modelFilter" value="{{ $model->name }}" class="rounded border-gray-300 text-primary focus:ring-primary">
                                    {{ $model->name }}
                                </span>
                                <span class="text-gray-400 text-[12px]">({{ $modelCounts[$model->id] ?? 0 }})</span>
                            </label>
                        @empty
                            <p class="text-[12px] text-gray-400">No models found for the selected make(s).</p>
                        @endforelse
                    </div>
                </div>
            @endif

            {{-- Body Type --}}
            <div class="border-t border-gray-100 py-4">
                <button @click="open.bodyType = !open.bodyType" class="w-full flex items-center justify-between text-[13px] font-bold text-gray-900">
                    Body Type
                    <svg class="w-3.5 h-3.5 text-gray-500 transition-transform" :class="open.bodyType && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open.bodyType" class="mt-3 space-y-1.5">
                    @foreach (\App\Enums\CarBodyType::cases() as $bodyType)
                        <label class="flex items-center justify-between gap-2 text-[13px] text-gray-700 cursor-pointer">
                            <span class="flex items-center gap-2">
                                <input type="checkbox" wire:model.live="bodyTypeFilter" value="{{ $bodyType->value }}" class="rounded border-gray-300 text-primary focus:ring-primary">
                                {{ $bodyType->label() }}
                            </span>
                            <span class="text-gray-400 text-[12px]">({{ $bodyTypeCounts[$bodyType->value] ?? 0 }})</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Price (GHS) --}}
            <div class="border-t border-gray-100 py-4">
                <button @click="open.price = !open.price" class="w-full flex items-center justify-between text-[13px] font-bold text-gray-900">
                    Car Price (GH₵)
                    <svg class="w-3.5 h-3.5 text-gray-500 transition-transform" :class="open.price && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open.price" class="mt-3">
                    <input type="number" wire:model.live.debounce.500ms="maxPriceGhs" placeholder="Maximum price" class="w-full bg-gray-100 border-none rounded-lg py-3 px-3 text-center text-[14px] text-gray-800 focus:ring-2 focus:ring-primary outline-none font-medium mb-3">
                    <div class="flex flex-wrap gap-2">
                        <button wire:click="$set('maxPriceGhs', 100000)" class="rounded-full px-3.5 py-1.5 text-[11px] font-semibold bg-gray-900 text-white hover:bg-black transition-colors">Under 100K</button>
                        <button wire:click="$set('maxPriceGhs', 300000)" class="rounded-full px-3.5 py-1.5 text-[11px] font-semibold bg-gray-900 text-white hover:bg-black transition-colors">Under 300K</button>
                        <button wire:click="$set('maxPriceGhs', 600000)" class="rounded-full px-3.5 py-1.5 text-[11px] font-semibold bg-gray-900 text-white hover:bg-black transition-colors">Under 600K</button>
                    </div>
                </div>
            </div>

            {{-- Mileage — a single max-cap slider, matching the reference's
                 "250,000+ miles" style control rather than separate min/max boxes. --}}
            <div class="border-t border-gray-100 py-4" x-data="{ mileage: {{ $maxMileage !== '' ? (int) $maxMileage : \App\Livewire\Cars\CarCatalogue::MAX_MILEAGE_CAP }} }">
                <button @click="open.mileage = !open.mileage" class="w-full flex items-center justify-between text-[13px] font-bold text-gray-900">
                    Mileage
                    <svg class="w-3.5 h-3.5 text-gray-500 transition-transform" :class="open.mileage && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open.mileage" class="mt-3">
                    <p class="text-[13px] font-semibold text-gray-700 mb-3" x-text="mileage >= {{ \App\Livewire\Cars\CarCatalogue::MAX_MILEAGE_CAP }} ? 'Any mileage' : Number(mileage).toLocaleString() + ' km or less'"></p>
                    <input
                        type="range"
                        min="0"
                        max="{{ \App\Livewire\Cars\CarCatalogue::MAX_MILEAGE_CAP }}"
                        step="5000"
                        x-model.number="mileage"
                        @change="$wire.set('maxMileage', mileage >= {{ \App\Livewire\Cars\CarCatalogue::MAX_MILEAGE_CAP }} ? '' : mileage)"
                        class="w-full accent-primary cursor-pointer"
                    >
                    <div class="flex items-center justify-between text-[11px] text-gray-400 mt-1.5">
                        <span>0 km</span>
                        <span>{{ number_format(\App\Livewire\Cars\CarCatalogue::MAX_MILEAGE_CAP) }}+ km</span>
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
                    <select wire:model.live="minYear" class="w-full bg-gray-100 border-none rounded-lg py-3 pl-3 pr-8 text-[14px] text-gray-800 text-center focus:ring-2 focus:ring-primary outline-none font-medium">
                        <option value="">From</option>
                        @foreach ($yearOptions as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                    <span class="text-gray-400 font-bold">—</span>
                    <select wire:model.live="maxYear" class="w-full bg-gray-100 border-none rounded-lg py-3 pl-3 pr-8 text-[14px] text-gray-800 text-center focus:ring-2 focus:ring-primary outline-none font-medium">
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
                    <select wire:model.live="countryFilter" class="w-full bg-gray-100 border-none rounded-lg py-3 pl-3 pr-10 text-[14px] text-gray-800 focus:ring-2 focus:ring-primary outline-none font-medium">
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
                <button wire:click="$set('showMobileFilters', true)" class="lg:hidden flex items-center gap-1.5 bg-white border border-gray-300 rounded text-[13px] font-medium text-gray-700 py-2 px-3">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m9 12h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0H7.5" /></svg>
                    Filters
                    @if ($hasActiveFilters)
                        <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-primary text-white text-[10px] font-bold">&bull;</span>
                    @endif
                </button>
                <span class="text-[13px] text-gray-500 hidden sm:inline">Sort by</span>
                <select wire:model.live="sort" class="bg-gray-100 border-none rounded-lg py-2.5 pl-3 pr-10 text-[13px] font-medium text-gray-800 focus:ring-2 focus:ring-primary outline-none">
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
