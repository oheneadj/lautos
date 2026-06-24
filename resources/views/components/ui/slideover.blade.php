{{--
    Shared slide-over shell — a panel that slides in from the right edge of
    the screen with a fading backdrop, for content that reads like an
    ongoing conversation (e.g. the support chat bubble) rather than a
    one-shot confirmation, which is what x-ui.modal is for.

    I keep this always rendered in the DOM and let Alpine's x-show/x-transition
    handle open + close — wrapping it in a Livewire @if would let Livewire
    remove it from the DOM the instant the property flips, with no chance
    for an exit animation to play.
--}}
@props([
    'closeAction',
    'show',
    'width' => 'max-w-md',
])

<div
    x-data="{ open: @entangle($show) }"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex justify-end"
>
    <div
        x-show="open"
        x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/40 backdrop-blur-[2px]"
        wire:click="{{ $closeAction }}"
    ></div>
    <div
        x-show="open"
        x-transition:enter="transition-transform ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition-transform ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        {{ $attributes->merge(['class' => "relative z-10 h-full w-full {$width} bg-base-100 shadow-2xl flex flex-col"]) }}
    >
        {{ $slot }}
    </div>
</div>
