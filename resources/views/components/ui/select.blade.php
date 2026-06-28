@props([
    'label'    => null,
    'hint'     => null,
    'error'    => null,
    'required' => false,
])

@php
$selectId = $attributes->get('id') ?? 'select-' . uniqid();
$selectName = $attributes->get('name') ?? \Illuminate\Support\Str::slug(preg_replace('/\s*\(.*?\)/', '', $label ?? $attributes->get('wire:model', 'field')), '_');
$base = 'w-full px-[14px] py-[10px] text-[15px] bg-base-100 border rounded-md transition-all duration-120 outline-none disabled:bg-base-200 disabled:cursor-not-allowed';
$normal = 'border-base-content/10 focus:border-primary focus:ring-3 focus:ring-primary/20';
$err    = 'border-error focus:ring-3 focus:ring-error/20';
$classes = $base . ' ' . ($error ? $err : $normal);
@endphp

<div class="flex flex-col gap-1">
    @if ($label)
        <label for="{{ $selectId }}" class="text-[13px] font-medium text-base-content">
            {{ $label }}@if ($required) <span class="text-error ml-0.5">*</span> @endif
        </label>
    @endif

    <select {{ $attributes->merge(['class' => $classes, 'id' => $selectId, 'name' => $selectName]) }}>
        {{ $slot }}
    </select>

    @if ($error)
        <span class="text-xs text-error flex items-center gap-1"><span>⚠</span> {{ $error }}</span>
    @elseif ($hint)
        <span class="text-[11px] text-base-content/60">{{ $hint }}</span>
    @endif
</div>
