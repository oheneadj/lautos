{{-- Cookie Consent Banner --}}
<div
    x-data="{
        show: false,
        init() {
            if (!localStorage.getItem('cookie_consent')) {
                setTimeout(() => this.show = true, 1500);
            }
        },
        accept() {
            localStorage.setItem('cookie_consent', 'accepted');
            document.cookie = 'cookie_consent=accepted; max-age=' + (365 * 24 * 60 * 60) + '; path=/; SameSite=Lax';
            this.show = false;
        },
        decline() {
            localStorage.setItem('cookie_consent', 'declined');
            document.cookie = 'cookie_consent=declined; max-age=' + (365 * 24 * 60 * 60) + '; path=/; SameSite=Lax';
            this.show = false;
        }
    }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-4"
    x-cloak
    class="fixed bottom-4 left-4 right-4 z-[100] md:left-auto md:right-6 md:bottom-6 md:max-w-md"
>
    <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 p-5 md:p-6">
        {{-- Icon + Title --}}
        <div class="flex items-start gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-amber-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2a10 10 0 1 0 10 10A10.011 10.011 0 0 0 12 2Zm0 18a8 8 0 0 1-7.937-7.064A3.005 3.005 0 0 0 7 10a3 3 0 0 0-2.878-2.994A8 8 0 0 1 20 12a8.009 8.009 0 0 1-8 8Z"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <circle cx="10.5" cy="14.5" r="1.5"/>
                    <circle cx="15" cy="10" r="1"/>
                    <circle cx="14" cy="15" r="1"/>
                </svg>
            </div>
            <div>
                <h3 class="text-[15px] font-bold text-gray-900">{{ __('We use cookies') }}</h3>
                <p class="text-[12px] text-gray-500 mt-0.5 leading-relaxed">
                    {{ __('We use cookies to enhance your browsing experience, analyze site traffic, and personalize content. By clicking "Accept", you consent to our use of cookies.') }}
                </p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-2 mt-4">
            <button
                @click="accept()"
                class="flex-1 px-4 py-2.5 rounded-xl bg-gray-900 text-white text-[13px] font-bold hover:bg-gray-800 transition-colors duration-150 cursor-pointer"
            >
                {{ __('Accept All') }}
            </button>
            <button
                @click="decline()"
                class="flex-1 px-4 py-2.5 rounded-xl bg-gray-100 text-gray-700 text-[13px] font-bold hover:bg-gray-200 transition-colors duration-150 cursor-pointer"
            >
                {{ __('Decline') }}
            </button>
        </div>

        {{-- Privacy link --}}
        <div class="mt-3 text-center">
            <a href="{{ route('pages.privacy') }}" class="text-[11px] font-medium text-gray-400 hover:text-gray-600 hover:underline transition-colors">
                {{ __('Read our Privacy Policy') }}
            </a>
        </div>
    </div>
</div>
