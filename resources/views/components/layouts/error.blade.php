{{--
    Standalone layout for error pages (404, 500, 503, etc) — a centered card,
    no header/footer chrome. I deliberately don't reuse <x-layouts.public> —
    that layout queries Setting::get() and the authenticated user for the
    header/footer, and a 500/503 is exactly the moment those queries are most
    likely to be the thing that's broken. I still look up the real site logo
    below, but wrapped in a try/catch — if that lookup is itself what's down,
    the page falls back to the app name instead of failing to render at all.
--}}
@php
    $errorLogoPath = null;
    try {
        $errorLogoPath = \App\Models\Setting::get('site_logo_path');
    } catch (\Throwable $e) {
        // I swallow this deliberately — an error page must never itself error.
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Error' }} — {{ config('app.name') }}</title>
    <meta name="robots" content="noindex">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:300,400,500,600,700,800" rel="stylesheet" />

    @vite(['resources/css/app.css'])
</head>
<body class="bg-base-200 text-base-content antialiased font-sans min-h-screen flex flex-col items-center justify-center px-4 py-12">

    {{-- Brand mark — real logo if one's configured, app name as a fallback. --}}
    <a href="{{ url('/') }}" class="mb-8 flex items-center gap-2.5 hover:opacity-80 transition-opacity">
        @if ($errorLogoPath)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($errorLogoPath) }}" alt="{{ config('app.name') }}" class="h-9 w-auto">
        @else
            <span class="flex items-center justify-center w-10 h-10 rounded-full bg-primary text-white font-black text-sm">{{ \Illuminate\Support\Str::of(config('app.name'))->explode(' ')->map(fn ($word) => $word[0])->take(2)->implode('') }}</span>
            <span class="block">
                <span class="block font-black text-lg leading-tight tracking-tight text-gray-900">{{ config('app.name') }}</span>
                <span class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 leading-tight">Quality Japanese &amp; Korean Imports</span>
            </span>
        @endif
    </a>

    {{ $slot }}

    <p class="mt-8 text-sm text-gray-400">
        Need help? <a href="{{ url('/contact') }}" class="font-semibold text-primary hover:underline">Contact us</a>
    </p>

</body>
</html>
