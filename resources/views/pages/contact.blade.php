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
                    @foreach ([
                        ['icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'Location', 'value' => 'Accra, Ghana', 'href' => null],
                        ['icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'label' => 'Phone', 'value' => '+233 000 000 000', 'href' => 'tel:+233000000000'],
                        ['icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'label' => 'Email', 'value' => 'info@livingstonautos.com', 'href' => 'mailto:info@livingstonautos.com'],
                    ] as $contact)
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $contact['icon'] }}" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-widest text-base-content/40 mb-0.5">{{ $contact['label'] }}</p>
                                @if ($contact['href'])
                                    <a href="{{ $contact['href'] }}" class="text-[13px] text-primary font-medium hover:text-primary/80 transition-colors">{{ $contact['value'] }}</a>
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
