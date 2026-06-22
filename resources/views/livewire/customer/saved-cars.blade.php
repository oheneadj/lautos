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

    @if ($savedCars->isEmpty())
        <div class="bg-base-100 border border-base-content/5 rounded-xl p-14 text-center shadow-sm">
            <svg class="mx-auto w-12 h-12 text-base-content/20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z" /></svg>
            <p class="mt-3 text-[15px] font-bold text-base-content">{{ __('No saved cars') }}</p>
            <p class="mt-1 text-[13px] text-base-content/40">{{ __('You haven\'t bookmarked any cars yet. Start browsing to save your favorites!') }}</p>
        </div>
    @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($savedCars as $car)
                <div class="bg-white border border-base-content/5 rounded-xl shadow-sm overflow-hidden flex flex-col group relative">
                    <button wire:click="removeSavedCar('{{ $car->uuid }}')" wire:confirm="Remove this car from your saved list?" class="absolute top-3 right-3 z-10 w-8 h-8 rounded-full bg-white/90 shadow-sm text-base-content hover:text-error hover:bg-white flex items-center justify-center transition-all opacity-0 group-hover:opacity-100 focus:opacity-100">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                    </button>
                    
                    <a href="{{ route('cars.show', $car->slug) }}" class="block aspect-[4/3] bg-base-200 overflow-hidden relative">
                        @if($car->images->first())
                            <img src="{{ Storage::url($car->images->first()->path) }}" alt="{{ $car->year }} {{ $car->make->name }} {{ $car->carModel->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-base-content/20 bg-base-200">
                                <svg class="w-10 h-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>
                            </div>
                        @endif
                        <div class="absolute bottom-3 left-3 flex gap-2">
                            <x-ui.badge :type="$car->status->colour()">{{ $car->status->label() }}</x-ui.badge>
                        </div>
                    </a>
                    
                    <div class="p-4 flex flex-col flex-1">
                        <div class="mb-3">
                            <h3 class="text-base font-bold text-base-content leading-tight line-clamp-1 group-hover:text-primary transition-colors">
                                <a href="{{ route('cars.show', $car->slug) }}">{{ $car->year }} {{ $car->make->name }} {{ $car->carModel->name }}</a>
                            </h3>
                            <p class="text-[12px] text-base-content/60 mt-1">{{ $car->mileage ? number_format($car->mileage) . ' km' : 'Mileage N/A' }} • {{ $car->transmission }}</p>
                        </div>
                        
                        <div class="mt-auto pt-4 border-t border-base-content/5 flex items-end justify-between">
                            <div>
                                <span class="block text-[10px] font-bold uppercase tracking-widest text-base-content/40 mb-0.5">{{ __('Total Price') }}</span>
                                <span class="text-lg font-bold text-base-content">${{ number_format($car->total_usd_cents / 100, 0) }}</span>
                            </div>
                            <a href="{{ route('cars.show', $car->slug) }}" class="text-[12px] font-bold text-primary hover:underline">{{ __('View Details') }}</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
