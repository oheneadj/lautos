<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-[28px] font-semibold text-base-content leading-tight">{{ __('Saved Cars') }}</h1>
            <p class="text-[14px] text-base-content/50 mt-1">{{ __('Cars you have bookmarked from the catalogue') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('cars.index') }}" wire:navigate class="inline-flex items-center gap-2 rounded-xl bg-primary px-[18px] py-[10px] text-[13px] font-medium text-white hover:brightness-110 transition-all duration-150 shadow-sm">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                {{ __('Browse Cars') }}
            </a>
        </div>
    </div>

    {{-- Filter Bar --}}
    @if (! $savedCars->isEmpty() || $search)
        <div class="bg-base-100 border border-base-content/5 rounded-xl p-4">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                {{-- Search --}}
                <div class="w-full sm:max-w-xs relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-base-content/40" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search saved cars...') }}" class="block w-full pl-10 pr-3 py-2 border-none rounded-lg bg-base-200/50 text-[13px] text-base-content focus:ring-2 focus:ring-primary focus:bg-base-100 transition-colors">
                </div>

                {{-- Sort --}}
                <div class="flex items-center gap-2 flex-shrink-0 w-full sm:w-auto">
                    <span class="text-[13px] font-medium text-base-content/50">{{ __('Sort by:') }}</span>
                    <select wire:model.live="sort" class="block w-full sm:w-auto py-2 pl-3 pr-10 border-none rounded-lg bg-base-200/50 text-[13px] font-bold text-base-content focus:ring-2 focus:ring-primary focus:bg-base-100 transition-colors cursor-pointer">
                        <option value="latest">{{ __('Recently Saved') }}</option>
                        <option value="price_asc">{{ __('Price: Low to High') }}</option>
                        <option value="price_desc">{{ __('Price: High to Low') }}</option>
                        <option value="year_desc">{{ __('Year: Newest') }}</option>
                    </select>
                </div>
            </div>
        </div>
    @endif

    @if ($savedCars->isEmpty())
        <div class="bg-base-100 border border-base-content/5 rounded-xl p-14 text-center">
            <div class="w-16 h-16 rounded-full bg-base-200 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-base-content/20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" /></svg>
            </div>
            @if ($search)
                <p class="text-[16px] font-bold text-base-content">{{ __('No cars match your search') }}</p>
                <p class="mt-1 text-[13px] text-base-content/40">{{ __('Try adjusting your search query.') }}</p>
                <button wire:click="$set('search', '')" class="mt-4 text-[12px] font-bold text-primary hover:underline cursor-pointer">
                    {{ __('Clear search') }}
                </button>
            @else
                <p class="text-[16px] font-bold text-base-content">{{ __('No saved cars') }}</p>
                <p class="mt-1 text-[13px] text-base-content/40">{{ __('You haven\'t bookmarked any cars yet. Start browsing to save your favorites!') }}</p>
            @endif
        </div>
    @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($savedCars as $car)
                @include('partials.car-card', ['car' => $car])
            @endforeach
        </div>

        @if ($savedCars->hasPages())
            <div class="mt-6">
                {{ $savedCars->links() }}
            </div>
        @endif
    @endif
</div>
