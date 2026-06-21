@props([
    'padding' => 'default',
    'accent'  => null,
])

@php
$paddings = [
    'default' => 'p-6',
    'compact' => 'p-4',
    'none'    => 'p-0',
];

$accentMap = [
    'rose'    => 'border-t-3 border-t-primary',
    'green'   => 'border-t-3 border-t-[#18542A]',
    'warning' => 'border-t-3 border-t-warning',
    'info'    => 'border-t-3 border-t-info',
    'primary' => 'border-t-3 border-t-primary',
];

$base = 'bg-base-100 border border-base-content/10 rounded-lg shadow-sm transition-all duration-200';
$accentClass = $accent ? ' ' . ($accentMap[$accent] ?? '') : '';
$paddingClass = $paddings[$padding] ?? $paddings['default'];
@endphp

<div {{ $attributes->merge(['class' => $base . $accentClass]) }}>
    <div class="{{ $paddingClass }}">
        {{ $slot }}
    </div>
</div>
