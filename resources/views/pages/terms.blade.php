<x-layouts.public title="Terms & Conditions">

        {{-- HERO SECTION --}}
    <section class="relative min-h-[50vh] flex items-center bg-[#1a1c23] overflow-visible">
        <div class="absolute top-0 right-0 w-full lg:w-[60%] h-full z-0">
            <img src="https://images.unsplash.com/photo-1450101499163-c8848c66ca85?auto=format&fit=crop&w=2000&q=80"
                alt="Terms & Conditions" class="w-full h-full object-cover opacity-30 lg:opacity-60">
            <div class="absolute inset-0 bg-gradient-to-r from-[#1a1c23] via-[#1a1c23]/90 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-[#1a1c23] via-transparent to-transparent lg:hidden"></div>
        </div>

        <div class="relative z-10 max-w-[90rem] mx-auto px-4 lg:px-8 w-full py-20">
            <div class="max-w-2xl">
                <span class="inline-block px-3 py-1 mb-6 rounded-full bg-primary/20 text-primary text-xs font-bold uppercase tracking-widest border border-primary/20">Legal Hub</span>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 leading-tight tracking-tight">
                    Terms & Conditions <br><span class="text-primary">Clear & Transparent</span>
                </h1>
                <p class="text-lg text-white/70 font-medium">
                    Please read these terms carefully before using our services or purchasing a vehicle.
                </p>
            </div>
        </div>
        
    </section>

    {{-- LEGAL DOCUMENT LAYOUT --}}
    <section class="max-w-[90rem] mx-auto px-4 lg:px-8 py-16 lg:py-24">
        <div class="flex flex-col lg:flex-row gap-12 lg:gap-16">
            
            {{-- Sidebar Navigation --}}
            <aside class="w-full lg:w-72 flex-shrink-0">
                <div class="sticky top-24 bg-white rounded-2xl p-8 shadow-xl shadow-gray-200/50 border border-gray-100 relative overflow-hidden">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Legal Hub</h3>
                    <nav class="space-y-2">
                        <a href="{{ route('pages.terms') }}" class="flex items-center gap-3 px-4 py-3 bg-primary/10 text-primary rounded-xl font-bold text-sm transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            Terms & Conditions
                        </a>
                        <a href="{{ route('pages.privacy') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-xl font-medium text-sm transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                            Privacy Policy
                        </a>
                        <a href="{{ route('pages.refund-policy') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-xl font-medium text-sm transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                            Refund Policy
                        </a>
                        <a href="{{ route('pages.fraud-awareness') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-xl font-medium text-sm transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            Fraud Awareness
                        </a>
                    </nav>
                </div>
            </aside>

            {{-- Main Document Area --}}
            <div class="flex-1">
                <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 p-8 md:p-12 lg:p-16 relative overflow-hidden">
                    <div class="prose prose-lg prose-gray max-w-none">
                        
                        <p class="text-sm text-gray-400 font-bold tracking-widest uppercase mb-8">Last Updated: January 1, 2024</p>

                        <div class="mb-12">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100">1. Agreement to Terms</h2>
                            <p class="text-base text-gray-600 leading-relaxed mb-4">
                                By accessing or using the Livingston Autos website and purchasing vehicles through our platform, you agree to be bound by these Terms and Conditions. If you do not agree with any part of these terms, you must not use our services.
                            </p>
                            <p class="text-base text-gray-600 leading-relaxed">
                                These terms apply to all visitors, users, and others who wish to access or use our vehicle import services.
                            </p>
                        </div>

                        <div class="mb-12">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100">2. Vehicle Representation</h2>
                            <p class="text-base text-gray-600 leading-relaxed mb-4">
                                We make every effort to display the colors, features, specifications, and details of the vehicles available on the Site as accurately as possible. 
                            </p>
                            <p class="text-base text-gray-600 leading-relaxed">
                                However, we do not guarantee that the colors, features, specifications, and details of the vehicles will be accurate, complete, reliable, current, or free of other errors. Your electronic display may not accurately reflect the actual colors and details of the vehicles. All vehicles are subject to availability.
                            </p>
                        </div>

                    </div>
                </div>

                {{-- Support Box --}}
                <div class="mt-8 bg-white rounded-2xl p-8 shadow-xl shadow-gray-200/50 border border-gray-100 relative overflow-hidden flex flex-col sm:flex-row items-center justify-between gap-6">
                    <div>
                        <h4 class="text-lg font-bold text-gray-900 mb-1">Questions about our terms?</h4>
                        <p class="text-sm text-gray-600 font-medium">Our support team is happy to clarify any legal or policy questions.</p>
                    </div>
                    <a href="{{ route('contact') }}" class="shrink-0 inline-flex items-center justify-center bg-gray-900 text-white text-sm font-bold py-3 px-6 rounded-lg hover:bg-gray-800 transition-all duration-200">
                        Contact Support
                    </a>
                </div>

            </div>

        </div>
    </section>

</x-layouts.public>
