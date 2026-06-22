{{--
    Floating WhatsApp button, visible on every public page. I use WhatsApp's own
    brand green here rather than our red/gold palette — visitors recognise the
    real WhatsApp colour instantly, and mixing it into our brand colours would
    just make it harder to spot.
--}}
@php
    $number = preg_replace('/\D/', '', (string) \App\Models\Setting::get('whatsapp_number', ''));
    $message = rawurlencode('Hello, I am interested in your cars.');
@endphp

@if ($number)
    <a
        href="https://wa.me/{{ $number }}?text={{ $message }}"
        target="_blank"
        rel="noopener"
        aria-label="Chat with us on WhatsApp"
        class="fixed bottom-5 right-5 z-40 flex h-14 w-14 items-center justify-center rounded-full shadow-lg transition-transform hover:scale-105 sm:bottom-6 sm:right-6"
        style="background-color: #25D366;"
    >
        <svg class="h-8 w-8" viewBox="0 0 32 32" fill="#FFFFFF" aria-hidden="true">
            <path d="M16.004 0C7.166 0 0 7.166 0 16.004c0 2.82.745 5.587 2.16 8.012L.06 31.94l8.107-2.067a15.93 15.93 0 0 0 7.837 2.058h.004c8.836 0 16.002-7.166 16.002-16.004C32.01 7.166 24.84 0 16.004 0zm0 29.27a13.23 13.23 0 0 1-6.747-1.85l-.484-.288-5.025 1.281 1.34-4.896-.316-.502a13.2 13.2 0 0 1-2.026-7.011c0-7.31 5.948-13.258 13.262-13.258 3.543 0 6.873 1.381 9.378 3.888a13.2 13.2 0 0 1 3.881 9.374c0 7.314-5.95 13.262-13.263 13.262zm7.27-9.933c-.398-.199-2.356-1.163-2.722-1.296-.365-.133-.63-.199-.896.199-.265.398-1.029 1.296-1.262 1.561-.232.265-.464.298-.862.1-2.33-1.165-3.857-2.08-5.392-4.717-.408-.703.408-.653 1.166-2.176.13-.265.066-.497-.066-.696-.133-.199-.896-2.16-1.227-2.96-.323-.778-.654-.672-.896-.685-.232-.013-.498-.016-.764-.016-.265 0-.696.099-1.062.398-.365.298-1.395 1.362-1.395 3.322 0 1.96 1.426 3.854 1.624 4.119.199.265 2.722 4.158 6.6 5.665 3.879 1.508 3.879 1.006 5.142.946 1.262-.06 2.722-.913 3.087-1.793.365-.879.365-1.626.265-1.793-.099-.166-.365-.265-.764-.464z"/>
        </svg>
    </a>
@endif
