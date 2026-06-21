<x-layouts.public :title="$post->title">

    <article class="max-w-3xl mx-auto px-4 lg:px-8 py-10">

        <nav class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-widest text-base-content/40 mb-6">
            <a href="{{ route('home') }}" class="hover:text-base-content transition-colors">Home</a>
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('blog.index') }}" class="hover:text-base-content transition-colors">Blog</a>
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            <span class="text-base-content truncate max-w-[200px]">{{ $post->title }}</span>
        </nav>

        @if ($post->category)
            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-primary/10 text-primary border border-primary/20 inline-block mb-4">{{ $post->category->name }}</span>
        @endif

        <h1 class="text-[28px] lg:text-[36px] font-semibold text-base-content leading-tight mb-4">{{ $post->title }}</h1>

        <div class="flex items-center gap-3 mb-8">
            @if ($post->author)
                <span class="text-[12px] text-base-content/40 font-medium">By {{ $post->author->name }}</span>
                <span class="text-base-content/20">&bull;</span>
            @endif
            <span class="text-[12px] text-base-content/40 font-medium">{{ $post->published_at->format('F j, Y') }}</span>
        </div>

        @if ($post->featured_image_path)
            <div class="rounded-lg overflow-hidden mb-8 h-64 lg:h-80 border border-base-content/10">
                <img src="{{ Storage::url($post->featured_image_path) }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
            </div>
        @endif

        <div class="text-[15px] text-base-content/80 leading-relaxed space-y-4">
            {!! nl2br(e($post->body)) !!}
        </div>

        <div class="mt-12 pt-8 border-t border-base-content/10 flex items-center justify-between">
            <x-ui.button href="{{ route('blog.index') }}" variant="outline" size="sm">← Back to Blog</x-ui.button>
            <x-ui.button href="{{ route('contact') }}" variant="primary" size="sm">Get in Touch</x-ui.button>
        </div>

    </article>

</x-layouts.public>
