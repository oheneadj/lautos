<x-layouts.public title="Contact Us">

    <div class="bg-base-200 border-b border-base-300 py-10 px-4 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-[28px] font-semibold text-base-content leading-tight">Contact Us</h1>
            <p class="text-[14px] text-base-content/50 mt-1">Reach out with questions, custom requests, or just to say hello</p>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 lg:px-8 py-14">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-12">

            {{-- Info --}}
            <div class="lg:col-span-2 space-y-6">
                <div>
                    <h2 class="text-[18px] font-semibold text-base-content mb-3">Get in Touch</h2>
                    <p class="text-[13px] text-base-content/50 leading-relaxed">
                        Whether you want to order a specific car, have questions about the import process,
                        or need help tracking an existing order — we're here to help.
                    </p>
                </div>

                <div class="space-y-4">
                    @php
                        $whatsappNumber = preg_replace('/\D/', '', (string) \App\Models\Setting::get('whatsapp_number', ''));
                        $whatsappMessage = rawurlencode('Hello, I am interested in your cars.');
                    @endphp
                    @foreach (array_filter([
                        ['icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'Location', 'value' => \App\Models\Setting::get('contact_address'), 'href' => null],
                        ['icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'label' => 'Phone', 'value' => \App\Models\Setting::get('contact_phone'), 'href' => 'tel:' . preg_replace('/\D/', '', (string) \App\Models\Setting::get('contact_phone', ''))],
                        ['icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'label' => 'Email', 'value' => \App\Models\Setting::get('contact_email'), 'href' => 'mailto:' . \App\Models\Setting::get('contact_email')],
                        $whatsappNumber ? ['icon' => 'M17.498 14.382c-.301-.15-1.767-.867-2.04-.966-.273-.101-.473-.15-.673.15-.197.295-.771.964-.944 1.162-.175.195-.349.21-.646.075-.3-.15-1.263-.465-2.403-1.485-.888-.795-1.484-1.78-1.66-2.08-.173-.3-.018-.465.13-.615.134-.135.3-.345.45-.523.146-.181.194-.301.297-.496.1-.21.049-.375-.05-.524-.1-.149-.672-1.612-.922-2.206-.246-.579-.497-.5-.683-.51-.172-.008-.371-.01-.571-.01-.2 0-.522.074-.797.359-.273.3-1.045 1.02-1.045 2.475 0 1.453 1.07 2.86 1.22 3.06.149.195 2.06 3.135 5 4.275.71.255 1.265.405 1.696.52.713.18 1.36.15 1.87.09.57-.075 1.767-.72 2.016-1.41.255-.696.255-1.29.18-1.41-.074-.135-.27-.21-.57-.36z M12 2C6.477 2 2 6.477 2 12c0 1.91.531 3.7 1.453 5.225L2 22l4.95-1.418A9.954 9.954 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2zm0 18.001a7.96 7.96 0 01-4.075-1.119l-.292-.174-3.025.866.866-2.94-.19-.304A7.962 7.962 0 014 12c0-4.411 3.589-8 8-8s8 3.589 8 8-3.589 8-8 8z', 'label' => 'WhatsApp', 'value' => 'Chat with us', 'href' => "https://wa.me/{$whatsappNumber}?text={$whatsappMessage}", 'target' => '_blank'] : null,
                    ]) as $contact)
                        @continue(empty($contact['value']))
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $contact['icon'] }}" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-widest text-base-content/40 mb-0.5">{{ $contact['label'] }}</p>
                                @if ($contact['href'])
                                    <a href="{{ $contact['href'] }}" @if(($contact['target'] ?? null)) target="{{ $contact['target'] }}" rel="noopener" @endif class="text-[13px] text-primary font-medium hover:text-primary/80 transition-colors">{{ $contact['value'] }}</a>
                                @else
                                    <p class="text-[13px] text-base-content/60">{{ $contact['value'] }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="bg-base-200 border border-base-content/10 rounded-lg p-4">
                    <p class="text-[13px] text-base-content/50 leading-relaxed">
                        <span class="font-semibold text-base-content">Response time:</span>
                        We reply to all enquiries within 24 hours on business days.
                    </p>
                </div>
            </div>

            {{-- Form --}}
            <div class="lg:col-span-3 bg-base-100 border border-base-content/10 rounded-lg shadow-sm p-6">
                <h2 class="text-[18px] font-semibold text-base-content mb-5">Send a Message</h2>
                <livewire:contact.contact-form />
            </div>

        </div>
    </div>

</x-layouts.public>
