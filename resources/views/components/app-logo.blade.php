@props([
    'sidebar' => false,
])

@php
    $logoPath = \App\Models\Setting::get('site_logo_path');
@endphp

<a {{ $attributes->merge(['class' => 'flex items-center gap-2 hover:opacity-80 transition-opacity duration-200']) }}>
    @if ($logoPath)
        <img src="{{ \Illuminate\Support\Facades\Storage::url($logoPath) }}" alt="{{ config('app.name') }}" class="h-6 w-auto">
    @else
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" viewBox="0 0 24 24" fill="currentColor">
            <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.85 7h10.29l1.08 3.11H5.77L6.85 7zM19 17H5v-5h14v5z"/>
            <circle cx="7.5" cy="14.5" r="1.5"/>
            <circle cx="16.5" cy="14.5" r="1.5"/>
        </svg>
    @endif
    <span class="text-[14px] font-semibold text-base-content">{{ config('app.name') }}</span>
</a>
