<x-layouts.public title="Payment Information">

    @php
        $bankName = \App\Models\Setting::get('bank_name');
        $accountName = \App\Models\Setting::get('bank_account_name');
        $accountNumber = \App\Models\Setting::get('bank_account_number');
        $momoNumber = \App\Models\Setting::get('momo_number');
        $momoName = \App\Models\Setting::get('momo_name');
        $demurrageWarning = \App\Models\Setting::get('demurrage_warning');
    @endphp

    <div class="bg-base-200 border-b border-base-300 py-10 px-4 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-[28px] font-semibold text-base-content leading-tight">Payment Information</h1>
            <p class="text-[14px] text-base-content/50 mt-1">How to pay for your car once your order is placed</p>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 lg:px-8 py-14 space-y-8">

        {{-- Bank & MoMo Details --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-base-100 border border-base-content/10 rounded-lg p-6">
                <h2 class="text-[15px] font-semibold text-base-content mb-4">Bank Transfer</h2>
                <dl class="space-y-3 text-[13px]">
                    <div>
                        <dt class="text-base-content/40 uppercase tracking-widest text-[11px] font-bold mb-0.5">Bank Name</dt>
                        <dd class="text-base-content font-medium">{{ $bankName ?: 'Not yet configured' }}</dd>
                    </div>
                    <div>
                        <dt class="text-base-content/40 uppercase tracking-widest text-[11px] font-bold mb-0.5">Account Name</dt>
                        <dd class="text-base-content font-medium">{{ $accountName ?: 'Not yet configured' }}</dd>
                    </div>
                    <div>
                        <dt class="text-base-content/40 uppercase tracking-widest text-[11px] font-bold mb-0.5">Account Number</dt>
                        <dd class="text-base-content font-medium">{{ $accountNumber ?: 'Not yet configured' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-base-100 border border-base-content/10 rounded-lg p-6">
                <h2 class="text-[15px] font-semibold text-base-content mb-4">Mobile Money</h2>
                <dl class="space-y-3 text-[13px]">
                    <div>
                        <dt class="text-base-content/40 uppercase tracking-widest text-[11px] font-bold mb-0.5">MoMo Number</dt>
                        <dd class="text-base-content font-medium">{{ $momoNumber ?: 'Not yet configured' }}</dd>
                    </div>
                    <div>
                        <dt class="text-base-content/40 uppercase tracking-widest text-[11px] font-bold mb-0.5">MoMo Name</dt>
                        <dd class="text-base-content font-medium">{{ $momoName ?: 'Not yet configured' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Step-by-step instructions --}}
        <div>
            <h2 class="text-[18px] font-semibold text-base-content mb-4">How to Pay</h2>
            <ol class="space-y-3 text-[14px] text-base-content/60 leading-relaxed list-decimal list-inside">
                <li>Place your order from the car's detail page — this reserves your selection.</li>
                <li>Transfer the full amount shown on your order to the bank account or MoMo number above.</li>
                <li>Upload your payment proof (receipt or screenshot) from your dashboard.</li>
                <li>We confirm your payment and your order moves to the next stage — purchase and shipping.</li>
            </ol>
        </div>

        {{-- Demurrage warning --}}
        @if ($demurrageWarning)
            <div class="bg-warning/10 border border-warning/20 rounded-lg p-4 flex gap-3">
                <svg class="w-5 h-5 text-warning shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p class="text-[13px] text-base-content/70 leading-relaxed">{{ $demurrageWarning }}</p>
            </div>
        @endif

    </div>

</x-layouts.public>
