<x-layouts.public :title="$post->title">

    <article class="max-w-3xl mx-auto px-4 lg:px-8 py-10">

        {{-- Breadcrumb --}}
        <nav class="text-sm text-base-content/50 mb-6 flex items-center gap-2">
            <a href="{{ route('home') }}" class="hover:text-primary">Home</a>
            <span>/</span>
            <a href="{{ route('blog.index') }}" class="hover:text-primary">Blog</a>
            <span>/</span>
            <span class="text-base-content line-clamp-1">{{ $post->title }}</span>
        </nav>

        {{-- Category --}}
        @if ($post->category)
            <span class="badge badge-primary badge-soft mb-4">{{ $post->category->name }}</span>
        @endif

        <h1 class="text-3xl lg:text-4xl font-bold leading-tight mb-4">{{ $post->title }}</h1>

        <div class="flex items-center gap-3 text-sm text-base-content/50 mb-8">
            @if ($post->author)
                <span>By {{ $post->author->name }}</span>
                <span>&bull;</span>
            @endif
            <span>{{ $post->published_at->format('F j, Y') }}</span>
        </div>

        @if ($post->featured_image_path)
            <div class="rounded-2xl overflow-hidden mb-8 h-72 lg:h-96">
                <img src="{{ Storage::url($post->featured_image_path) }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
            </div>
        @endif

        {{-- Body --}}
        <div class="prose prose-base max-w-none text-base-content">
            {!! nl2br(e($post->body)) !!}
        </div>

        {{-- Footer --}}
        <div class="mt-12 pt-8 border-t border-base-200 flex items-center justify-between">
            <a href="{{ route('blog.index') }}" class="btn btn-outline btn-sm">← Back to Blog</a>
            <a href="{{ route('contact') }}" class="btn btn-primary btn-sm">Get in Touch</a>
        </div>

    </article>

</x-layouts.public>
