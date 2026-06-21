@props([
    'label'    => null,
    'hint'     => null,
    'error'    => null,
    'required' => false,
    'rows'     => 3,
])

@php
$textareaId = $attributes->get('id') ?? 'textarea-' . uniqid();
$base = 'w-full px-[14px] py-[10px] text-[15px] bg-base-100 border rounded-md transition-all outline-none placeholder:text-base-content/40';
$normal = 'border-base-content/10 focus:border-primary focus:ring-3 focus:ring-primary/20';
$err    = 'border-error focus:ring-3 focus:ring-error/20';
$classes = $base . ' ' . ($error ? $err : $normal);
@endphp

<div class="flex flex-col gap-1">
    @if ($label)
        <label for="{{ $textareaId }}" class="text-[13px] font-medium text-base-content">
            {{ $label }}@if ($required) <span class="text-error ml-0.5">*</span> @endif
        </label>
    @endif

    <textarea
        rows="{{ $rows }}"
        {{ $attributes->merge(['class' => $classes, 'id' => $textareaId]) }}
    ></textarea>

    @if ($error)
        <span class="text-xs text-error flex items-center gap-1"><span>⚠</span> {{ $error }}</span>
    @elseif ($hint)
        <span class="text-[11px] text-base-content/60">{{ $hint }}</span>
    @endif
</div>
