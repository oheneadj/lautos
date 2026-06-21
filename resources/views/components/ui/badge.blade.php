@props([
    'type' => 'ghost',
    'dot'  => false,
])

@php
$map = [
    'success'   => 'bg-success/10 text-success border border-success/20',
    'confirmed' => 'bg-success/10 text-success border border-success/20',
    'completed' => 'bg-success/10 text-success border border-success/20',
    'paid'      => 'bg-success/10 text-success border border-success/20',
    'warning'   => 'bg-warning/10 text-warning border border-warning/20',
    'pending'   => 'bg-warning/10 text-warning border border-warning/20',
    'preparing' => 'bg-warning/10 text-warning border border-warning/20',
    'danger'    => 'bg-error/10 text-error border border-error/20',
    'cancelled' => 'bg-error/10 text-error border border-error/20',
    'failed'    => 'bg-error/10 text-error border border-error/20',
    'info'      => 'bg-info/10 text-info border border-info/20',
    'new'       => 'bg-info/10 text-info border border-info/20',
    'primary'   => 'bg-primary/10 text-primary border border-primary/20',
    'brand'     => 'bg-primary/10 text-primary border border-primary/20',
    'ghost'     => 'bg-base-200 text-base-content/60 border border-base-content/10',
    'neutral'   => 'bg-neutral text-white border border-neutral/20',
    'outline'   => 'bg-transparent text-base-content border border-base-content/20',
];

$dotColors = [
    'success' => 'bg-success', 'confirmed' => 'bg-success', 'completed' => 'bg-success', 'paid' => 'bg-success',
    'warning' => 'bg-warning', 'pending' => 'bg-warning', 'preparing' => 'bg-warning',
    'danger'  => 'bg-error', 'cancelled' => 'bg-error', 'failed' => 'bg-error',
    'info'    => 'bg-info', 'new' => 'bg-info',
    'primary' => 'bg-primary', 'brand' => 'bg-primary',
    'ghost'   => 'bg-base-content/40',
];

$classes = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest ' . ($map[$type] ?? $map['ghost']);
$dotColor = $dotColors[$type] ?? 'bg-base-content/40';
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if ($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }}"></span>
    @endif
    {{ $slot }}
</span>
