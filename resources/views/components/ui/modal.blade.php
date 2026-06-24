{{--
    Shared modal shell — centered overlay with a backdrop that closes on
    click-outside. Used for the order-confirmation modal, the create-ticket
    modal, and the login-to-save prompt, so we're not copy-pasting the same
    overlay markup a third time.
--}}
@props([
    'closeAction',
    'maxWidth' => 'max-w-lg',
])

<div class="fixed inset-0 z-50 flex items-center justify-center p-4" wire:click.self="{{ $closeAction }}">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-[2px]"></div>
    <div {{ $attributes->merge(['class' => "relative z-10 w-full {$maxWidth} rounded-2xl bg-base-100 p-6 shadow-2xl border border-base-content/10"]) }}>
        {{ $slot }}
    </div>
</div>
