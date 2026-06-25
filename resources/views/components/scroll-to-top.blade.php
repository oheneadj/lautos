{{--
    Floating "back to top" button. I only show it once the page has scrolled
    past one screen height, so it doesn't clutter pages short enough that
    scrolling to the top is already trivial.
--}}
<button
    type="button"
    x-data="{ visible: false }"
    x-show="visible"
    x-on:scroll.window="visible = window.scrollY > window.innerHeight"
    x-on:click="window.scrollTo({ top: 0, behavior: 'smooth' })"
    x-transition
    x-cloak
    aria-label="Scroll to top"
    class="fixed bottom-24 right-5 z-40 flex h-11 w-11 items-center justify-center rounded-full bg-primary text-white shadow-lg transition-transform hover:scale-105 sm:bottom-[88px] sm:right-6"
>
    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
    </svg>
</button>
