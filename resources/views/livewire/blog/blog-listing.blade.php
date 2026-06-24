<div>
    {{-- HORIZONTAL CATEGORY NAVIGATION & SEARCH --}}
    <div class="max-w-[85rem] mx-auto px-4 lg:px-8 mb-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6 pb-4 border-b border-gray-200">
            
            {{-- Category Pills --}}
            <div class="flex items-center gap-2 flex-wrap w-full md:w-auto">
                <button wire:click="$set('categoryFilter', '')" class="text-sm font-bold px-4 py-2 rounded-full transition-colors {{ empty($categoryFilter) ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">All</button>
                @foreach ($categories as $category)
                    <button wire:click="$set('categoryFilter', '{{ $category->slug }}')" class="text-sm font-bold px-4 py-2 rounded-full transition-colors {{ $categoryFilter === $category->slug ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>

            {{-- Search Bar --}}
            <div class="relative w-full md:w-72 shrink-0">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input
                    type="text"
                    wire:model.live.debounce.400ms="search"
                    placeholder="Search articles..."
                    class="w-full pl-9 pr-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg outline-none font-medium placeholder:text-gray-400 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                />
            </div>
        </div>
    </div>

    @if ($isFiltered)
        {{-- STANDARD GRID RESULTS --}}
        <section class="max-w-[85rem] mx-auto px-4 lg:px-8">
            @if ($posts->isEmpty())
                <div class="text-center py-20">
                    <p class="text-base font-semibold text-gray-500">No articles found</p>
                    <p class="text-sm text-gray-400 mt-1">Try adjusting your search or category filter.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($posts as $post)
                        <a href="{{ route('blog.show', $post->slug) }}" class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300 flex flex-col h-full flex flex-col overflow-hidden">
                            @if ($post->cover_image_url)
                                <div class="h-48 overflow-hidden flex-shrink-0">
                                    <img src="{{ $post->cover_image_url }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                </div>
                            @endif
                            <div class="p-6 flex flex-col flex-1">
                                @if ($post->category)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-black uppercase tracking-widest bg-primary/10 text-primary border border-primary/20 w-fit mb-3">{{ $post->category->name }}</span>
                                @endif
                                <h3 class="text-base font-bold text-gray-900 leading-snug group-hover:text-primary transition-colors">{{ $post->title }}</h3>
                                @if ($post->excerpt)
                                    <p class="text-sm text-gray-500 mt-2 leading-relaxed line-clamp-3">{{ $post->excerpt }}</p>
                                @endif
                                <div class="flex items-center gap-2 mt-auto pt-4 border-t border-gray-100">
                                    <span class="text-xs text-gray-400 font-medium">{{ $post->published_at->format('M j, Y') }}</span>
                                    @if ($post->author)
                                        <span class="text-gray-300 text-xs">&bull;</span>
                                        <span class="text-xs text-gray-400 font-medium">{{ $post->author->name }}</span>
                                    @endif
                                    <span class="text-gray-300 text-xs">&bull;</span>
                                    <span class="text-xs text-gray-400 font-medium">{{ $post->read_time }} min read</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $posts->links() }}
                </div>
            @endif
        </section>
    @else
        {{-- COMPLEX LANDING PAGE --}}
        
        @if($featuredNews)
        {{-- FEATURED NEWS --}}
        <section class="max-w-[85rem] mx-auto px-4 lg:px-8 mb-16">
            <h2 class="text-2xl font-bold mb-6">Featured news</h2>
            
            {{-- Big Hero Card --}}
            <a href="{{ route('blog.show', $featuredNews->slug) }}" class="relative block w-full h-[400px] md:h-[500px] rounded-2xl overflow-hidden mb-6 group cursor-pointer">
                @if($featuredNews->cover_image_url)
                    <img src="{{ $featuredNews->cover_image_url }}" alt="{{ $featuredNews->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>

                {{-- Content --}}
                <div class="absolute bottom-0 left-0 p-8 md:p-12 w-full max-w-4xl">
                    <h3 class="text-3xl md:text-5xl font-bold text-white mb-4 leading-tight">{{ $featuredNews->title }}</h3>
                    <p class="text-white/80 text-base md:text-base font-medium mb-6 line-clamp-2 md:line-clamp-none max-w-2xl">
                        {{ $featuredNews->excerpt }}
                    </p>
                    <div class="flex items-center gap-4">
                        <span class="bg-white text-gray-900 font-bold px-6 py-3 rounded-full text-sm hover:bg-gray-100 transition-colors inline-block">
                            Read more
                        </span>
                        <span class="text-white/70 text-sm font-medium">{{ $featuredNews->read_time }} min read</span>
                    </div>
                </div>
            </a>

            {{-- 2 Sub-Featured Cards --}}
            @if($subFeaturedNews->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($subFeaturedNews as $sub)
                <a href="{{ route('blog.show', $sub->slug) }}" class="flex gap-4 group">
                    <div class="w-1/3 aspect-[4/3] rounded-xl overflow-hidden shrink-0">
                        @if($sub->cover_image_url)
                            <img src="{{ $sub->cover_image_url }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @endif
                    </div>
                    <div>
                        @if($sub->category)
                            <span class="text-primary text-xs font-black uppercase tracking-widest mb-1 block">{{ $sub->category->name }}</span>
                        @endif
                        <h4 class="font-bold text-gray-900 text-base leading-snug group-hover:text-primary transition-colors mb-2">{{ $sub->title }}</h4>
                        <p class="text-sm text-gray-500 line-clamp-3">{{ $sub->excerpt }}</p>
                    </div>
                </a>
                @endforeach
            </div>
            @endif
        </section>
        @endif

        {{-- MAIN SPLIT LAYOUT --}}
        <section class="max-w-[85rem] mx-auto px-4 lg:px-8 mb-20">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                
                {{-- LEFT COLUMN: Latest News --}}
                <div class="lg:col-span-8">
                    <h2 class="text-2xl font-bold mb-6">Latest news</h2>
                    
                    @if($topStory)
                    {{-- Top Story --}}
                    <a href="{{ route('blog.show', $topStory->slug) }}" class="mb-10 group block cursor-pointer">
                        <div class="w-full aspect-[16/9] md:aspect-[2/1] rounded-2xl overflow-hidden mb-5">
                            @if($topStory->cover_image_url)
                                <img src="{{ $topStory->cover_image_url }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @endif
                        </div>
                        @if($topStory->category)
                            <span class="text-primary text-xs font-black uppercase tracking-widest mb-2 block">{{ $topStory->category->name }}</span>
                        @endif
                        <h3 class="text-2xl md:text-3xl font-bold text-gray-900 leading-tight mb-3 group-hover:text-primary transition-colors">{{ $topStory->title }}</h3>
                        <p class="text-gray-600 mb-4 text-base leading-relaxed">
                            {{ $topStory->excerpt }}
                        </p>
                        <div class="text-xs text-gray-400 font-medium flex items-center gap-2">
                            @if($topStory->author)
                                <span>By {{ $topStory->author->name }}</span>
                                <span>&bull;</span>
                            @endif
                            <span>{{ $topStory->published_at->format('M d, Y') }}</span>
                            <span>&bull;</span>
                            <span>{{ $topStory->read_time }} min read</span>
                        </div>
                    </a>
                    @endif

                    {{-- News Grid (2 columns) --}}
                    @if($latestNews->isNotEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-10 mb-8">
                        @foreach($latestNews as $latest)
                        <a href="{{ route('blog.show', $latest->slug) }}" class="group block cursor-pointer">
                            <div class="w-full aspect-[16/10] rounded-xl overflow-hidden mb-4">
                                @if($latest->cover_image_url)
                                    <img src="{{ $latest->cover_image_url }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @endif
                            </div>
                            @if($latest->category)
                                <span class="text-primary text-xs font-black uppercase tracking-widest mb-2 block">{{ $latest->category->name }}</span>
                            @endif
                            <h4 class="text-lg font-bold text-gray-900 leading-snug mb-2 group-hover:text-primary transition-colors">{{ $latest->title }}</h4>
                            <div class="text-xs text-gray-400 font-medium">
                                @if($latest->author)
                                    By {{ $latest->author->name }} &bull; 
                                @endif
                                {{ $latest->published_at->format('M d, Y') }}
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @endif


                </div>

                {{-- RIGHT COLUMN: Sidebar (Featured Stories & Reviews) --}}
                <div class="lg:col-span-4">
                    
                    {{-- Featured Stories Stack --}}
                    @if($featuredStories->isNotEmpty())
                    <div class="mb-12">
                        <h2 class="text-xl font-bold mb-6">Featured stories</h2>
                        <div class="space-y-6">
                            @foreach($featuredStories as $index => $story)
                                @if($index === 0)
                                    {{-- Story 1 (Large) --}}
                                    <a href="{{ route('blog.show', $story->slug) }}" class="group block">
                                        <div class="w-full aspect-video rounded-xl overflow-hidden mb-3">
                                            @if($story->cover_image_url)
                                                <img src="{{ $story->cover_image_url }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                            @endif
                                        </div>
                                        @if($story->category)
                                            <span class="text-primary text-xs font-black uppercase tracking-widest mb-1 block">{{ $story->category->name }}</span>
                                        @endif
                                        <h4 class="text-base font-bold text-gray-900 leading-snug group-hover:text-primary transition-colors">{{ $story->title }}</h4>
                                        <div class="text-xs text-gray-400 font-medium mt-1">{{ $story->published_at->format('M d, Y') }}</div>
                                    </a>
                                @else
                                    {{-- Story 2/3 (Mini Horizontal) --}}
                                    <a href="{{ route('blog.show', $story->slug) }}" class="flex gap-3 group">
                                        <div class="w-24 aspect-video rounded-lg overflow-hidden shrink-0">
                                            @if($story->cover_image_url)
                                                <img src="{{ $story->cover_image_url }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                            @endif
                                        </div>
                                        <div>
                                            @if($story->category)
                                                <span class="text-primary text-xs font-black uppercase tracking-widest mb-1 block">{{ $story->category->name }}</span>
                                            @endif
                                            <h4 class="text-sm font-bold text-gray-900 leading-tight group-hover:text-primary transition-colors">{{ $story->title }}</h4>
                                            <div class="text-xs text-gray-400 font-medium mt-1">{{ $story->published_at->format('M d, Y') }}</div>
                                        </div>
                                    </a>
                                @endif
                            @endforeach
                            
                            {{-- Import Promo Banner --}}
                            <div class="bg-[#1a1c23] rounded-xl p-6 text-center mt-6 shadow-xl shadow-gray-200/50 border border-gray-800">
                                <div class="text-xs text-primary font-black uppercase tracking-widest mb-2">Custom Imports</div>
                                <h4 class="text-white font-bold text-lg leading-tight mb-4">Can't find what you're looking for? Order it.</h4>
                                <a href="{{ route('contact') }}" class="w-full inline-block bg-primary text-white font-bold py-3 rounded-lg text-sm hover:bg-red-700 transition-colors">Start Custom Order</a>
                            </div>

                        </div>
                    </div>
                    @endif



                </div>
            </div>
        </section>

    @endif
</div>
