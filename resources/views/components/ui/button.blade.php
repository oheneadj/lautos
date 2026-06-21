@props([
    'variant' => 'primary',
    'size'    => 'md',
    'loading' => false,
    'icon'    => null,
    'href'    => null,
])

@php
$base = 'inline-flex items-center justify-center font-medium rounded-xl transition-all duration-150 focus:outline-none focus:ring-3 focus:ring-offset-1 disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap no-underline';

$variants = [
    'primary'   => 'bg-primary text-white hover:bg-primary/90 focus:ring-primary/30',
    'secondary' => 'bg-base-200 text-base-content hover:bg-base-300 focus:ring-base-content/20',
    'success'   => 'bg-success text-white hover:bg-success/90 focus:ring-success/30',
    'danger'    => 'bg-error text-white hover:bg-error/90 focus:ring-error/30',
    'outline'   => 'bg-transparent border border-base-content/20 text-base-content hover:bg-base-200 focus:ring-base-content/20',
    'ghost'     => 'bg-transparent text-base-content hover:bg-base-200 focus:ring-base-content/20',
    'black'     => 'bg-black text-white hover:bg-black/80 focus:ring-black/30',
    'green'     => 'bg-[#18542A] text-white hover:bg-[#18542A]/90 focus:ring-[#18542A]/30',
    'accent'    => 'bg-accent text-black hover:bg-accent/90 focus:ring-accent/30',
];

$sizes = [
    'sm'      => 'px-[12px] py-[6px] text-[11px] gap-1',
    'md'      => 'px-[18px] py-[10px] text-[13px] gap-1.5',
    'lg'      => 'px-[24px] py-[13px] text-[15px] gap-2',
    'icon'    => 'p-[8px]',
    'icon-sm' => 'p-[5px]',
];

$iconSizes = [
    'sm'      => 'w-3.5 h-3.5',
    'md'      => 'w-4 h-4',
    'lg'      => 'w-5 h-5',
    'icon'    => 'w-4 h-4',
    'icon-sm' => 'w-3.5 h-3.5',
];

$classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
$iconClass = $iconSizes[$size] ?? $iconSizes['md'];
$tag = $href ? 'a' : 'button';
@endphp

<{{ $tag }}
    @if ($href) href="{{ $href }}" @endif
    @if (!$href) {{ $attributes->except(['class']) }} @else {{ $attributes->except(['class', 'href']) }} @endif
    @class([$classes, $attributes->get('class')])
    @if (!$href && !$attributes->has('type')) type="button" @endif
    @if ($loading) disabled @endif
>
    @if ($loading)
        <svg class="{{ $iconClass }} animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        <span class="invisible">{{ $slot }}</span>
    @else
        @if ($icon)
            <span @class([$iconClass])>{!! $icon !!}</span>
        @endif
        {{ $slot }}
    @endif
</{{ $tag }}>
