<div>
    <div class="flex flex-col sm:flex-row gap-3 mb-8">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-base-content/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input
                type="text"
                wire:model.live.debounce.400ms="search"
                placeholder="Search articles..."
                class="w-full pl-9 pr-4 py-[10px] text-[13px] bg-base-200 border-none rounded-lg outline-none font-medium placeholder:text-base-content/40 focus:ring-2 focus:ring-primary/20"
            />
        </div>
        <select wire:model.live="categoryFilter" class="bg-base-200 border-none text-[13px] rounded-lg px-3 py-2 outline-none font-medium focus:ring-2 focus:ring-primary/20 sm:w-44">
            <option value="">All Categories</option>
            @foreach ($categories as $category)
                <option value="{{ $category->slug }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    @if ($posts->isEmpty())
        <div class="text-center py-20">
            <p class="text-[15px] font-semibold text-base-content/40">No articles found</p>
            <p class="text-[13px] text-base-content/30 mt-1">Check back soon for new posts.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($posts as $post)
                <a href="{{ route('blog.show', $post->slug) }}" class="bg-base-100 border border-base-content/10 rounded-lg shadow-sm transition-all duration-200 hover:shadow-md group flex flex-col overflow-hidden">
                    @if ($post->featured_image_path)
                        <div class="h-40 overflow-hidden flex-shrink-0">
                            <img src="{{ Storage::url($post->featured_image_path) }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                        </div>
                    @endif
                    <div class="p-5 flex flex-col flex-1">
                        @if ($post->category)
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-primary/10 text-primary border border-primary/20 w-fit mb-2">{{ $post->category->name }}</span>
                        @endif
                        <h3 class="text-[14px] font-semibold text-base-content leading-snug group-hover:text-primary transition-colors">{{ $post->title }}</h3>
                        @if ($post->excerpt)
                            <p class="text-[13px] text-base-content/50 mt-2 leading-relaxed line-clamp-3">{{ $post->excerpt }}</p>
                        @endif
                        <div class="flex items-center gap-2 mt-auto pt-3 border-t border-base-content/5">
                            <span class="text-[11px] text-base-content/40 font-medium">{{ $post->published_at->format('M j, Y') }}</span>
                            @if ($post->author)
                                <span class="text-base-content/20 text-[11px]">&bull;</span>
                                <span class="text-[11px] text-base-content/40 font-medium">{{ $post->author->name }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    @endif
</div>
