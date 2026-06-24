<x-layouts.public title="Fraud Awareness">

    {{-- HERO SECTION --}}
    <section class="relative min-h-[50vh] flex items-center bg-[#1a1c23] overflow-visible">
        <div class="absolute top-0 right-0 w-full lg:w-[60%] h-full z-0">
            <img src="https://images.unsplash.com/photo-1556761175-5973dc0f32b7?auto=format&fit=crop&w=2000&q=80"
                alt="Fraud Awareness" class="w-full h-full object-cover opacity-30 lg:opacity-60">
            <div class="absolute inset-0 bg-gradient-to-r from-[#1a1c23] via-[#1a1c23]/90 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-[#1a1c23] via-transparent to-transparent lg:hidden"></div>
        </div>

        <div class="relative z-10 max-w-[90rem] mx-auto px-4 lg:px-8 w-full py-20">
            <div class="max-w-2xl">
                <span class="inline-block px-3 py-1 mb-6 rounded-full bg-primary/20 text-primary text-xs font-bold uppercase tracking-widest border border-primary/20">Security Alert</span>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 leading-tight tracking-tight">
                    Fraud Awareness <br><span class="text-primary">Protect Yourself</span>
                </h1>
                <p class="text-lg text-white/70 font-medium">
                    Important information to help you identify and avoid fraudulent sellers and scams.
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
                        <a href="{{ route('pages.terms') }}" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-xl font-medium text-sm transition-colors">
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
                        <a href="{{ route('pages.fraud-awareness') }}" class="flex items-center gap-3 px-4 py-3 bg-primary/10 text-primary rounded-xl font-bold text-sm transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            Fraud Awareness
                        </a>
                    </nav>
                </div>
            </aside>

            {{-- Main Document Area --}}
            <div class="flex-1">
                <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 p-8 md:p-12 lg:p-16 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-bl-[100px] -z-0"></div>
                    
                    <div class="prose prose-lg prose-gray max-w-none relative z-10">
                        
                        <p class="text-sm text-gray-400 font-bold tracking-widest uppercase mb-8">Important Security Notice</p>

                        <div class="mb-12">
                            <p class="text-base text-gray-900 font-bold leading-relaxed mb-4 p-4 bg-gray-50 rounded-xl border border-gray-100">
                                Livingston Autos will NEVER ask you to make payments to personal Mobile Money numbers or unofficial bank accounts.
                            </p>
                            <p class="text-base text-gray-600 leading-relaxed mb-4">
                                All official payments must be made strictly to the company bank accounts listed on the <a href="{{ route('pages.payment-info') }}" class="text-primary hover:underline font-bold">Payment Information</a> page or on the official invoice generated directly from your secure customer dashboard.
                            </p>
                        </div>

                        <div class="mb-12">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100">How to spot a scam</h2>
                            <ul class="space-y-3 mt-4 text-base text-gray-600 font-medium">
                                <li class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    They ask you to rush a payment via WhatsApp.
                                </li>
                                <li class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    They use personal bank accounts rather than corporate accounts.
                                </li>
                                <li class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    They contact you from phone numbers not listed on our official Contact page.
                                </li>
                            </ul>
                            <p class="text-base text-gray-600 leading-relaxed mt-6">
                                If you are unsure about a communication you have received, please contact our official support lines immediately.
                            </p>
                        </div>

                    </div>
                </div>

                {{-- Support Box --}}
                <div class="mt-8 bg-white rounded-2xl p-8 shadow-xl shadow-gray-200/50 border border-gray-100 relative overflow-hidden flex flex-col sm:flex-row items-center justify-between gap-6 group hover:-translate-y-1 transition-transform duration-300">
                    <div>
                        <h4 class="text-lg font-bold text-gray-900 mb-1">Report Suspicious Activity</h4>
                        <p class="text-sm text-gray-600 font-medium">Please contact us immediately if you suspect fraud.</p>
                    </div>
                    <a href="{{ route('contact') }}" class="shrink-0 inline-flex items-center justify-center bg-gray-900 text-white text-sm font-bold py-3 px-6 rounded-lg hover:bg-gray-800 transition-all duration-200">
                        Contact Us
                    </a>
                </div>

            </div>

        </div>
    </section>

</x-layouts.public>
