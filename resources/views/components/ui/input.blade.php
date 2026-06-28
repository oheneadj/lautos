@props([
    'label'    => null,
    'hint'     => null,
    'error'    => null,
    'required' => false,
])

@php
$type = $attributes->get('type', 'text');
$isPassword = $type === 'password';
$inputId = $attributes->get('id') ?? 'input-' . uniqid();

// I default name from the label (or wire:model, as a fallback) so every
// input is autofill/screen-reader friendly even when a caller doesn't
// bother passing one explicitly — Livewire's own binding doesn't need it,
// but browsers and assistive tech do.
$inputName = $attributes->get('name') ?? \Illuminate\Support\Str::slug(preg_replace('/\s*\(.*?\)/', '', $label ?? $attributes->get('wire:model', 'field')), '_');

$baseClasses = 'w-full px-[14px] py-[10px] text-[15px] bg-base-100 border rounded-lg transition-all duration-120 outline-none placeholder:text-base-content/40 disabled:bg-base-200 disabled:cursor-not-allowed';
$normalClasses = 'border-base-content/10 focus:border-primary focus:ring-3 focus:ring-primary/20';
$errorClasses  = 'border-error focus:ring-3 focus:ring-error/20';

$inputClasses = $baseClasses . ' ' . ($error ? $errorClasses : $normalClasses);
if ($isPassword) $inputClasses .= ' pr-11';
@endphp

<div class="flex flex-col gap-1">
    @if ($label)
        <label for="{{ $inputId }}" class="text-[13px] font-medium text-base-content">
            {{ $label }}@if ($required) <span class="text-error ml-0.5">*</span> @endif
        </label>
    @endif

    <div class="relative">
        @if ($isPassword)
            <input
                {{ $attributes->merge(['class' => $inputClasses, 'id' => $inputId, 'name' => $inputName]) }}
                x-data="{ show: false }"
                x-bind:type="show ? 'text' : 'password'"
            >
            <button
                type="button"
                class="absolute inset-y-0 right-0 pr-3 flex items-center text-base-content/40 hover:text-base-content/70"
                x-data
                @click="$el.closest('.relative').querySelector('input').type === 'password'
                    ? $el.closest('.relative').querySelector('input').type = 'text'
                    : $el.closest('.relative').querySelector('input').type = 'password'"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </button>
        @else
            <input
                {{ $attributes->merge(['class' => $inputClasses, 'id' => $inputId, 'name' => $inputName]) }}
            >
        @endif
    </div>

    @if ($error)
        <span class="text-xs text-error flex items-center gap-1">
            <span>⚠</span> {{ $error }}
        </span>
    @elseif ($hint)
        <span class="text-[11px] text-base-content/60">{{ $hint }}</span>
    @endif
</div>
