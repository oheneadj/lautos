<x-layouts.public title="Welcome">

    {{-- Hero --}}
    <section class="relative bg-gradient-to-br from-primary/10 to-base-100 py-20 lg:py-28 px-4 lg:px-8">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <span class="badge badge-primary badge-soft mb-4">Quality Imports from Japan & Korea</span>
                <h1 class="text-4xl lg:text-5xl font-bold leading-tight mb-6">
                    Find Your Perfect<br>
                    <span class="text-primary">Imported Car</span><br>
                    in Ghana
                </h1>
                <p class="text-base-content/70 text-lg mb-8 leading-relaxed">
                    We source, ship, and deliver quality Japanese and Korean vehicles directly to you.
                    Transparent pricing, reliable service, and a wide selection.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('cars.index') }}" class="btn btn-primary btn-lg">Browse Our Cars</a>
                    <a href="{{ route('contact') }}" class="btn btn-outline btn-lg">Make an Enquiry</a>
                </div>
                <div class="flex gap-8 mt-10">
                    <div>
                        <div class="text-2xl font-bold text-primary">500+</div>
                        <div class="text-sm text-base-content/60">Cars Sold</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-primary">100%</div>
                        <div class="text-sm text-base-content/60">Verified Stock</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-primary">5★</div>
                        <div class="text-sm text-base-content/60">Customer Rating</div>
                    </div>
                </div>
            </div>
            <div class="hidden lg:flex justify-center">
                <div class="w-80 h-64 bg-primary/10 rounded-3xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-40 w-40 text-primary/30" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.85 7h10.29l1.08 3.11H5.77L6.85 7zM19 17H5v-5h14v5z"/>
                        <circle cx="7.5" cy="14.5" r="1.5"/>
                        <circle cx="16.5" cy="14.5" r="1.5"/>
                    </svg>
                </div>
            </div>
        </div>
    </section>

    {{-- Latest Cars --}}
    <section class="py-16 px-4 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-bold">Latest Arrivals</h2>
                    <p class="text-base-content/60 mt-1">Fresh stock, just landed</p>
                </div>
                <a href="{{ route('cars.index') }}" class="btn btn-outline btn-sm">View All</a>
            </div>
            <livewire:cars.latest-cars />
        </div>
    </section>

    {{-- Why Us --}}
    <section class="py-16 px-4 lg:px-8 bg-base-200">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-2xl font-bold">Why Choose Livingston Autos?</h2>
                <p class="text-base-content/60 mt-2">We make importing simple, safe, and stress-free</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body items-center text-center">
                        <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="card-title text-base">Verified Quality</h3>
                        <p class="text-sm text-base-content/60">Every vehicle is inspected and auction-graded before shipping. No hidden surprises.</p>
                    </div>
                </div>
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body items-center text-center">
                        <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="card-title text-base">Transparent Pricing</h3>
                        <p class="text-sm text-base-content/60">Car price + shipping cost displayed upfront. No hidden fees, no surprises at the port.</p>
                    </div>
                </div>
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body items-center text-center">
                        <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            </svg>
                        </div>
                        <h3 class="card-title text-base">Delivered to Ghana</h3>
                        <p class="text-sm text-base-content/60">We handle all shipping logistics. Track your car from Japan to your doorstep.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-16 px-4 lg:px-8">
        <div class="max-w-3xl mx-auto text-center">
            <h2 class="text-2xl font-bold mb-4">Don't see what you're looking for?</h2>
            <p class="text-base-content/60 mb-6">Tell us what you want and we'll source it from auction for you.</p>
            <a href="{{ route('contact') }}" class="btn btn-primary btn-lg">Make a Custom Request</a>
        </div>
    </section>

</x-layouts.public>
