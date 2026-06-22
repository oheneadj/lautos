@props([
    'type'        => 'info',
    'title'       => null,
    'dismissible' => false,
])

@php
$styles = [
    'success' => ['bg' => 'bg-success/20', 'border' => 'border-l-success', 'text' => 'text-success', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'],
    'warning' => ['bg' => 'bg-warning/20', 'border' => 'border-l-warning', 'text' => 'text-warning', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />'],
    'danger'  => ['bg' => 'bg-error/20',   'border' => 'border-l-error',   'text' => 'text-error',   'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />'],
    'info'    => ['bg' => 'bg-info/20',    'border' => 'border-l-info',    'text' => 'text-info',    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'],
];
$s = $styles[$type] ?? $styles['info'];
@endphp

<div
    {{ $attributes->merge(['class' => "rounded-lg py-[14px] px-[16px] flex gap-3 border-l-3 {$s['bg']} {$s['border']}"]) }}
    @if ($dismissible) x-data="{ show: true }" x-show="show" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @endif
>
    <svg class="w-4 h-4 flex-shrink-0 mt-0.5 {{ $s['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        {!! $s['icon'] !!}
    </svg>
    <div class="flex-1 min-w-0">
        @if ($title)
            <p class="text-[14px] font-bold text-base-content">{{ $title }}</p>
        @endif
        @if (!$slot->isEmpty())
            <p class="text-[13px] font-medium leading-relaxed text-base-content mt-1">{{ $slot }}</p>
        @endif
    </div>
    @if ($dismissible)
        <button @click="show = false" class="p-1 -mr-2 -mt-2 hover:bg-black/5 rounded-md opacity-60 hover:opacity-100 flex-shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    @endif
</div>
