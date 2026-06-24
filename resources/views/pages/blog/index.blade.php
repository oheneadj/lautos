<x-layouts.public title="News & Guides">

    {{-- PAGE WRAPPER (Clean White Background) --}}
    <div class="bg-white pb-24 font-sans text-gray-900">

        {{-- BREADCRUMB & HEADER --}}
        <div class="max-w-[85rem] mx-auto px-4 lg:px-8 pt-8">
            <nav class="flex items-center gap-2 text-[12px] font-medium text-gray-500 mb-4">
                <a href="{{ route('home') }}" class="hover:text-gray-900 transition-colors underline decoration-1 underline-offset-2">Home</a>
                <span class="text-gray-300">/</span>
                <span class="text-gray-500">News & Guides</span>
            </nav>
            
            <h1 class="text-4xl md:text-5xl font-black tracking-tight mb-6">
                News & Guides
            </h1>
            <p class="text-[15px] text-gray-500 font-medium max-w-3xl mb-8">
                Your trusted source for the latest automotive news, expert car reviews, and buying guides.
            </p>
        </div>

        {{-- MAIN CONTENT: Livewire Listing --}}
        <livewire:blog.blog-listing />

    </div>

</x-layouts.public>
