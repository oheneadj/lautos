<x-layouts.public title="How It Works">

    {{{-- HERO SECTION --}}}
    <section class="relative min-h-[50vh] flex items-center overflow-visible bg-[#1a1c23] mb-32">
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1573074617613-bfc8efcb4ba4?auto=format&fit=crop&w=2000&q=80"
                alt="Shipment and Delivery" class="w-full h-full object-cover opacity-30">
            <div class="absolute inset-0 bg-gradient-to-t from-[#1a1c23] via-transparent to-[#1a1c23]/50"></div>
        </div>

        <div class="relative z-10 max-w-[90rem] mx-auto px-4 lg:px-8 w-full py-20 text-center">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 leading-tight tracking-tight">
                How It Works <br><span class="text-primary">Your Guide to Importing</span>
            </h1>
            <p class="text-lg text-white/70 max-w-2xl mx-auto font-medium">
                Importing a car with Livingston Autos is straightforward and transparent. We handle the sourcing, and you can track your vehicle every step of the way.
            </p>
        </div>

        {{-- STATS BANNER (Overlapping) --}}
        <div class="absolute bottom-0 left-0 right-0 translate-y-1/2 px-4 lg:px-8 z-20">
            <div class="max-w-5xl mx-auto bg-[#da251d] rounded-2xl shadow-2xl overflow-hidden">
                <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-white/20">
                    <div class="text-center py-10 px-4" x-data="{ count: 0, target: 500 }" x-intersect.once="let i = setInterval(() => { count += Math.ceil(target/30); if(count >= target) { count = target; clearInterval(i); } }, 30)">
                        <div class="text-4xl lg:text-5xl font-black text-white mb-2"><span x-text="count">0</span><span class="text-white/80">+</span></div>
                        <div class="text-sm font-bold text-white/90 uppercase tracking-wider">Vehicles Imported</div>
                    </div>
                    <div class="text-center py-10 px-4" x-data="{ count: 0, target: 10 }" x-intersect.once="let i = setInterval(() => { count += 1; if(count >= target) { count = target; clearInterval(i); } }, 100)">
                        <div class="text-4xl lg:text-5xl font-black text-white mb-2"><span x-text="count">0</span><span class="text-white/80">+</span></div>
                        <div class="text-sm font-bold text-white/90 uppercase tracking-wider">Years Experience</div>
                    </div>
                    <div class="text-center py-10 px-4" x-data="{ count: 0, target: 100 }" x-intersect.once="let i = setInterval(() => { count += Math.ceil(target/30); if(count >= target) { count = target; clearInterval(i); } }, 30)">
                        <div class="text-4xl lg:text-5xl font-black text-white mb-2"><span x-text="count">0</span><span class="text-white/80">%</span></div>
                        <div class="text-sm font-bold text-white/90 uppercase tracking-wider">Satisfied Clients</div>
                    </div>
                    <div class="text-center py-10 px-4" x-data="{ count: 0, target: 150 }" x-intersect.once="let i = setInterval(() => { count += Math.ceil(target/30); if(count >= target) { count = target; clearInterval(i); } }, 30)">
                        <div class="text-4xl lg:text-5xl font-black text-white mb-2"><span x-text="count">0</span><span class="text-white/80">+</span></div>
                        <div class="text-sm font-bold text-white/90 uppercase tracking-wider">Dealer Reviews</div>
                    </div>
                </div>
            </div>
        </div>
            </div>
        </div>
    </section>

    {{-- PROCESS SECTION --}}
    <section class="max-w-[90rem] mx-auto px-4 lg:px-8 py-16 lg:py-24">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">The Import Process</h2>
            <p class="text-base text-gray-500 font-medium">Follow these five simple steps to get your dream car delivered directly to you from Japan or Korea.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            {{-- Step 1 --}}
            <div class="bg-white rounded-2xl p-8 shadow-xl shadow-gray-200/50 border border-gray-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                <div class="absolute -right-6 -top-6 text-[120px] font-black text-gray-50 group-hover:text-primary/5 transition-colors duration-300 z-0 select-none">1</div>
                <div class="relative z-10">
                    <div class="w-14 h-14 bg-primary/10 text-primary rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Browse & Select</h3>
                    <p class="text-base text-gray-500 leading-relaxed">
                        Find your ideal car in our extensive, regularly updated inventory. We provide detailed specifications, high-quality images, and transparent pricing for every vehicle.
                    </p>
                </div>
            </div>

            {{-- Step 2 --}}
            <div class="bg-white rounded-2xl p-8 shadow-xl shadow-gray-200/50 border border-gray-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                <div class="absolute -right-6 -top-6 text-[120px] font-black text-gray-50 group-hover:text-primary/5 transition-colors duration-300 z-0 select-none">2</div>
                <div class="relative z-10">
                    <div class="w-14 h-14 bg-primary/10 text-primary rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Place an Order</h3>
                    <p class="text-base text-gray-500 leading-relaxed">
                        Once you've found the perfect car, submit your request securely through our platform. Our team will immediately reserve the vehicle and prepare your invoice.
                    </p>
                </div>
            </div>

            {{-- Step 3 --}}
            <div class="bg-white rounded-2xl p-8 shadow-xl shadow-gray-200/50 border border-gray-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                <div class="absolute -right-6 -top-6 text-[120px] font-black text-gray-50 group-hover:text-primary/5 transition-colors duration-300 z-0 select-none">3</div>
                <div class="relative z-10">
                    <div class="w-14 h-14 bg-primary/10 text-primary rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Payment</h3>
                    <p class="text-base text-gray-500 leading-relaxed">
                        Complete your payment safely offline via Bank Transfer or Mobile Money. Upload your proof of payment to your dashboard, and we'll verify it and initiate shipping.
                    </p>
                </div>
            </div>

            {{-- Step 4 --}}
            <div class="bg-white rounded-2xl p-8 shadow-xl shadow-gray-200/50 border border-gray-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300 md:col-start-1 lg:col-start-2">
                <div class="absolute -right-6 -top-6 text-[120px] font-black text-gray-50 group-hover:text-primary/5 transition-colors duration-300 z-0 select-none">4</div>
                <div class="relative z-10">
                    <div class="w-14 h-14 bg-primary/10 text-primary rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Live Tracking</h3>
                    <p class="text-base text-gray-500 leading-relaxed">
                        Once shipped, track your vehicle's journey across the ocean directly from your customer dashboard. We provide real-time updates on location and estimated arrival.
                    </p>
                </div>
            </div>

            {{-- Step 5 --}}
            <div class="bg-white rounded-2xl p-8 shadow-xl shadow-gray-200/50 border border-gray-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300 md:col-start-2 lg:col-start-3">
                <div class="absolute -right-6 -top-6 text-[120px] font-black text-gray-50 group-hover:text-primary/5 transition-colors duration-300 z-0 select-none">5</div>
                <div class="relative z-10">
                    <div class="w-14 h-14 bg-primary/10 text-primary rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Clearance & Delivery</h3>
                    <p class="text-base text-gray-500 leading-relaxed">
                        When the ship arrives at Tema port, you can either handle the clearing yourself using the provided documents, or let our experienced team manage the entire clearance process.
                    </p>
                </div>
            </div>

        </div>
    </section>

    {{-- CTA SECTION --}}
    <section class="bg-gray-50 border-t border-gray-100 py-16 lg:py-20 mt-8">
        <div class="max-w-[90rem] mx-auto px-4 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Ready to find your perfect car?</h2>
            <p class="text-gray-500 mb-8 max-w-2xl mx-auto">Browse our full inventory of high-quality Japanese and Korean imports today.</p>
            <a href="{{ route('cars.index') }}" class="inline-flex items-center justify-center bg-primary text-white text-base font-bold py-4 px-10 rounded-lg hover:bg-primary/90 transition-all duration-200 shadow-lg shadow-primary/30">
                Browse Inventory
            </a>
        </div>
    </section>


    {{-- CTA BANNER --}}
    <section class="py-20 bg-[#1a1c23] relative overflow-hidden border-t border-gray-800">
        <div class="absolute top-0 right-0 w-96 h-96 bg-primary/20 blur-[100px] rounded-full translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-primary/10 blur-[100px] rounded-full -translate-x-1/2 translate-y-1/2"></div>
        
        <div class="relative z-10 max-w-4xl mx-auto px-4 lg:px-8 text-center">
            <h2 class="text-3xl md:text-5xl font-bold text-white mb-6 tracking-tight">Ready to find your perfect car?</h2>
            <p class="text-lg text-gray-400 mb-10 max-w-2xl mx-auto font-medium">Browse our curated inventory of high-quality Japanese and Korean imports, or contact our expert team for a custom order.</p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a wire:navigate href="{{ route('cars.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center bg-primary text-white text-base font-bold py-4 px-8 rounded-lg hover:bg-red-700 transition-all duration-200 shadow-lg shadow-primary/30">
                    Browse Inventory
                </a>
                <a wire:navigate href="{{ route('contact') }}" class="w-full sm:w-auto inline-flex items-center justify-center bg-white/10 text-white border border-white/20 text-base font-bold py-4 px-8 rounded-lg hover:bg-white/20 transition-all duration-200">
                    Contact Support
                </a>
            </div>
        </div>
    </section>

</x-layouts.public>
