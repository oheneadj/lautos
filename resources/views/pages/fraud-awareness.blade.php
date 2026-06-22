<x-layouts.public title="Fraud Awareness">
    <div class="bg-error/10 py-12 md:py-20 border-b border-error/20">
        <div class="max-w-4xl mx-auto px-4 lg:px-8 text-center">
            <h1 class="text-3xl md:text-5xl font-black text-error tracking-tight mb-4 flex items-center justify-center gap-3">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" /></svg>
                Fraud Awareness
            </h1>
            <p class="text-[16px] text-error/80 max-w-2xl mx-auto font-medium">Protect yourself from online scammers and impersonators.</p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 lg:px-8 py-12 md:py-20 prose prose-base max-w-none">
        <h2>Important Security Notice</h2>
        <p>Livingston Autos will <strong>NEVER</strong> ask you to make payments to personal Mobile Money numbers or unofficial bank accounts.</p>
        <p>All official payments must be made strictly to the company bank accounts listed on the <a href="{{ route('pages.payment-info') }}">Payment Information</a> page or on the invoice generated from your customer dashboard.</p>
        <h3>How to spot a scam</h3>
        <ul>
            <li>They ask you to rush a payment via WhatsApp.</li>
            <li>They use personal bank accounts rather than corporate accounts.</li>
            <li>They contact you from phone numbers not listed on our official Contact page.</li>
        </ul>
        <p>If you are unsure about a communication you have received, please contact our official support lines immediately.</p>
    </div>
</x-layouts.public>
