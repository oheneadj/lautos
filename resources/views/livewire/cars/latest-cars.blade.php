<div>
    @if ($cars->isEmpty())
        <div class="text-center py-12 text-base-content/50">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3 opacity-30" viewBox="0 0 24 24" fill="currentColor">
                <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99z"/>
            </svg>
            <p>No cars available yet. Check back soon!</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($cars as $car)
                <a href="{{ route('cars.show', $car->uuid) }}" class="card bg-base-100 shadow-sm hover:shadow-md transition-shadow border border-base-200">
                    <figure class="h-48 bg-base-200 overflow-hidden">
                        @if ($car->images->first())
                            <img src="{{ Storage::url($car->images->first()->path) }}" alt="{{ $car->make->name }} {{ $car->carModel->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-base-content/20">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.85 7h10.29l1.08 3.11H5.77L6.85 7zM19 17H5v-5h14v5z"/>
                                    <circle cx="7.5" cy="14.5" r="1.5"/>
                                    <circle cx="16.5" cy="14.5" r="1.5"/>
                                </svg>
                            </div>
                        @endif
                    </figure>
                    <div class="card-body p-4">
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-semibold text-base leading-tight">
                                {{ $car->year }} {{ $car->make->name }} {{ $car->carModel->name }}
                                @if ($car->carTrim)
                                    <span class="text-base-content/50 font-normal">{{ $car->carTrim->name }}</span>
                                @endif
                            </h3>
                        </div>
                        <div class="flex flex-wrap gap-1 mt-1">
                            <span class="badge badge-ghost badge-sm">{{ $car->transmission }}</span>
                            <span class="badge badge-ghost badge-sm">{{ $car->fuel_type }}</span>
                            <span class="badge badge-ghost badge-sm">{{ number_format($car->mileage) }} km</span>
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <div>
                                <div class="text-lg font-bold text-primary">${{ number_format($car->price_usd_cents / 100, 0) }}</div>
                                <div class="text-xs text-base-content/50">+ ${{ number_format($car->shipping_cost_usd_cents / 100, 0) }} shipping</div>
                            </div>
                            <span class="text-sm text-primary font-medium">View →</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
