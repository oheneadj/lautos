<x-layouts.public title="About Us">

    {{-- MAIN CONTENT: SPLIT FEATURE --}}
    <section class="max-w-[90rem] mx-auto px-4 lg:px-8 py-16 lg:py-24">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-center">

            {{-- Left Side: Image with Red Box --}}
            <div class="relative">
                <div class="rounded-2xl overflow-hidden shadow-2xl relative z-10 border border-gray-100">
                    <img src="https://images.unsplash.com/photo-1619642751034-765dfdf7c58e?auto=format&fit=crop&w=1200&q=80"
                        alt="Car Inspection" class="w-full h-auto object-cover min-h-[500px]">
                </div>

                {{-- Overlapping Red Title Box --}}
                <div
                    class="absolute bottom-8 -right-4 md:-right-12 lg:-right-12 z-20 bg-[#da251d] text-white p-8 md:p-10 shadow-2xl max-w-[320px] rounded-xl transform translate-y-1/4 lg:translate-y-0">
                    <h3 class="text-2xl md:text-3xl font-bold leading-tight">
                        We Ensure Quality Vehicles For Everyone
                    </h3>
                </div>

                {{-- Decorative element --}}
                <div
                    class="absolute -top-6 -left-6 w-32 h-32 bg-gray-100 rounded-full z-0 opacity-50 mix-blend-multiply">
                </div>
            </div>

            {{-- Right Side: Text & Features --}}
            <div class="pt-12 lg:pt-0">
                <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 leading-tight mb-6">
                    We Provide The Best <span class="text-primary block">Imported Vehicles</span>
                </h1>

                <p class="text-base text-gray-600 leading-relaxed mb-6 font-medium">
                    Livingston Autos was founded to make buying a quality imported car in Ghana simple and trustworthy.
                    We source directly from auction houses and dealers in Japan and Korea, so every vehicle that reaches
                    our customers has been rigorously inspected before it ever leaves the port of origin.
                </p>

                <p class="text-base text-gray-500 leading-relaxed mb-10">
                    Japan and Korea are home to some of the world's most reliable, well-maintained used vehicles. Strict
                    inspection standards and a culture of meticulous car care mean our imports consistently outperform
                    vehicles sourced elsewhere.
                </p>

                {{-- Feature Checklist --}}
                <div class="space-y-4 mb-10">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-primary flex-shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-base font-bold text-gray-800">Direct access to overseas auction
                            houses.</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-primary flex-shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-base font-bold text-gray-800">Every vehicle undergoes strict quality
                            inspections.</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-primary flex-shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-base font-bold text-gray-800">Transparent pricing with no hidden shipping
                            fees.</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-primary flex-shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-base font-bold text-gray-800">Full customs clearance assistance
                            provided.</span>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col sm:flex-row items-center gap-6">
                    <a href="{{ route('cars.index') }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center bg-primary text-white text-base font-bold py-4 px-8 rounded-lg hover:bg-primary/90 transition-all duration-200 shadow-lg shadow-primary/30">
                        View Inventory
                    </a>

                    @if (\App\Models\Setting::get('contact_phone'))
                        <a href="tel:{{ preg_replace('/\D/', '', (string) \App\Models\Setting::get('contact_phone', '')) }}"
                            class="flex items-center gap-4 bg-gray-50 pr-6 pl-2 py-2 rounded-full border border-gray-100 hover:bg-gray-100 transition-colors">
                            <div
                                class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm text-primary flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Call Anytime</p>
                                <p class="text-base font-bold text-gray-900">{{ \App\Models\Setting::get('contact_phone') }}</p>
                            </div>
                        </a>
                    @endif
                </div>

            </div>
        </div>

    </section>

    {{-- STATS BANNER --}}
    <section class="relative z-20 px-4 lg:px-8 mb-16 lg:mb-24 mt-8">
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
    </section>

    {{-- MISSION & VISION --}}
    <section class="bg-gray-50 py-16 lg:py-24 border-t border-b border-gray-100">
        <div class="max-w-[90rem] mx-auto px-4 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12">
                {{-- Mission --}}
                <div
                    class="bg-white rounded-2xl p-8 shadow-xl shadow-gray-200/50 border border-gray-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                    <div
                        class="absolute -right-6 -top-6 text-[120px] font-black text-gray-50 group-hover:text-primary/5 transition-colors duration-300 z-0 select-none">
                        M</div>
                    <div class="relative z-10">
                        <div
                            class="w-14 h-14 bg-primary/10 text-primary rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Our Mission</h3>
                        <p class="text-base text-gray-500 leading-relaxed">
                            To demystify the vehicle importation process in Ghana by providing a transparent, reliable,
                            and cost-effective pathway for individuals and businesses to acquire high-quality vehicles
                            directly from overseas markets.
                        </p>
                    </div>
                </div>

                {{-- Vision --}}
                <div
                    class="bg-white rounded-2xl p-8 shadow-xl shadow-gray-200/50 border border-gray-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                    <div
                        class="absolute -right-6 -top-6 text-[120px] font-black text-gray-50 group-hover:text-primary/5 transition-colors duration-300 z-0 select-none">
                        V</div>
                    <div class="relative z-10">
                        <div
                            class="w-14 h-14 bg-primary/10 text-primary rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Our Vision</h3>
                        <p class="text-base text-gray-500 leading-relaxed">
                            To be the premier, most trusted automotive importer in West Africa, recognized for our
                            unwavering commitment to vehicle quality, customer satisfaction, and innovative digital
                            logistics solutions.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CORE VALUES --}}
    <section class="max-w-[90rem] mx-auto px-4 lg:px-8 py-16 lg:py-24">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">Our Core Values</h2>
            <p class="text-base text-gray-500 font-medium">The principles that guide every decision we make and every
                car we deliver.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            {{-- Value 1 --}}
            <div
                class="bg-white rounded-2xl p-8 shadow-xl shadow-gray-200/50 border border-gray-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                <div
                    class="absolute -right-6 -top-6 text-[120px] font-black text-gray-50 group-hover:text-primary/5 transition-colors duration-300 z-0 select-none">
                    1</div>
                <div class="relative z-10">
                    <div class="w-14 h-14 bg-primary/10 text-primary rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-bold text-gray-900 mb-2">Transparency</h4>
                    <p class="text-sm text-gray-500 leading-relaxed">No hidden fees, no surprise charges. What you
                        see is exactly what you pay for.</p>
                </div>
            </div>

            {{-- Value 2 --}}
            <div
                class="bg-white rounded-2xl p-8 shadow-xl shadow-gray-200/50 border border-gray-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                <div
                    class="absolute -right-6 -top-6 text-[120px] font-black text-gray-50 group-hover:text-primary/5 transition-colors duration-300 z-0 select-none">
                    2</div>
                <div class="relative z-10">
                    <div class="w-14 h-14 bg-primary/10 text-primary rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-bold text-gray-900 mb-2">Quality First</h4>
                    <p class="text-sm text-gray-500 leading-relaxed">We never compromise on condition. Every car
                        passes strict export inspections.</p>
                </div>
            </div>

            {{-- Value 3 --}}
            <div
                class="bg-white rounded-2xl p-8 shadow-xl shadow-gray-200/50 border border-gray-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                <div
                    class="absolute -right-6 -top-6 text-[120px] font-black text-gray-50 group-hover:text-primary/5 transition-colors duration-300 z-0 select-none">
                    3</div>
                <div class="relative z-10">
                    <div class="w-14 h-14 bg-primary/10 text-primary rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-bold text-gray-900 mb-2">Reliability</h4>
                    <p class="text-sm text-gray-500 leading-relaxed">We deliver on our promises, ensuring your
                        vehicle arrives safely and on time.</p>
                </div>
            </div>

            {{-- Value 4 --}}
            <div
                class="bg-white rounded-2xl p-8 shadow-xl shadow-gray-200/50 border border-gray-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                <div
                    class="absolute -right-6 -top-6 text-[120px] font-black text-gray-50 group-hover:text-primary/5 transition-colors duration-300 z-0 select-none">
                    4</div>
                <div class="relative z-10">
                    <div class="w-14 h-14 bg-primary/10 text-primary rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-bold text-gray-900 mb-2">Customer Success</h4>
                    <p class="text-sm text-gray-500 leading-relaxed">We are dedicated to providing support at every
                        stage, from browsing to driving.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- MEET THE TEAM --}}
    <section class="bg-[#1a1c23] py-16 lg:py-24">
        <div class="max-w-[90rem] mx-auto px-4 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-white mb-4">Meet The Team</h2>
                <p class="text-base text-gray-400 font-medium">The experts working behind the scenes to bring you the
                    best vehicles.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                {{-- Team Member 1 --}}
                <div class="group">
                    <div class="relative overflow-hidden rounded-2xl mb-4 bg-gray-800 aspect-[3/4]">
                        <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=600&q=80"
                            alt="Team Member"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </div>
                    <h4 class="text-lg font-bold text-white">James Livingston</h4>
                    <p class="text-sm text-primary font-medium">Founder & CEO</p>
                </div>

                {{-- Team Member 2 --}}
                <div class="group">
                    <div class="relative overflow-hidden rounded-2xl mb-4 bg-gray-800 aspect-[3/4]">
                        <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=600&q=80"
                            alt="Team Member"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </div>
                    <h4 class="text-lg font-bold text-white">Sarah Osei</h4>
                    <p class="text-sm text-primary font-medium">Head of Operations</p>
                </div>

                {{-- Team Member 3 --}}
                <div class="group">
                    <div class="relative overflow-hidden rounded-2xl mb-4 bg-gray-800 aspect-[3/4]">
                        <img src="https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=600&q=80"
                            alt="Team Member"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </div>
                    <h4 class="text-lg font-bold text-white">David Mensah</h4>
                    <p class="text-sm text-primary font-medium">Logistics Manager</p>
                </div>

                {{-- Team Member 4 --}}
                <div class="group">
                    <div class="relative overflow-hidden rounded-2xl mb-4 bg-gray-800 aspect-[3/4]">
                        <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=600&q=80"
                            alt="Team Member"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </div>
                    <h4 class="text-lg font-bold text-white">Grace Abena</h4>
                    <p class="text-sm text-primary font-medium">Customer Success Lead</p>
                </div>
            </div>
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
                    class="w-full sm:w-auto inline-flex items-center justify-center bg-primary text-white text-base font-bold py-4 px-8 rounded-lg hover:bg-red-700 transition-all duration-200 shadow-lg shadow-primary/30">
                    Browse Inventory
                </a>
                <a wire:navigate href="{{ route('contact') }}"
                    class="w-full sm:w-auto inline-flex items-center justify-center bg-white/10 text-white border border-white/20 text-base font-bold py-4 px-8 rounded-lg hover:bg-white/20 transition-all duration-200">
                    Contact Support
                </a>
            </div>
        </div>
    </section>

</x-layouts.public>