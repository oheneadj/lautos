<div class="py-20 bg-white" x-data="{ active: 0, total: {{ count($reviewGroups) }} }">
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
</div>
