<x-layouts.public title="Payment Information">

    @php
        $bankName = \App\Models\Setting::get('bank_name');
        $accountName = \App\Models\Setting::get('bank_account_name');
        $accountNumber = \App\Models\Setting::get('bank_account_number');
        $momoNumber = \App\Models\Setting::get('momo_number');
        $momoName = \App\Models\Setting::get('momo_name');
        $demurrageWarning = \App\Models\Setting::get('demurrage_warning');
    @endphp

        {{-- HERO SECTION --}}
    <section class="relative min-h-[50vh] flex items-center bg-[#1a1c23] overflow-visible">
        <div class="absolute top-0 right-0 w-full lg:w-[60%] h-full z-0">
            <img src="https://images.unsplash.com/photo-1601597111158-2fceff292cdc?auto=format&fit=crop&w=2000&q=80"
                alt="Payment Information" class="w-full h-full object-cover opacity-30 lg:opacity-60">
            <div class="absolute inset-0 bg-gradient-to-r from-[#1a1c23] via-[#1a1c23]/90 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-[#1a1c23] via-transparent to-transparent lg:hidden"></div>
        </div>

        <div class="relative z-10 max-w-[90rem] mx-auto px-4 lg:px-8 w-full py-20">
            <div class="max-w-2xl">
                <span class="inline-block px-3 py-1 mb-6 rounded-full bg-primary/20 text-primary text-[12px] font-bold uppercase tracking-widest border border-primary/20">Import Process</span>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 leading-tight tracking-tight">
                    Payment Information <br><span class="text-primary">Secure Offline Transactions</span>
                </h1>
                <p class="text-lg text-white/70 font-medium">
                    Step-by-step instructions for safely completing your vehicle payment.
                </p>
            </div>
        </div>
        
    </section>

    {{-- WARNING SECTION --}}
    @if ($demurrageWarning)
    <section class="max-w-[90rem] mx-auto px-4 lg:px-8 mt-16 mb-8">
        <div class="bg-red-50 border-l-4 border-[#da251d] rounded-r-xl p-6 shadow-md flex gap-4 items-start max-w-4xl mx-auto">
            <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-[#da251d] shadow-sm flex-shrink-0 mt-1">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Important Notice</h3>
                <p class="text-[15px] text-gray-700 leading-relaxed font-medium">{{ $demurrageWarning }}</p>
            </div>
        </div>
    </section>
    @endif

    {{-- PAYMENT DETAILS CARDS --}}
    <section class="max-w-[90rem] mx-auto px-4 lg:px-8 py-12">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">Official Payment Details</h2>
            <p class="text-[16px] text-gray-500 font-medium">Please only send funds to the verified accounts listed below to avoid fraud.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">
            
            {{-- Bank Transfer Card --}}
            <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden relative group hover:-translate-y-1 transition-transform duration-300">
                <div class="h-2 w-full bg-gray-900 absolute top-0 left-0"></div>
                <div class="p-8 md:p-10">
                    <div class="flex items-center gap-4 mb-8 pb-8 border-b border-gray-100">
                        <div class="w-14 h-14 bg-gray-50 rounded-xl flex items-center justify-center border border-gray-100">
                            <svg class="w-7 h-7 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">Bank Transfer</h3>
                            <p class="text-[13px] text-gray-500 font-medium uppercase tracking-wider">Direct Deposit</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <p class="text-[12px] text-gray-400 uppercase tracking-widest font-bold mb-1">Bank Name</p>
                            <p class="text-lg font-bold text-gray-900">{{ $bankName ?: 'Not yet configured' }}</p>
                        </div>
                        <div>
                            <p class="text-[12px] text-gray-400 uppercase tracking-widest font-bold mb-1">Account Name</p>
                            <p class="text-lg font-bold text-gray-900">{{ $accountName ?: 'Not yet configured' }}</p>
                        </div>
                        <div>
                            <p class="text-[12px] text-gray-400 uppercase tracking-widest font-bold mb-1">Account Number</p>
                            <p class="text-2xl font-black text-primary tracking-wider">{{ $accountNumber ?: 'Not yet configured' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mobile Money Card --}}
            <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden relative group hover:-translate-y-1 transition-transform duration-300">
                <div class="h-2 w-full bg-primary absolute top-0 left-0"></div>
                <div class="p-8 md:p-10">
                    <div class="flex items-center gap-4 mb-8 pb-8 border-b border-gray-100">
                        <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center border border-primary/20">
                            <svg class="w-7 h-7 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">Mobile Money</h3>
                            <p class="text-[13px] text-gray-500 font-medium uppercase tracking-wider">Fast Transfer</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <p class="text-[12px] text-gray-400 uppercase tracking-widest font-bold mb-1">Registered Name</p>
                            <p class="text-lg font-bold text-gray-900">{{ $momoName ?: 'Not yet configured' }}</p>
                        </div>
                        <div>
                            <p class="text-[12px] text-gray-400 uppercase tracking-widest font-bold mb-1">MoMo Number</p>
                            <p class="text-2xl font-black text-primary tracking-wider">{{ $momoNumber ?: 'Not yet configured' }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- HOW TO PAY PROCESS --}}
    <section class="bg-gray-50 py-16 lg:py-24 border-t border-gray-100 mt-12">
        <div class="max-w-[90rem] mx-auto px-4 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">How To Make A Payment</h2>
                <p class="text-[16px] text-gray-500 font-medium">A simple, four-step process to secure your vehicle.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                
                {{-- Step 1 --}}
                <div class="bg-white p-8 rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 relative group hover:-translate-y-1 transition-transform duration-300">
                    <div class="absolute -top-4 -left-4 w-10 h-10 bg-gray-900 text-white rounded-full flex items-center justify-center font-bold text-lg shadow-md">1</div>
                    <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center mb-4 text-gray-500">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <h4 class="text-lg font-bold text-gray-900 mb-2">Place Order</h4>
                    <p class="text-[14px] text-gray-500 leading-relaxed">Place your order from the car's detail page to securely reserve your vehicle.</p>
                </div>

                {{-- Step 2 --}}
                <div class="bg-white p-8 rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 relative group hover:-translate-y-1 transition-transform duration-300">
                    <div class="absolute -top-4 -left-4 w-10 h-10 bg-gray-900 text-white rounded-full flex items-center justify-center font-bold text-lg shadow-md">2</div>
                    <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center mb-4 text-gray-500">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <h4 class="text-lg font-bold text-gray-900 mb-2">Transfer Funds</h4>
                    <p class="text-[14px] text-gray-500 leading-relaxed">Transfer the exact amount shown on your invoice to our verified accounts.</p>
                </div>

                {{-- Step 3 --}}
                <div class="bg-white p-8 rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 relative group hover:-translate-y-1 transition-transform duration-300">
                    <div class="absolute -top-4 -left-4 w-10 h-10 bg-gray-900 text-white rounded-full flex items-center justify-center font-bold text-lg shadow-md">3</div>
                    <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center mb-4 text-gray-500">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    </div>
                    <h4 class="text-lg font-bold text-gray-900 mb-2">Upload Proof</h4>
                    <p class="text-[14px] text-gray-500 leading-relaxed">Log into your dashboard and upload a screenshot or photo of your payment receipt.</p>
                </div>

                {{-- Step 4 --}}
                <div class="bg-white p-8 rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 relative group hover:-translate-y-1 transition-transform duration-300">
                    <div class="absolute -top-4 -left-4 w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center font-bold text-lg shadow-md">4</div>
                    <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center mb-4 text-primary shadow-sm border border-primary/10">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h4 class="text-lg font-bold text-gray-900 mb-2">Verification</h4>
                    <p class="text-[14px] text-gray-600 leading-relaxed">We manually verify the transaction and update your order status to initiate shipping.</p>
                </div>

            </div>
        </div>
    </section>

</x-layouts.public>
