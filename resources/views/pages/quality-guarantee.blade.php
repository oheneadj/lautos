<x-layouts.public title="Vehicle Inspections & Quality Guarantee">

    {{-- STATS BANNER (Overlapping) --}}
    {{-- <div class="absolute bottom-0 left-0 right-0 translate-y-1/2 px-4 lg:px-8 z-20">
        <div class="max-w-5xl mx-auto bg-[#da251d] rounded-2xl shadow-2xl overflow-hidden">
            <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-white/20">
                <div class="text-center py-10 px-4" x-data="{ count: 0, target: 500 }"
                    x-intersect.once="let i = setInterval(() => { count += Math.ceil(target/30); if(count >= target) { count = target; clearInterval(i); } }, 30)">
                    <div class="text-4xl lg:text-5xl font-black text-white mb-2"><span x-text="count">0</span><span
                            class="text-white/80">+</span></div>
                    <div class="text-sm font-bold text-white/90 uppercase tracking-wider">Vehicles Imported</div>
                </div>
                <div class="text-center py-10 px-4" x-data="{ count: 0, target: 10 }"
                    x-intersect.once="let i = setInterval(() => { count += 1; if(count >= target) { count = target; clearInterval(i); } }, 100)">
                    <div class="text-4xl lg:text-5xl font-black text-white mb-2"><span x-text="count">0</span><span
                            class="text-white/80">+</span></div>
                    <div class="text-sm font-bold text-white/90 uppercase tracking-wider">Years Experience</div>
                </div>
                <div class="text-center py-10 px-4" x-data="{ count: 0, target: 100 }"
                    x-intersect.once="let i = setInterval(() => { count += Math.ceil(target/30); if(count >= target) { count = target; clearInterval(i); } }, 30)">
                    <div class="text-4xl lg:text-5xl font-black text-white mb-2"><span x-text="count">0</span><span
                            class="text-white/80">%</span></div>
                    <div class="text-sm font-bold text-white/90 uppercase tracking-wider">Satisfied Clients</div>
                </div>
                <div class="text-center py-10 px-4" x-data="{ count: 0, target: 150 }"
                    x-intersect.once="let i = setInterval(() => { count += Math.ceil(target/30); if(count >= target) { count = target; clearInterval(i); } }, 30)">
                    <div class="text-4xl lg:text-5xl font-black text-white mb-2"><span x-text="count">0</span><span
                            class="text-white/80">+</span></div>
                    <div class="text-sm font-bold text-white/90 uppercase tracking-wider">Dealer Reviews</div>
                </div>
            </div>
        </div>
    </div> --}}

    </section>

    {{-- OUR PROMISE (Split Section) --}}
    <section class="max-w-[90rem] mx-auto px-4 lg:px-8 py-16 lg:py-24">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-center">

            {{-- Left Side: Image with Red Box --}}
            <div class="relative">
                <div class="rounded-2xl overflow-hidden shadow-2xl relative z-10 border border-gray-100">
                    <img src="https://images.unsplash.com/photo-1517524008697-84bbe3c3fd98?auto=format&fit=crop&w=1200&q=80"
                        alt="Mechanic inspecting engine" class="w-full h-auto object-cover min-h-[500px]">
                </div>

                {{-- Overlapping Red Title Box --}}
                <div
                    class="absolute bottom-8 -right-4 md:-right-12 lg:-right-12 z-20 bg-[#da251d] text-white p-8 md:p-10 shadow-2xl max-w-[320px] rounded-xl transform translate-y-1/4 lg:translate-y-0">
                    <h3 class="text-2xl md:text-3xl font-bold leading-tight">
                        No Compromise On Quality
                    </h3>
                </div>

                {{-- Decorative element --}}
                <div
                    class="absolute -top-6 -left-6 w-32 h-32 bg-gray-100 rounded-full z-0 opacity-50 mix-blend-multiply">
                </div>
            </div>

            {{-- Right Side: Text --}}
            <div class="pt-12 lg:pt-0">
                <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 leading-tight mb-6">
                    Our Unbreakable <span class="text-primary block">Promise to You</span>
                </h2>

                <p class="text-base text-gray-600 leading-relaxed mb-6 font-medium">
                    We partner directly with established auction houses and independent mechanics in Japan and South
                    Korea. By removing middlemen, we maintain absolute control over the quality of every vehicle we
                    source.
                </p>

                <p class="text-base text-gray-500 leading-relaxed mb-10">
                    Before any car is listed for sale on our platform or shipped to a customer, it must pass our
                    rigorous export standards. If a vehicle doesn't meet our criteria, it doesn't get onto our ships.
                    Period.
                </p>

                {{-- Key Trust Indicators --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="flex items-center gap-4 bg-gray-50 rounded-xl p-4 border border-gray-100">
                        <div
                            class="w-10 h-10 bg-white text-primary rounded-full flex items-center justify-center shadow-sm flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="font-bold text-gray-900 text-sm">Direct Auction Access</p>
                    </div>
                    <div class="flex items-center gap-4 bg-gray-50 rounded-xl p-4 border border-gray-100">
                        <div
                            class="w-10 h-10 bg-white text-primary rounded-full flex items-center justify-center shadow-sm flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="font-bold text-gray-900 text-sm">Independent Mechanics</p>
                    </div>
                    <div class="flex items-center gap-4 bg-gray-50 rounded-xl p-4 border border-gray-100">
                        <div
                            class="w-10 h-10 bg-white text-primary rounded-full flex items-center justify-center shadow-sm flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="font-bold text-gray-900 text-sm">Verified Mileage</p>
                    </div>
                    <div class="flex items-center gap-4 bg-gray-50 rounded-xl p-4 border border-gray-100">
                        <div
                            class="w-10 h-10 bg-white text-primary rounded-full flex items-center justify-center shadow-sm flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="font-bold text-gray-900 text-sm">Full History Reports</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- MULTI-POINT INSPECTION GRID --}}
    <section class="bg-gray-50 py-16 lg:py-24 border-t border-gray-100">
        <div class="max-w-[90rem] mx-auto px-4 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">Multi-Point Inspection</h2>
                <p class="text-base text-gray-500 font-medium">Every vehicle undergoes a comprehensive point-by-point
                    inspection verifying these crucial areas.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

                {{-- Card 1 --}}
                <div
                    class="bg-white p-8 rounded-2xl shadow-xl shadow-gray-200/40 border border-gray-100 hover:-translate-y-1 transition-transform duration-300">
                    <div class="w-14 h-14 bg-primary/10 text-primary rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Engine & Transmission</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        We verify the health of the powertrain, checking for leaks, unusual noises, smooth shifting, and
                        testing the overall performance of the engine under load.
                    </p>
                </div>

                {{-- Card 2 --}}
                <div
                    class="bg-white p-8 rounded-2xl shadow-xl shadow-gray-200/40 border border-gray-100 hover:-translate-y-1 transition-transform duration-300">
                    <div class="w-14 h-14 bg-gray-900/5 text-gray-900 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Chassis Integrity</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        We inspect the frame and undercarriage to ensure there is no hidden rust, flood damage, or
                        structural repairs from previous undisclosed accidents.
                    </p>
                </div>

                {{-- Card 3 --}}
                <div
                    class="bg-white p-8 rounded-2xl shadow-xl shadow-gray-200/40 border border-gray-100 hover:-translate-y-1 transition-transform duration-300">
                    <div class="w-14 h-14 bg-primary/10 text-primary rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Interior Quality</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        The cabin is checked for excessive wear and tear on seats and panels, as well as confirming the
                        absence of smoke or mold odors.
                    </p>
                </div>

                {{-- Card 4 --}}
                <div
                    class="bg-white p-8 rounded-2xl shadow-xl shadow-gray-200/40 border border-gray-100 hover:-translate-y-1 transition-transform duration-300">
                    <div class="w-14 h-14 bg-gray-900/5 text-gray-900 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Electrical Systems</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        We test all electrical components, from air conditioning and infotainment systems to modern
                        driver-assist sensors and warning lights.
                    </p>
                </div>

            </div>
        </div>
    </section>


</x-layouts.public>