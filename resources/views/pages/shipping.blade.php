<x-layouts.public title="Shipping & Delivery">

    {{-- LOGISTICS SPLIT SECTION --}}
    <section class="max-w-[90rem] mx-auto px-4 lg:px-8 py-16 lg:py-24">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-center">

            {{-- Left Side: Text --}}
            <div>
                <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 leading-tight mb-6">
                    Reliable <span class="text-primary">Shipping Logistics</span>
                </h1>

                <p class="text-[16px] text-gray-600 leading-relaxed mb-6 font-medium">
                    We work with the world's leading RoRo (Roll-on/Roll-off) and container shipping lines to ensure your
                    vehicle arrives safely and on time. Our strong logistics partnerships allow us to offer competitive
                    shipping rates and priority boarding for your vehicle.
                </p>

                <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 flex gap-4 items-start mb-8">
                    <div
                        class="w-12 h-12 bg-primary/10 text-primary rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-900 mb-1">Fully Insured Transit</h4>
                        <p class="text-[14px] text-gray-500 leading-relaxed">
                            Full marine insurance is included for all direct purchases. Your vehicle is protected
                            against damage or total loss from the moment it leaves the port of origin until it reaches
                            Tema port.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Right Side: Image with Red Box --}}
            <div class="relative order-first lg:order-last">
                <div class="rounded-2xl overflow-hidden shadow-2xl relative z-10 border border-gray-100">
                    <img src="https://images.unsplash.com/photo-1494412519320-aa613dfb7738?auto=format&fit=crop&w=1200&q=80"
                        alt="Cars loading onto ship" class="w-full h-auto object-cover min-h-[400px]">
                </div>

                {{-- Overlapping Red Title Box --}}
                <div
                    class="absolute bottom-8 -left-4 md:-left-12 lg:-left-12 z-20 bg-[#da251d] text-white p-8 md:p-10 shadow-2xl max-w-[320px] rounded-xl transform translate-y-1/4 lg:translate-y-0">
                    <h3 class="text-2xl md:text-3xl font-bold leading-tight">
                        Partners with Top Global Shipping Lines
                    </h3>
                </div>

                {{-- Decorative element --}}
                <div
                    class="absolute -bottom-6 -right-6 w-32 h-32 bg-gray-100 rounded-full z-0 opacity-50 mix-blend-multiply">
                </div>
            </div>

        </div>
    </section>

    {{-- TIMELINES SECTION --}}
    <section class="bg-gray-50 py-16 lg:py-24 border-t border-gray-100">
        <div class="max-w-[90rem] mx-auto px-4 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">Estimated Shipping Timelines</h2>
                <p class="text-[16px] text-gray-500 font-medium">Clear expectations for when your vehicle will arrive at
                    Tema Port.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">

                {{-- Japan Card --}}
                <div
                    class="bg-white rounded-2xl p-8 shadow-xl shadow-gray-200/50 border border-gray-100 flex flex-col items-center text-center group hover:-translate-y-1 transition-transform duration-300">
                    <div
                        class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-6 shadow-inner text-4xl">
                        🇯🇵
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">From Japan</h3>
                    <div class="text-primary font-black text-4xl mb-4">5 - 7 <span
                            class="text-xl text-gray-400 font-bold uppercase tracking-wider">Weeks</span></div>
                    <p class="text-[15px] text-gray-500 leading-relaxed">
                        Shipping from Yokohama, Kobe, or Nagoya ports directly to Tema. This timeline accounts for
                        vessel scheduling, loading, and transit across the ocean.
                    </p>
                </div>

                {{-- Korea Card --}}
                <div
                    class="bg-white rounded-2xl p-8 shadow-xl shadow-gray-200/50 border border-gray-100 flex flex-col items-center text-center group hover:-translate-y-1 transition-transform duration-300">
                    <div
                        class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-6 shadow-inner text-4xl">
                        🇰🇷
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">From South Korea</h3>
                    <div class="text-primary font-black text-4xl mb-4">4 - 6 <span
                            class="text-xl text-gray-400 font-bold uppercase tracking-wider">Weeks</span></div>
                    <p class="text-[15px] text-gray-500 leading-relaxed">
                        Shipping from Incheon or Masan ports. The transit from South Korea to West Africa is typically
                        slightly faster due to shipping routes.
                    </p>
                </div>

            </div>
        </div>
    </section>

    {{-- CTA SECTION --}}
    <section class="border-t border-gray-100 py-16 lg:py-20">
        <div class="max-w-[90rem] mx-auto px-4 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Track your shipment live</h2>
            <p class="text-gray-500 mb-8 max-w-2xl mx-auto">Once you order, you can monitor your vehicle's progress
                directly from your customer dashboard.</p>
            <a href="{{ route('dashboard.index') }}"
                class="inline-flex items-center justify-center bg-gray-900 text-white text-[15px] font-bold py-4 px-10 rounded-lg hover:bg-gray-800 transition-all duration-200 shadow-lg shadow-gray-900/20">
                Go to Dashboard
            </a>
        </div>
    </section>


    {{-- CTA BANNER --}}
    <section class="py-20 bg-[#1a1c23] relative overflow-hidden border-t border-gray-800">
        <div
            class="absolute top-0 right-0 w-96 h-96 bg-primary/20 blur-[100px] rounded-full translate-x-1/2 -translate-y-1/2">
        </div>
        <div
            class="absolute bottom-0 left-0 w-96 h-96 bg-primary/10 blur-[100px] rounded-full -translate-x-1/2 translate-y-1/2">
        </div>

        <div class="relative z-10 max-w-4xl mx-auto px-4 lg:px-8 text-center">
            <h2 class="text-3xl md:text-5xl font-bold text-white mb-6 tracking-tight">Ready to find your perfect car?
            </h2>
            <p class="text-lg text-gray-400 mb-10 max-w-2xl mx-auto font-medium">Browse our curated inventory of
                high-quality Japanese and Korean imports, or contact our expert team for a custom order.</p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a wire:navigate href="{{ route('cars.index') }}"
                    class="w-full sm:w-auto inline-flex items-center justify-center bg-primary text-white text-[15px] font-bold py-4 px-8 rounded-lg hover:bg-red-700 transition-all duration-200 shadow-lg shadow-primary/30">
                    Browse Inventory
                </a>
                <a wire:navigate href="{{ route('contact') }}"
                    class="w-full sm:w-auto inline-flex items-center justify-center bg-white/10 text-white border border-white/20 text-[15px] font-bold py-4 px-8 rounded-lg hover:bg-white/20 transition-all duration-200">
                    Contact Support
                </a>
            </div>
        </div>
    </section>

</x-layouts.public>