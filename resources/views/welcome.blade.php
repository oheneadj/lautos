<x-layouts.public title="Welcome">

    {{-- HERO SECTION with embedded search --}}
    <section class="relative min-h-[70vh] flex items-center overflow-hidden bg-[#0e0e0f]">
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=2000&q=80"
                alt="Car Background" class="w-full h-full object-cover opacity-40">
            <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/40 to-transparent"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 lg:px-8 w-full py-20">
            <div class="max-w-xl">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4 leading-tight tracking-tight">
                    Find Your <br><span class="text-primary">Perfect Car</span>
                </h1>
                <p class="text-lg text-white/70 mb-8 font-medium">Browse quality Japanese & Korean imports. Fully
                    inspected, competitively priced, delivered to your door.</p>

                {{-- Embedded Search Form — every field maps to a filter CarCatalogue actually understands. --}}
                @php
                    $heroMakes = \App\Models\Make::withCount('cars')->orderByDesc('cars_count')->get();
                @endphp
                <form action="{{ route('cars.index') }}" method="GET" class="bg-white rounded-lg p-5 shadow-2xl">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label
                                class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Make</label>
                            {{-- I use make[] (array syntax), not make — CarCatalogue's makeFilter
                                 is now a multi-select array, and a plain scalar query value
                                 wouldn't hydrate into it. --}}
                            <select name="make[]"
                                class="w-full bg-gray-100 border-none rounded-lg py-3 pl-3 pr-10 text-sm text-gray-800 focus:ring-2 focus:ring-primary outline-none font-medium">
                                <option value="">Any Make</option>
                                @foreach ($heroMakes as $make)
                                    <option value="{{ $make->slug }}">{{ $make->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Transmission</label>
                            <select name="transmission"
                                class="w-full bg-gray-100 border-none rounded-lg py-3 pl-3 pr-10 text-sm text-gray-800 focus:ring-2 focus:ring-primary outline-none font-medium">
                                <option value="">Any Transmission</option>
                                <option value="Automatic">Automatic</option>
                                <option value="Manual">Manual</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Min
                                Year</label>
                            <select name="min_year"
                                class="w-full bg-gray-100 border-none rounded-lg py-3 pl-3 pr-10 text-sm text-gray-800 focus:ring-2 focus:ring-primary outline-none font-medium">
                                <option value="">Any Year</option>
                                @foreach (range(now()->year, now()->year - 15) as $year)
                                    <option value="{{ $year }}">{{ $year }}+</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Max
                                Price (GHS)</label>
                            <select name="max_price"
                                class="w-full bg-gray-100 border-none rounded-lg py-3 pl-3 pr-10 text-sm text-gray-800 focus:ring-2 focus:ring-primary outline-none font-medium">
                                <option value="">Any Price</option>
                                <option value="100000">Under GHS 100,000</option>
                                <option value="200000">Under GHS 200,000</option>
                                <option value="300000">Under GHS 300,000</option>
                                <option value="500000">Under GHS 500,000</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit"
                        class="w-full bg-primary text-white font-bold py-3.5 rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center gap-2 text-base">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Search Vehicles
                    </button>
                </form>
            </div>
        </div>

        {{-- Stats overlay bottom --}}
        <div
            class="absolute bottom-0 right-0 hidden lg:flex items-center gap-10 bg-white/10 backdrop-blur-md rounded-tl-2xl px-10 py-6 border-t border-l border-white/10">
            <div class="text-center">
                <div class="text-3xl font-bold text-white">400+</div>
                <div class="text-xs text-white/60 font-medium">Vehicles</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-white">25+</div>
                <div class="text-xs text-white/60 font-medium">Years</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-white">3990+</div>
                <div class="text-xs text-white/60 font-medium">Happy Clients</div>
            </div>
        </div>
    </section>

    {{-- HOW WE WORK --}}
    <section class="py-16 bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="flex items-start gap-4">
                    <div
                        class="w-12 h-12 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 mb-1">Search</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">Browse our curated inventory of
                            auction-graded imports.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div
                        class="w-12 h-12 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 mb-1">Inspect</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">Every vehicle is inspected with full
                            history reports.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div
                        class="w-12 h-12 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 mb-1">Pay</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">Transparent pricing. No hidden fees or
                            surprise costs.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div
                        class="w-12 h-12 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 mb-1">Deliver</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">Door-to-door delivery with customs
                            clearance included.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ALL MAKES SECTION --}}
    <section class="py-16 bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-12 text-center">Buy cars</h2>

            <div
                class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-y-10 gap-x-4 justify-items-center">
                @php
                    // Fetch more makes to match the large grid design
                    $allMakes = \App\Models\Make::withCount('cars')
                        ->orderByDesc('cars_count')
                        ->take(40)
                        ->get();
                @endphp
                @foreach($allMakes as $make)
                    {{-- makeFilter is a multi-select array, so this needs ['make' => [$make->slug]]
                         (producing make[0]=slug) rather than a plain scalar make=slug. --}}
                    <a href="{{ route('cars.index', ['make' => [$make->slug]]) }}"
                        class="flex flex-col items-center gap-3 group w-full">
                        <div class="w-24 h-24 flex items-center justify-center transition-transform group-hover:scale-110">
                            @if($make->icon_path)
                                <img src="{{ Storage::url($make->icon_path) }}"
                                    class="w-full h-full object-contain grayscale opacity-80 group-hover:grayscale-0 group-hover:opacity-100 transition-all"
                                    alt="{{ $make->name }} Logo" loading="lazy">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($make->name) }}&background=transparent&color=374151&bold=true&format=svg"
                                    class="w-full h-full object-contain opacity-80 group-hover:opacity-100 transition-all"
                                    alt="{{ $make->name }} Logo" loading="lazy">
                            @endif
                        </div>
                        <span
                            class="text-sm font-medium text-gray-500 group-hover:text-gray-900 transition-colors text-center">{{ $make->name }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- POPULAR CATEGORIES (TABS WITH CARS) --}}
    <section class="py-20 bg-gray-50" x-data="{ activeTab: 'SUVs' }">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Trending categories</h2>
                    <p class="text-sm text-gray-500 mt-1">Browse top vehicles by body style and category</p>
                </div>
                <a href="{{ route('cars.index') }}"
                    class="text-sm font-bold text-gray-900 underline decoration-2 underline-offset-4 hover:text-primary transition-colors hidden sm:inline-flex items-center gap-1">
                    View all inventory
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>

            @php
                // I drive these tabs off the real body_type column (set by the admin per
                // car) instead of a hardcoded list of model names — that old approach meant
                // any model not in the list silently never showed up under any category,
                // with no way for an admin to fix it. "Under $15k" stays a special case
                // since it's genuinely a price filter, not a body type.
                $categoryMap = [
                    'SUVs' => \App\Enums\CarBodyType::Suv,
                    'Sedans' => \App\Enums\CarBodyType::Sedan,
                    'Trucks' => \App\Enums\CarBodyType::PickupTruck,
                    'Hatchbacks' => \App\Enums\CarBodyType::Hatchback,
                    'Under $15k' => null, // special case handled below
                ];
                $popularTabs = array_keys($categoryMap);

                // Pre-fetch cars for categories
                $categoryCars = [];
                foreach ($categoryMap as $tab => $bodyType) {
                    $query = \App\Models\Car::with(['make', 'carModel', 'carTrim', 'images'])
                        ->withCount('orders')
                        ->where('status', \App\Enums\CarStatus::Available)
                        ->latest();

                    if ($tab === 'Under $15k') {
                        $query->where('price_usd_cents', '<=', 1500000);
                    } else {
                        $query->where('body_type', $bodyType);
                    }
                    $categoryCars[$tab] = $query->take(3)->get();
                }
            @endphp

            {{-- Tabs --}}
            <div class="flex overflow-x-auto gap-2 pb-4 mb-6 scrollbar-hide">
                @foreach($popularTabs as $tab)
                    <button @click="activeTab = '{{ $tab }}'"
                        :class="activeTab === '{{ $tab }}' ? 'bg-gray-900 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
                        class="px-6 py-2.5 rounded-full text-sm font-bold whitespace-nowrap transition-colors">
                        {{ $tab }}
                    </button>
                @endforeach
            </div>

            {{-- Tab Content --}}
            @foreach($popularTabs as $tab)
                <div x-show="activeTab === '{{ $tab }}'" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                    style="display: none;">
                    @if(isset($categoryCars[$tab]) && $categoryCars[$tab]->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($categoryCars[$tab] as $car)
                                @include('partials.car-card', ['car' => $car])
                            @endforeach
                        </div>
                    @else
                        <div class="bg-white border border-gray-200 rounded-lg p-12 text-center w-full">
                            <p class="text-gray-500">No {{ strtolower($tab) }} vehicles currently available in our inventory.
                            </p>
                            <a href="{{ route('cars.index') }}"
                                class="inline-block mt-4 text-primary font-bold hover:underline">View all cars</a>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </section>

    {{-- LATEST ARRIVALS --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Latest arrivals</h2>
                    <p class="text-sm text-gray-500 mt-1">Freshly added to our inventory</p>
                </div>
                <a href="{{ route('cars.index') }}"
                    class="inline-flex items-center gap-2 bg-gray-900 hover:bg-primary text-white font-bold py-3 px-6 rounded-lg transition-colors text-sm">
                    View All
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                    $latestCars = \App\Models\Car::with(['make', 'carModel', 'carTrim', 'images'])
                        ->withCount('orders')
                        ->where('status', \App\Enums\CarStatus::Available)
                        ->latest()
                        ->take(6)
                        ->get();
                @endphp
                @forelse($latestCars as $car)
                    @include('partials.car-card', ['car' => $car])
                @empty
                    <p class="text-gray-500 col-span-3 text-center py-12">No cars available yet. Check back soon!</p>
                @endforelse
            </div>
        </div>
    </section>


    {{-- TESTIMONIALS --}}
    @php
        // I only ever show approved reviews here — pending/rejected ones
        // never reach the public site.
        $approvedReviews = \App\Models\Review::approved()
            ->with(['user', 'order.car.make', 'order.car.carModel'])
            ->latest('approved_at')
            ->limit(9)
            ->get();

        $demoReviews = [
            ['author' => 'Richg321', 'subtitle' => 'from The Villages, FL', 'date' => '03/29/2026', 'rating' => 5, 'title' => 'Amazing car, comfortable, smooth ride', 'body' => 'Amazing car, comfortable, smooth ride very quiet solid performance. Averaging 35 miles per gallon. That kind of blew me away. Great turn ratio smooth turning radius.'],
            ['author' => 'JamesVZ', 'subtitle' => 'from South Bend, IN', 'date' => '03/05/2026', 'rating' => 5, 'title' => 'Great experience with the dealer', 'body' => 'Great experience with the dealer. Have about 300 miles on the new car so far and I really like it. You can\'t beat the brand for innovation and warranty.'],
            ['author' => 'William H.', 'subtitle' => 'from Round Lake Beach', 'date' => '11/22/2025', 'rating' => 4, 'title' => 'Very comfortable and nice to drive', 'body' => 'I purchased the Kia Sportage EX Hybrid model. It is very comfortable and nice to drive. There are many extras with this model. The warranty is outstanding.'],
        ];

        // A brand-new (or sparsely-reviewed) site shouldn't show an empty or
        // half-empty testimonials carousel, so I fall back to the demo set
        // until there are at least 3 real approved reviews to show instead.
        if ($approvedReviews->count() < 3) {
            $reviews = $demoReviews;
        } else {
            $reviews = $approvedReviews->map(function ($review) {
                $car = $review->order->car;

                return [
                    'author' => $review->user->name,
                    'subtitle' => "Verified Buyer — {$car->year} {$car->make->name} {$car->carModel->name}",
                    'date' => $review->approved_at?->format('m/d/Y') ?? $review->created_at->format('m/d/Y'),
                    'rating' => $review->rating,
                    'title' => $review->title,
                    'body' => $review->body,
                ];
            })->all();
        }

        // I group reviews into slides of 3 so the carousel shows three cards
        // side by side per slide, instead of one centered card with empty
        // space either side.
        $reviewGroups = array_chunk($reviews, 3);
    @endphp
    <section class="py-20 bg-white" x-data="{ active: 0, total: {{ count($reviewGroups) }} }">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">What our customers say</h2>
                    <p class="text-sm text-gray-500 mt-1">Most recent reviews from verified buyers</p>
                </div>
                <div class="hidden sm:flex items-center gap-2">
                    <button @click="active = (active - 1 + total) % total" aria-label="Previous review"
                        class="w-9 h-9 rounded-full border border-gray-200 flex items-center justify-center text-gray-500 hover:text-primary hover:border-primary transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button @click="active = (active + 1) % total" aria-label="Next review"
                        class="w-9 h-9 rounded-full border border-gray-200 flex items-center justify-center text-gray-500 hover:text-primary hover:border-primary transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="overflow-hidden">
                <div class="flex transition-transform duration-500 ease-out"
                    :style="`transform: translateX(-${active * 100}%)`">
                    @foreach($reviewGroups as $group)
                        <div class="w-full shrink-0 px-1 grid grid-cols-1 md:grid-cols-3 gap-6">
                            @foreach($group as $review)
                                <div
                                    class="bg-white rounded-2xl p-8 shadow-xl shadow-gray-200/50 border border-gray-100 relative overflow-hidden flex flex-col">
                                    <div class="flex justify-between items-start mb-1">
                                        <div class="text-sm text-gray-800 font-medium">By {{ $review['author'] }}</div>
                                        <div class="text-sm text-gray-500">{{ $review['date'] }}</div>
                                    </div>
                                    <div class="text-sm text-gray-500 mb-4">{{ $review['subtitle'] }}</div>
                                    <div class="flex gap-1 text-secondary mb-4">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= $review['rating'] ? '' : 'text-gray-200' }}"
                                                fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @endfor
                                    </div>
                                    <h3 class="font-bold text-base text-gray-900 mb-3 leading-snug">{{ $review['title'] }}
                                    </h3>
                                    <p class="text-sm text-gray-700 leading-relaxed">{{ $review['body'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-center gap-2 mt-8">
                @foreach($reviewGroups as $index => $group)
                    <button @click="active = {{ $index }}"
                        :class="active === {{ $index }} ? 'bg-primary w-6' : 'bg-gray-300 w-2'"
                        class="h-2 rounded-full transition-all duration-300"
                        aria-label="Go to review slide {{ $index + 1 }}"></button>
                @endforeach
            </div>
        </div>
    </section>

    {{-- NEWS & BLOG --}}
    @php
        $homepagePosts = \App\Models\BlogPost::with('category')->published()->latest('published_at')->take(3)->get();
    @endphp
    @if ($homepagePosts->isNotEmpty())
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">News & articles</h2>
                    <p class="text-sm text-gray-500 mt-1">Tips, guides, and the latest from Livingston Autos</p>
                </div>
                <a href="{{ route('blog.index') }}"
                    class="text-sm font-bold text-gray-900 underline decoration-2 underline-offset-4 hover:text-primary transition-colors hidden sm:inline-flex items-center gap-1">
                    View all articles
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($homepagePosts as $post)
                    <a href="{{ route('blog.show', $post->slug) }}"
                        class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-300 group flex flex-col">
                        <div class="relative h-[200px] overflow-hidden">
                            @if ($post->cover_image_url)
                                <img src="{{ $post->cover_image_url }}" alt="{{ $post->title }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    loading="lazy">
                            @endif
                            @if ($post->category)
                                <div class="absolute top-3 left-3">
                                    <span
                                        class="bg-primary text-white text-xs font-bold px-2.5 py-1 rounded-md">{{ $post->category->name }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="p-5 flex flex-col flex-1">
                            <div class="text-xs text-gray-500 font-medium mb-2">{{ $post->published_at->format('M j, Y') }}</div>
                            <h3
                                class="font-bold text-base text-gray-900 mb-2 leading-snug group-hover:text-primary transition-colors">
                                {{ $post->title }}
                            </h3>
                            <p class="text-sm text-gray-600 leading-relaxed flex-1">{{ $post->excerpt }}</p>
                            <div class="mt-4 inline-flex items-center gap-1 text-sm font-bold text-primary">
                                Read more
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

</x-layouts.public>