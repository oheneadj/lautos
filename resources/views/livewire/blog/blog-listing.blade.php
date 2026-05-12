<div>
    {{-- Search + Category Filter --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-8">
        <input
            type="text"
            wire:model.live.debounce.400ms="search"
            placeholder="Search articles..."
            class="input input-bordered flex-1"
        />
        <select wire:model.live="categoryFilter" class="select select-bordered sm:w-48">
            <option value="">All Categories</option>
            @foreach ($categories as $category)
                <option value="{{ $category->slug }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    @if ($posts->isEmpty())
        <div class="text-center py-20 text-base-content/40">
            <p class="text-lg font-medium">No articles found</p>
            <p class="text-sm mt-1">Check back soon for new posts.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($posts as $post)
                <a href="{{ route('blog.show', $post->slug) }}" class="card bg-base-100 shadow-sm hover:shadow-md transition-shadow border border-base-200 group">
                    @if ($post->featured_image_path)
                        <figure class="h-44 overflow-hidden">
                            <img src="{{ Storage::url($post->featured_image_path) }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </figure>
                    @endif
                    <div class="card-body p-5">
                        @if ($post->category)
                            <span class="badge badge-primary badge-soft badge-sm w-fit">{{ $post->category->name }}</span>
                        @endif
                        <h3 class="font-semibold text-base leading-snug mt-1 group-hover:text-primary transition-colors">{{ $post->title }}</h3>
                        @if ($post->excerpt)
                            <p class="text-sm text-base-content/60 line-clamp-3 mt-1">{{ $post->excerpt }}</p>
                        @endif
                        <div class="flex items-center gap-2 mt-3 text-xs text-base-content/40">
                            <span>{{ $post->published_at->format('M j, Y') }}</span>
                            @if ($post->author)
                                <span>&bull;</span>
                                <span>{{ $post->author->name }}</span>
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
