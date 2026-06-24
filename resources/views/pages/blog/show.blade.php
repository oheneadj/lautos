<x-layouts.public :title="$post->title">

    <div class="bg-white pb-24 font-sans text-gray-900">
        
        {{-- BREADCRUMB & HEADER --}}
        <div class="max-w-[85rem] mx-auto px-4 lg:px-8 pt-8 pb-10">
            <nav class="flex items-center gap-2 text-[12px] font-medium text-gray-500 mb-6">
                <a href="{{ route('home') }}" class="hover:text-gray-900 transition-colors underline decoration-1 underline-offset-2">Home</a>
                <span class="text-gray-300">/</span>
                <a href="{{ route('blog.index') }}" class="hover:text-gray-900 transition-colors underline decoration-1 underline-offset-2">News & Guides</a>
                <span class="text-gray-300">/</span>
                @if($post->category)
                    <span class="text-gray-500">{{ $post->category->name }}</span>
                @endif
            </nav>

            <div class="max-w-4xl">
                <h1 class="text-3xl md:text-4xl lg:text-[44px] font-black tracking-tight mb-6 leading-tight">
                    {{ $post->title }}
                </h1>
                
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 border-b border-gray-100 pb-6">
                    <div class="flex items-center gap-4 text-[13px] font-medium text-gray-500">
                        @if($post->author)
                            <div class="flex items-center gap-2 text-gray-900">
                                <span>By</span>
                                <span class="font-bold hover:underline cursor-pointer">{{ $post->author->name }}</span>
                            </div>
                            <span class="text-gray-300">&bull;</span>
                        @endif
                        <span>{{ $post->published_at->format('F j, Y') }}</span>
                        <span class="text-gray-300">&bull;</span>
                        <span>{{ $post->read_time }} min read</span>
                    </div>

                    {{-- Share Icons --}}
                    <div class="flex items-center gap-4">
                        <span class="text-[12px] font-bold text-gray-400 uppercase tracking-widest">Share</span>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-full bg-[#1877F2] text-white flex items-center justify-center hover:opacity-80 transition-opacity" title="Share on Facebook">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" /></svg>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($post->title) }}" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-full bg-black text-white flex items-center justify-center hover:opacity-80 transition-opacity" title="Share on X">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        <button id="copy-link-btn" class="relative w-8 h-8 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center hover:bg-gray-200 transition-colors" title="Copy link">
                            <svg id="copy-link-icon" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            <svg id="copy-check-icon" style="display: none;" class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <div id="copy-tooltip" style="display: none;" class="absolute -top-8 bg-gray-900 text-white text-[10px] font-bold px-2 py-1 rounded whitespace-nowrap">
                                Copied!
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MAIN SPLIT LAYOUT --}}
        <div class="max-w-[85rem] mx-auto px-4 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16">
                
                {{-- LEFT COLUMN (Main Content) --}}
                <div class="lg:col-span-8">
                    
                    {{-- Hero Image --}}
                    @if ($post->cover_image_url)
                    <div class="mb-8">
                        <div class="w-full aspect-[16/9] md:aspect-[21/9] rounded-2xl overflow-hidden shadow-lg shadow-gray-200/50">
                            <img src="{{ $post->cover_image_url }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                        </div>
                        <p class="text-[11px] text-gray-400 font-medium mt-3 text-right">Livingston Autos / Featured Image</p>
                    </div>
                    @endif

                    {{-- Article Body --}}
                    <article class="prose prose-lg prose-gray max-w-none text-gray-800 font-medium mb-12 prose-headings:font-bold prose-headings:tracking-tight prose-a:text-primary prose-a:no-underline hover:prose-a:underline prose-img:rounded-xl">
                        {!! $post->body !!}
                    </article>

                    {{-- INLINE PROMO MOCKUPS (Below Body) --}}
                    <div class="border-t border-gray-100 pt-10 mb-16">
                        
                        {{-- Mock Video Embed --}}
                        <div class="mb-10">
                            <h3 class="text-[14px] font-bold text-gray-900 mb-4">Related Video:</h3>
                            <div class="relative w-full aspect-video rounded-xl overflow-hidden group cursor-pointer shadow-lg shadow-gray-200/50 border border-gray-100">
                                @if ($post->cover_image_url)
                                    <img src="{{ $post->cover_image_url }}" class="w-full h-full object-cover blur-sm brightness-75 group-hover:scale-105 transition-transform duration-700">
                                @else
                                    <img src="https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=800&q=80" class="w-full h-full object-cover blur-sm brightness-75 group-hover:scale-105 transition-transform duration-700">
                                @endif
                                
                                <div class="absolute top-0 left-0 w-full p-4 bg-gradient-to-b from-black/60 to-transparent flex items-center justify-between">
                                    <div class="text-white font-bold text-[15px] truncate">Everything you need to know about {{ $post->category ? $post->category->name : 'importing' }}</div>
                                    <div class="text-white/80 text-[12px] flex items-center gap-1 shrink-0"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" /></svg> Share</div>
                                </div>

                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="w-16 h-16 bg-primary text-white rounded-full flex items-center justify-center group-hover:scale-110 group-hover:bg-red-700 transition-all duration-300 shadow-xl shadow-primary/30">
                                        <svg class="w-6 h-6 translate-x-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    </div>
                                </div>

                                <div class="absolute bottom-4 left-4 bg-white/90 backdrop-blur-sm rounded-full px-3 py-1 text-gray-900 font-bold text-[11px] flex items-center gap-2">
                                    <div class="w-4 h-4 rounded-full bg-primary flex items-center justify-center text-white"><svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></div>
                                    Livingston Autos
                                </div>
                            </div>
                        </div>

                        {{-- Mock Google News Promo --}}
                        <div class="inline-flex items-center gap-4 bg-white border border-gray-200 shadow-sm rounded-xl py-3 px-5 hover:bg-gray-50 transition-colors cursor-pointer group">
                            <div class="w-6 h-6 shrink-0">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M23.766 12.2764C23.766 11.4607 23.6999 10.6406 23.5588 9.83807H12.24V14.4591H18.7217C18.4528 15.9494 17.5885 17.2678 16.323 18.1056V21.1039H20.19C22.4608 19.0139 23.766 15.9274 23.766 12.2764Z" fill="#4285F4"/><path d="M12.2401 24.0008C15.4766 24.0008 18.2059 22.9382 20.1945 21.1039L16.3276 18.1055C15.2517 18.8375 13.8627 19.252 12.2445 19.252C9.11388 19.252 6.45946 17.1399 5.50705 14.3003H1.5166V17.3912C3.55371 21.4434 7.7029 24.0008 12.2401 24.0008Z" fill="#34A853"/><path d="M5.50253 14.3003C5.00315 12.8099 5.00315 11.1961 5.50253 9.70575V6.61481H1.51649C-0.18551 10.0056 -0.18551 14.0004 1.51649 17.3912L5.50253 14.3003Z" fill="#FBBC04"/><path d="M12.2401 4.74966C13.9509 4.7232 15.6044 5.36697 16.8434 6.54867L20.2695 3.12262C18.1001 1.08405 15.2208 -0.034466 12.2401 0.000808666C7.7029 0.000808666 3.55371 2.55822 1.5166 6.61481L5.50264 9.70575C6.45064 6.86173 9.10947 4.74966 12.2401 4.74966Z" fill="#EA4335"/></svg>
                            </div>
                            <div class="text-[14px] text-gray-900 font-medium">
                                Add <span class="font-bold">Livingston Autos</span> as a preferred source on Google
                            </div>
                        </div>

                    </div>

                    {{-- FEATURED STORIES GRID (Bottom of left col) --}}
                    @if($featuredStories->isNotEmpty())
                    <div>
                        <h2 class="text-2xl font-bold mb-6">Featured stories</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($featuredStories as $story)
                                <a href="{{ route('blog.show', $story->slug) }}" class="group block">
                                    <div class="w-full aspect-video rounded-xl overflow-hidden mb-3 shadow-sm">
                                        @if($story->cover_image_url)
                                            <img src="{{ $story->cover_image_url }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                        @endif
                                    </div>
                                    @if($story->category)
                                        <span class="text-primary text-[10px] font-black uppercase tracking-widest mb-1 block">{{ $story->category->name }}</span>
                                    @endif
                                    <h4 class="text-[15px] font-bold text-gray-900 leading-snug group-hover:text-primary transition-colors mb-2">{{ $story->title }}</h4>
                                    <div class="text-[11px] text-gray-400 font-medium flex items-center gap-1">
                                        @if($story->author)
                                            <span>By {{ $story->author->name }}</span>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>

                {{-- RIGHT COLUMN (Sidebar) --}}
                <div class="lg:col-span-4">
                    
                    {{-- "Shop this make" Widget Mockup --}}
                    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 mb-10 shadow-sm">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <div class="text-[11px] text-gray-400 font-bold uppercase tracking-widest mb-1">Import Experts</div>
                                <h4 class="text-xl font-bold text-gray-900">Custom Order</h4>
                            </div>
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm border border-gray-100 shrink-0">
                                <svg class="w-6 h-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            </div>
                        </div>
                        <p class="text-[13px] text-gray-600 font-medium mb-6">
                            Want to import a specific vehicle from Japan or Korea? We handle the entire process from auction bidding to port clearance.
                        </p>
                        <a href="{{ route('contact') }}" class="block w-full bg-gray-900 text-white text-center font-bold py-3 rounded-lg text-[13px] hover:bg-gray-800 transition-colors">
                            Contact Sales Team
                        </a>
                    </div>

                    {{-- Latest News Stack --}}
                    @if($latestNews->isNotEmpty())
                    <div class="mb-10">
                        <h2 class="text-xl font-bold mb-6">Latest news</h2>
                        <div class="space-y-5">
                            @foreach($latestNews as $news)
                                <a href="{{ route('blog.show', $news->slug) }}" class="group block cursor-pointer flex gap-4">
                                    <div class="flex flex-col justify-center flex-1">
                                        @if($news->category)
                                            <span class="text-primary text-[9px] font-black uppercase tracking-widest mb-1 block">{{ $news->category->name }}</span>
                                        @endif
                                        <h4 class="text-[14px] font-bold text-gray-900 leading-snug group-hover:text-primary transition-colors mb-1">{{ $news->title }}</h4>
                                        <div class="text-[10px] text-gray-400 font-medium">By {{ $news->author->name ?? 'Livingston' }} &bull; {{ $news->published_at->format('M j, Y') }}</div>
                                    </div>
                                    <div class="w-24 aspect-[4/3] rounded-lg overflow-hidden shrink-0 shadow-sm">
                                        @if($news->cover_image_url)
                                            <img src="{{ $news->cover_image_url }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                        @endif
                                    </div>
                                </a>
                                @if(!$loop->last)
                                    <hr class="border-gray-100">
                                @endif
                            @endforeach
                        </div>
                        <div class="flex justify-end mt-4">
                            <a href="{{ route('blog.index') }}" class="text-[13px] font-bold text-primary hover:underline">See all latest news &rarr;</a>
                        </div>
                    </div>
                    @endif

                    {{-- Promo Banner Mockup --}}
                    <div class="sticky top-24">
                        <div class="w-full aspect-square bg-[#da251d] rounded-2xl overflow-hidden relative shadow-xl shadow-red-200/50 group cursor-pointer">
                            <div class="absolute inset-0 bg-gradient-to-br from-black/40 to-transparent"></div>
                            
                            <div class="absolute inset-0 p-8 flex flex-col items-center justify-center text-center">
                                <h4 class="text-white font-black text-3xl italic tracking-tight leading-none mb-2">IMPORT NOW.</h4>
                                <h4 class="text-white font-black text-3xl italic tracking-tight leading-none mb-4">SAVE MORE.</h4>
                                
                                <div class="bg-white text-[#da251d] font-black text-4xl px-4 py-2 rounded-lg mb-6 shadow-lg rotate-[-3deg] group-hover:rotate-0 transition-transform duration-300">
                                    15% OFF
                                </div>
                                
                                <p class="text-white/90 text-[13px] font-bold uppercase tracking-widest mb-6">On Port Clearance Fees</p>
                                
                                <span class="bg-gray-900 text-white text-[13px] font-bold py-3 px-8 rounded-full hover:bg-black transition-colors">Claim Offer</span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var btn = document.getElementById('copy-link-btn');
            if (!btn) return;

            btn.addEventListener('click', function () {
                var url = window.location.href;

                function onSuccess() {
                    var icon = document.getElementById('copy-link-icon');
                    var check = document.getElementById('copy-check-icon');
                    var tooltip = document.getElementById('copy-tooltip');
                    if (icon) icon.style.display = 'none';
                    if (check) check.style.display = 'block';
                    if (tooltip) tooltip.style.display = 'block';
                    setTimeout(function () {
                        if (icon) icon.style.display = 'block';
                        if (check) check.style.display = 'none';
                        if (tooltip) tooltip.style.display = 'none';
                    }, 2000);
                }

                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(url).then(onSuccess).catch(function () {
                        fallbackCopy(url, onSuccess);
                    });
                } else {
                    fallbackCopy(url, onSuccess);
                }
            });

            function fallbackCopy(text, callback) {
                var ta = document.createElement('textarea');
                ta.value = text;
                ta.style.position = 'fixed';
                ta.style.left = '-9999px';
                ta.style.top = '-9999px';
                document.body.appendChild(ta);
                ta.focus();
                ta.select();
                try {
                    document.execCommand('copy');
                    callback();
                } catch (e) {
                    window.prompt('Copy this link:', text);
                }
                document.body.removeChild(ta);
            }
        });
    </script>

</x-layouts.public>
