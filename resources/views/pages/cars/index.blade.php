<x-layouts.public title="Browse Cars">

    {{-- Page Header --}}
    <div class="relative bg-[#222] pt-24 pb-36 px-4 lg:px-8 overflow-hidden">
        {{-- Background Image --}}
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1552519507-da3b142c6e3d?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80" alt="Dealership showroom" class="w-full h-full object-cover opacity-30">
            <div class="absolute inset-0 bg-gradient-to-t from-[#111] via-transparent to-transparent"></div>
        </div>
        
        <div class="max-w-[1600px] mx-auto relative z-10 text-center">
            <h1 class="text-[32px] md:text-[42px] lg:text-[46px] font-normal text-white leading-tight mb-2 tracking-wide">Find new & used cars for sale in Ghana</h1>
            <p class="text-[15px] lg:text-[18px] text-white/90 max-w-2xl mx-auto font-light">The safest way to buy or sell your car in Ghana.</p>
        </div>
    </div>

    <div class="max-w-[1600px] mx-auto px-4 lg:px-8 -mt-24 relative z-20">
        <livewire:cars.car-catalogue />
    </div>

    {{-- Browse by Make --}}
    @php
        $browseMakes = \App\Models\Make::withCount('cars')->orderByDesc('cars_count')->take(12)->get();
    @endphp
    @if ($browseMakes->isNotEmpty())
        <section class="py-16 bg-white border-t border-gray-100 mt-20">
            <div class="max-w-[1600px] mx-auto px-4 lg:px-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-10 text-center">Browse by make</h2>
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-y-8 gap-x-4 justify-items-center">
                    @foreach ($browseMakes as $make)
                        <a href="{{ route('cars.index', ['make' => $make->slug]) }}" class="flex flex-col items-center gap-3 group w-full">
                            <div class="w-20 h-20 flex items-center justify-center transition-transform group-hover:scale-110">
                                @if ($make->icon_path)
                                    <img src="{{ Storage::url($make->icon_path) }}" class="w-full h-full object-contain grayscale opacity-80 group-hover:grayscale-0 group-hover:opacity-100 transition-all" alt="{{ $make->name }} Logo" loading="lazy">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($make->name) }}&background=transparent&color=374151&bold=true&format=svg" class="w-full h-full object-contain opacity-80 group-hover:opacity-100 transition-all" alt="{{ $make->name }} Logo" loading="lazy">
                                @endif
                            </div>
                            <span class="text-[13px] font-medium text-gray-500 group-hover:text-gray-900 transition-colors text-center">{{ $make->name }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Latest News --}}
    @php
        $latestPosts = \App\Models\BlogPost::published()->latest('published_at')->take(3)->get();
    @endphp
    @if ($latestPosts->isNotEmpty())
        <section class="py-16 bg-gray-50 border-t border-gray-100">
            <div class="max-w-[1600px] mx-auto px-4 lg:px-8">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">Latest news</h2>
                    <a href="{{ route('blog.index') }}" class="text-[14px] font-bold text-gray-900 underline decoration-2 underline-offset-4 hover:text-primary transition-colors">View more</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ($latestPosts as $post)
                        <a href="{{ route('blog.show', $post->slug) }}" class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow group">
                            @if ($post->cover_image_path)
                                <div class="h-40 overflow-hidden">
                                    <img src="{{ Storage::url($post->cover_image_path) }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                                </div>
                            @endif
                            <div class="p-5">
                                <h3 class="text-[15px] font-bold text-gray-900 leading-snug group-hover:text-primary transition-colors">{{ $post->title }}</h3>
                                <p class="text-[12px] text-gray-500 mt-2">{{ $post->published_at->format('M j, Y') }}</p>
                                <p class="text-[13px] text-gray-600 mt-2 line-clamp-2">{{ $post->excerpt }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

</x-layouts.public>
