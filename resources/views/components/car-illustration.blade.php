{{--
    A small inline car illustration for error pages — built as plain SVG shapes
    rather than downloaded artwork, so it always renders (no external asset
    fetch, no broken-link risk) and matches the site's own stroke-icon style.

    variant:
      'lost'    — 404, car facing a signpost with a question mark
      'blocked' — 403, car stopped at a barrier
      'broken'  — 500/503, car with a wrench overlay
      'wait'    — 419/429, car next to a clock
--}}
@props(['variant' => 'lost'])

<svg viewBox="0 0 280 160" {{ $attributes->class(['h-auto']) }} fill="none" xmlns="http://www.w3.org/2000/svg">
    {{-- Ground shadow + road --}}
    <ellipse cx="110" cy="138" rx="80" ry="4" fill="currentColor" class="text-gray-100/60" />
    <line x1="0" y1="148" x2="280" y2="148" stroke="currentColor" stroke-width="2" stroke-dasharray="10 10" class="text-gray-200" />

    {{-- Car body --}}
    <path d="M28 122c-6 0-10-4-10-10v-8c0-5 3-9 8-11l14-6 12-16c3-4 8-7 13-7h44c6 0 11 3 14 8l9 15 16 4c6 2 10 7 10 13v8c0 6-4 10-10 10H28z"
          fill="currentColor" class="text-primary" />
    {{-- Windows --}}
    <path d="M62 84l9-13c2-3 5-4 8-4h18v17H62z" fill="currentColor" class="text-white/30" />
    <path d="M101 67h22c3 0 6 1 8 4l8 13h-38V67z" fill="currentColor" class="text-white/30" />

    {{-- Wheels --}}
    <circle cx="58" cy="122" r="14" fill="currentColor" class="text-gray-900" />
    <circle cx="58" cy="122" r="5" fill="currentColor" class="text-gray-300" />
    <circle cx="158" cy="122" r="14" fill="currentColor" class="text-gray-900" />
    <circle cx="158" cy="122" r="5" fill="currentColor" class="text-gray-300" />

    {{-- Headlight --}}
    <circle cx="183" cy="100" r="4" fill="currentColor" class="text-secondary" />

    @if ($variant === 'lost')
        {{-- Signpost with a question mark, just ahead of the car --}}
        <line x1="225" y1="138" x2="225" y2="60" stroke="currentColor" stroke-width="4" class="text-gray-300" stroke-linecap="round" />
        <rect x="215" y="48" width="44" height="22" rx="4" fill="currentColor" class="text-gray-900" />
        <text x="237" y="64" text-anchor="middle" font-size="18" font-weight="700" fill="white">?</text>
    @elseif ($variant === 'blocked')
        {{-- Striped barrier in front of the car --}}
        <rect x="208" y="78" width="14" height="50" rx="3" fill="currentColor" class="text-gray-300" />
        <rect x="252" y="78" width="14" height="50" rx="3" fill="currentColor" class="text-gray-300" />
        <rect x="205" y="86" width="68" height="14" rx="4" fill="currentColor" class="text-primary" />
        <rect x="205" y="86" width="14" height="14" fill="currentColor" class="text-white" />
        <rect x="233" y="86" width="14" height="14" fill="currentColor" class="text-white" />
        <rect x="261" y="86" width="14" height="14" fill="currentColor" class="text-white" />
    @elseif ($variant === 'broken')
        {{-- Wrench + smoke puffs above the hood --}}
        <circle cx="218" cy="55" r="7" fill="currentColor" class="text-gray-200" />
        <circle cx="232" cy="40" r="5" fill="currentColor" class="text-gray-200" />
        <circle cx="206" cy="38" r="4" fill="currentColor" class="text-gray-200" />
        <path d="M196 70l9-9 3 3-4 4 4 4-4 4-4-4-4 4z" fill="currentColor" class="text-gray-900" />
    @else
        {{-- Clock, parked beside the car --}}
        <circle cx="232" cy="95" r="20" fill="currentColor" class="text-gray-900" />
        <circle cx="232" cy="95" r="16" fill="currentColor" class="text-white" />
        <line x1="232" y1="95" x2="232" y2="84" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" class="text-gray-900" />
        <line x1="232" y1="95" x2="240" y2="98" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" class="text-gray-900" />
    @endif
</svg>
