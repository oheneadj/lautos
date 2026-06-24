<x-layouts.public title="Frequently Asked Questions">

        {{-- HERO SECTION --}}
    <section class="relative min-h-[50vh] flex items-center bg-[#1a1c23] overflow-visible">
        <div class="absolute top-0 right-0 w-full lg:w-[60%] h-full z-0">
            <img src="https://images.unsplash.com/photo-1521791136064-7986c2920216?auto=format&fit=crop&w=2000&q=80"
                alt="Frequently Asked Questions" class="w-full h-full object-cover opacity-30 lg:opacity-60">
            <div class="absolute inset-0 bg-gradient-to-r from-[#1a1c23] via-[#1a1c23]/90 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-[#1a1c23] via-transparent to-transparent lg:hidden"></div>
        </div>

        <div class="relative z-10 max-w-[90rem] mx-auto px-4 lg:px-8 w-full py-20">
            <div class="max-w-2xl">
                <span class="inline-block px-3 py-1 mb-6 rounded-full bg-primary/20 text-primary text-[12px] font-bold uppercase tracking-widest border border-primary/20">Support & Guides</span>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 leading-tight tracking-tight">
                    Frequently Asked Questions <br><span class="text-primary">We're Here to Help</span>
                </h1>
                <p class="text-lg text-white/70 font-medium">
                    Find quick answers to common questions about importing your car to Ghana.
                </p>
            </div>
        </div>
        
    </section>

    {{-- FAQs ACCORDION SECTION --}}
    <section class="max-w-[90rem] mx-auto px-4 lg:px-8 py-16 lg:py-24">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-24">
            
            {{-- Left Side: Context --}}
            <div class="lg:col-span-4">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 leading-tight mb-6">
                    Got Questions? <br><span class="text-primary">We have answers.</span>
                </h2>
                <p class="text-[16px] text-gray-600 leading-relaxed mb-8 font-medium">
                    Importing a vehicle from overseas is a major decision. We've compiled a list of our most frequently asked questions to help you understand the process, costs, and guarantees.
                </p>
                
                {{-- Support Box --}}
                <div class="bg-gray-900 rounded-2xl p-8 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-primary/20 blur-2xl rounded-full translate-x-1/3 -translate-y-1/3"></div>
                    <div class="relative z-10">
                        <div class="w-12 h-12 bg-white/10 text-white rounded-xl flex items-center justify-center mb-6 border border-white/10">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Still have questions?</h3>
                        <p class="text-[14px] text-gray-400 leading-relaxed mb-8">
                            Can't find the answer you're looking for? Chat with our expert team today.
                        </p>
                        <a href="{{ route('contact') }}" class="inline-flex items-center justify-center bg-primary text-white text-[14px] font-bold py-3.5 px-6 rounded-lg hover:bg-red-700 transition-all duration-200 w-full shadow-lg shadow-primary/20">
                            Contact Support
                        </a>
                    </div>
                </div>
            </div>

            {{-- Right Side: Accordions --}}
            <div class="lg:col-span-8 space-y-4">
                @php $faqs = \App\Models\Faq::ordered()->get(); @endphp
                @foreach ($faqs as $faq)
                    <details class="group bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 [&_summary::-webkit-details-marker]:hidden" @if ($loop->first) open @endif>
                        <summary class="flex items-center justify-between cursor-pointer p-6 md:p-8">
                            <h3 class="text-lg font-bold text-gray-900">{{ $faq->question }}</h3>
                            <span class="relative shrink-0 ml-1.5 w-6 h-6 text-gray-500">
                                <svg class="absolute inset-0 w-6 h-6 opacity-100 group-open:opacity-0 transition-opacity duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                <svg class="absolute inset-0 w-6 h-6 opacity-0 group-open:opacity-100 transition-opacity duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 12H6"/></svg>
                            </span>
                        </summary>
                        <div class="px-6 md:px-8 pb-6 md:pb-8">
                            <p class="text-[15px] text-gray-600 leading-relaxed">
                                {{ $faq->answer }}
                            </p>
                        </div>
                    </details>
                @endforeach
            </div>

        </div>
    </section>

</x-layouts.public>
