<x-layouts.public title="Contact Us">

    <div class="bg-base-200 py-10 px-4 lg:px-8 border-b border-base-300">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl font-bold">Contact Us</h1>
            <p class="text-base-content/60 mt-1">Reach out with questions, custom requests, or just to say hello</p>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 lg:px-8 py-14">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-12">

            {{-- Info --}}
            <div class="lg:col-span-2 space-y-6">
                <div>
                    <h2 class="text-lg font-semibold mb-4">Get in Touch</h2>
                    <p class="text-base-content/60 text-sm leading-relaxed">
                        Whether you want to order a specific car, have questions about the import process,
                        or need help tracking an existing order — we're here to help.
                    </p>
                </div>

                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <div class="font-medium text-sm">Location</div>
                            <div class="text-sm text-base-content/60">Accra, Ghana</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <div>
                            <div class="font-medium text-sm">Phone</div>
                            <a href="tel:+233000000000" class="text-sm text-primary hover:underline">+233 000 000 000</a>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <div class="font-medium text-sm">Email</div>
                            <a href="mailto:info@livingstonautos.com" class="text-sm text-primary hover:underline">info@livingstonautos.com</a>
                        </div>
                    </div>
                </div>

                <div class="bg-base-200 rounded-xl p-4 text-sm text-base-content/60">
                    <strong class="text-base-content">Response time:</strong> We reply to all enquiries within 24 hours on business days.
                </div>
            </div>

            {{-- Form --}}
            <div class="lg:col-span-3 bg-base-100 border border-base-200 rounded-2xl p-6 shadow-sm">
                <h2 class="text-lg font-semibold mb-5">Send a Message</h2>
                <livewire:contact.contact-form />
            </div>

        </div>
    </div>

</x-layouts.public>
