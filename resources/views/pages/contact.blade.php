<x-layouts.public title="Contact Us">

    @php
        $whatsappNumber = preg_replace('/\D/', '', (string) \App\Models\Setting::get('whatsapp_number', ''));
        $whatsappMessage = rawurlencode('Hello, I am interested in your cars.');
        $facebook = \App\Models\Setting::get('facebook_url', '#');
        $instagram = \App\Models\Setting::get('instagram_url', '#');
        $twitter = \App\Models\Setting::get('twitter_url', '#');
    @endphp

    {{-- MAIN CONTACT CONTENT --}}
    <section class="bg-gray-50 py-16 lg:py-24">
        <div class="max-w-[90rem] mx-auto px-4 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16">

            {{-- LEFT SIDE: Info Cards --}}
            <div class="lg:col-span-5 space-y-8">

                <div>
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 leading-tight mb-4">
                        Get in Touch
                    </h1>
                    <p class="text-base text-gray-600 leading-relaxed font-medium">
                        Whether you want to order a specific car, have questions about the import process, or need help
                        tracking an existing order — we're here to help.
                    </p>
                </div>

                {{-- Contact Cards --}}
                <div class="space-y-4">
                    {{-- Headquarters --}}
                    @if(\App\Models\Setting::get('contact_address'))
                        <div
                            class="bg-white rounded-2xl p-8 shadow-xl shadow-gray-200/50 border border-gray-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300 flex items-center gap-6">
                            <div
                                class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0 group-hover:bg-primary group-hover:text-white transition-colors duration-300">
                                <svg class="w-5 h-5 text-primary group-hover:text-white transition-colors duration-300"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1">Headquarters
                                </p>
                                <p class="text-base font-bold text-gray-900">
                                    {{ \App\Models\Setting::get('contact_address') }}
                                </p>
                            </div>
                        </div>
                    @endif

                    {{-- Direct Line --}}
                    @if(\App\Models\Setting::get('contact_phone'))
                        <div
                            class="bg-white rounded-2xl p-8 shadow-xl shadow-gray-200/50 border border-gray-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300 flex items-center gap-6">
                            <div
                                class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0 group-hover:bg-primary group-hover:text-white transition-colors duration-300">
                                <svg class="w-5 h-5 text-primary group-hover:text-white transition-colors duration-300"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1">Direct Line
                                </p>
                                <a href="tel:{{ preg_replace('/\D/', '', (string) \App\Models\Setting::get('contact_phone', '')) }}"
                                    class="text-base font-bold text-gray-900 hover:text-primary transition-colors">
                                    {{ \App\Models\Setting::get('contact_phone') }}
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Email Support --}}
                    @if(\App\Models\Setting::get('contact_email'))
                        <div
                            class="bg-white rounded-2xl p-8 shadow-xl shadow-gray-200/50 border border-gray-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300 flex items-center gap-6">
                            <div
                                class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0 group-hover:bg-primary group-hover:text-white transition-colors duration-300">
                                <svg class="w-5 h-5 text-primary group-hover:text-white transition-colors duration-300"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1">Email Support
                                </p>
                                <a href="mailto:{{ \App\Models\Setting::get('contact_email') }}"
                                    class="text-base font-bold text-gray-900 hover:text-primary transition-colors">
                                    {{ \App\Models\Setting::get('contact_email') }}
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- WhatsApp Chat --}}
                    @if($whatsappNumber)
                        <div
                            class="bg-white rounded-2xl p-8 shadow-xl shadow-gray-200/50 border border-gray-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300 flex items-center gap-6">
                            <div
                                class="w-12 h-12 rounded-full bg-[#25D366]/10 flex items-center justify-center flex-shrink-0 group-hover:bg-[#25D366] group-hover:text-white transition-colors duration-300">
                                <svg class="w-6 h-6 text-[#25D366] group-hover:text-white transition-colors duration-300"
                                    fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1">WhatsApp Chat
                                </p>
                                <a href="https://wa.me/{{$whatsappNumber}}?text={{$whatsappMessage}}" target="_blank"
                                    rel="noopener"
                                    class="text-base font-bold text-gray-900 hover:text-primary transition-colors">
                                    Chat with us instantly
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Socials --}}
                <div class="mt-8 pt-8 border-t border-gray-100">
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-4">Follow Us</p>
                    <div class="flex gap-4">
                        <a href="{{ $facebook }}" target="_blank"
                            class="w-10 h-10 rounded-full bg-[#1877F2]/10 flex items-center justify-center text-[#1877F2] hover:bg-[#1877F2] hover:text-white transition-colors duration-300">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </a>
                        <a href="{{ $instagram }}" target="_blank"
                            class="w-10 h-10 rounded-full bg-[#E4405F]/10 flex items-center justify-center text-[#E4405F] hover:bg-[#E4405F] hover:text-white transition-colors duration-300">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z" />
                            </svg>
                        </a>
                        <a href="{{ $twitter }}" target="_blank"
                            class="w-10 h-10 rounded-full bg-[#1DA1F2]/10 flex items-center justify-center text-[#1DA1F2] hover:bg-[#1DA1F2] hover:text-white transition-colors duration-300">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723 10.054 10.054 0 01-3.127 1.184 4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                            </svg>
                        </a>
                    </div>
                </div>

            </div>

            {{-- RIGHT SIDE: Livewire Form --}}
            <div class="lg:col-span-7 pt-12 lg:pt-0">
                <div
                    class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 p-8 md:p-12 relative overflow-hidden">
                    {{-- Decorative Accent --}}
                    <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-bl-[100px] -z-0"></div>

                    <div class="relative z-10">
                        <h2 class="text-2xl font-bold text-gray-900 mb-8">Send us a message</h2>

                        {{-- The actual form component --}}
                        <div class="contact-form-wrapper">
                            <livewire:contact.contact-form />
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- LOCATION MAP --}}
    <section class="border-t border-gray-200 py-16 lg:py-24">
        <div class="max-w-[90rem] mx-auto px-4 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">Visit Our Office</h2>
                <p class="text-base text-gray-500 font-medium">We'd love to meet you in person. Drop by our office for
                    a consultation.</p>
            </div>
            <div
                class="w-full h-[400px] md:h-[500px] rounded-2xl overflow-hidden shadow-xl shadow-gray-200/50 border border-gray-100 relative z-10">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d127056.40250630018!2d-0.11726053335559381!3d5.6424361545642875!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xfdf83262c5929b9%3A0x600f90e5f29910d5!2sAccra%2C%20Ghana!5e0!3m2!1sen!2sus!4v1714493351989!5m2!1sen!2sus"
                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </section>

</x-layouts.public>