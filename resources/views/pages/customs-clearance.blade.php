<x-layouts.public title="Customs Clearance Guide">

    {{-- CLEARANCE OPTIONS (Split Section) --}}
    <section class="max-w-[90rem] mx-auto px-4 lg:px-8 py-16 lg:py-24">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-center">

            {{-- Left Side: Image with Red Box --}}
            <div class="relative">
                <div class="rounded-2xl overflow-hidden shadow-2xl relative z-10 border border-gray-100">
                    <img src="https://images.unsplash.com/photo-1563013544-824ae1b704d3?auto=format&fit=crop&w=1200&q=80"
                        alt="Clearing Agent at work" class="w-full h-auto object-cover min-h-[500px]">
                </div>

                {{-- Overlapping Red Title Box --}}
                <div
                    class="absolute bottom-8 -right-4 md:-right-12 lg:-right-12 z-20 bg-[#da251d] text-white p-8 md:p-10 shadow-2xl max-w-[320px] rounded-xl transform translate-y-1/4 lg:translate-y-0">
                    <h3 class="text-2xl md:text-3xl font-bold leading-tight">
                        Hassle-Free Clearance Options
                    </h3>
                </div>

                {{-- Decorative element --}}
                <div
                    class="absolute -top-6 -left-6 w-32 h-32 bg-gray-100 rounded-full z-0 opacity-50 mix-blend-multiply">
                </div>
            </div>

            {{-- Right Side: Text & Options --}}
            <div class="pt-12 lg:pt-0">
                <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 leading-tight mb-6">
                    Clearing Your Vehicle <span class="text-primary block">at Tema Port</span>
                </h2>

                <p class="text-base text-gray-600 leading-relaxed mb-8 font-medium">
                    When your vehicle arrives in Ghana, it must undergo customs clearance. We offer two distinct paths
                    to ensure you receive your car smoothly.
                </p>

                <div class="space-y-6">
                    {{-- Option 1 --}}
                    <div
                        class="bg-white rounded-2xl p-8 shadow-xl shadow-gray-200/50 border border-gray-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                        <div class="flex items-center gap-4 mb-3">
                            <div
                                class="w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center font-black text-xl shadow-md">
                                1</div>
                            <h3 class="text-xl font-bold text-gray-900">Self-Clearance</h3>
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed pl-14">
                            You or your designated clearing agent take full responsibility for the vehicle once it
                            docks. We will provide you with all necessary original shipping documents (Bill of Lading,
                            Export Certificate, Invoice) to facilitate the process.
                        </p>
                    </div>

                    {{-- Option 2 --}}
                    <div
                        class="bg-white rounded-2xl p-8 shadow-xl shadow-gray-200/50 border border-gray-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                        <div
                            class="absolute right-0 top-0 w-32 h-32 bg-primary/5 rounded-bl-full -z-10 transition-transform group-hover:scale-110 duration-300">
                        </div>
                        <div class="flex items-center gap-4 mb-3">
                            <div
                                class="w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center font-black text-xl shadow-md">
                                2</div>
                            <h3 class="text-xl font-bold text-gray-900">Managed Doorstep Delivery</h3>
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed pl-14">
                            Let our experienced logistics team handle everything. We will calculate the duties, process
                            the paperwork with Ghana Customs, clear the vehicle, and deliver it directly to your
                            doorstep.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- IMPORTANT FACTORS GRID --}}
    <section class="bg-gray-50 py-16 lg:py-24 border-t border-gray-100">
        <div class="max-w-[90rem] mx-auto px-4 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">Important Clearance Factors</h2>
                <p class="text-base text-gray-500 font-medium">Crucial information to ensure a smooth clearing process
                    without unnecessary fees.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                {{-- Factor 1 --}}
                <div
                    class="bg-white p-8 rounded-2xl shadow-xl shadow-gray-200/40 border border-gray-100 hover:-translate-y-1 transition-transform duration-300">
                    <div class="w-14 h-14 bg-primary/10 text-primary rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Duties Calculation</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Customs duties in Ghana are not fixed. They depend entirely on the vehicle's make, model, year
                        of manufacture, and engine capacity. Older vehicles typically attract higher overage penalties.
                    </p>
                </div>

                {{-- Factor 2 --}}
                <div
                    class="bg-white p-8 rounded-2xl shadow-xl shadow-gray-200/40 border border-gray-100 hover:-translate-y-1 transition-transform duration-300">
                    <div class="w-14 h-14 bg-gray-900/5 text-gray-900 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Certified Agents</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        If you choose to self-clear, always use a GRA-certified clearing agent. Unverified agents can
                        cause severe delays, document mishandling, and unexpected expenses.
                    </p>
                </div>

                {{-- Factor 3 --}}
                <div
                    class="bg-white p-8 rounded-2xl shadow-xl shadow-gray-200/40 border border-gray-100 hover:-translate-y-1 transition-transform duration-300">
                    <div class="w-14 h-14 bg-red-100 text-red-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Demurrage Warning</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Once your vehicle is discharged at Tema port, you have a limited number of rent-free days.
                        Delays in providing documentation or paying duties will result in daily demurrage (storage)
                        fees.
                    </p>
                </div>

            </div>
        </div>
    </section>

</x-layouts.public>