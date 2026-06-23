{{--
    The card shell used throughout the customer dashboard — same look as
    the dashboard overview's stat/widget cards (rounded-xl, hairline
    border, optional header with a title and a "View all" action).
    Body padding is left to the caller, since list cards, image cards,
    and stat cards each need different spacing.
--}}
@props([
    'title' => null,
    'subtitle' => null,
    'actionLabel' => null,
    'actionHref' => null,
    'headerBorder' => false,
    'plain' => false,
])

{{--
    "plain" skips the default bg-base-100/border so a caller can give the
    card its own background (e.g. the red "Total Orders" stat card) without
    fighting the default classes for CSS specificity.
--}}
<div {{ $attributes->merge(['class' => $plain ? 'rounded-xl' : 'bg-base-100 border border-base-content/5 rounded-xl']) }}>
    @if ($title || isset($icon))
        <div class="flex items-center justify-between px-5 pt-5 pb-3 {{ $headerBorder ? 'border-b border-base-content/5' : '' }}">
            <div>
                @if ($subtitle)
                    <p class="text-[10px] font-bold uppercase tracking-widest text-base-content/40">{{ $subtitle }}</p>
                @endif
                @if ($title)
                    <h2 class="text-lg font-semibold text-base-content {{ $subtitle ? 'mt-0.5' : '' }}">{{ $title }}</h2>
                @endif
            </div>

            @if ($actionLabel && $actionHref)
                <a href="{{ $actionHref }}" wire:navigate class="text-[11px] font-bold text-base-content/50 hover:text-primary transition-colors duration-150">
                    {{ $actionLabel }}
                </a>
            @elseif (isset($icon))
                {{ $icon }}
            @endif
        </div>
    @endif

    {{ $slot }}
</div>
