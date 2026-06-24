@props(['url'])
@php
    $siteLogoPath = \App\Models\Setting::get('site_logo_path');
@endphp
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if ($siteLogoPath)
<img src="{{ \Illuminate\Support\Facades\Storage::url($siteLogoPath) }}" class="logo" alt="{{ config('app.name') }} Logo">
@elseif (file_exists(public_path('logo.png')))
<img src="{{ asset('logo.png') }}" class="logo" alt="{{ config('app.name') }} Logo">
@elseif (file_exists(public_path('images/logo.png')))
<img src="{{ asset('images/logo.png') }}" class="logo" alt="{{ config('app.name') }} Logo">
@else
<img src="https://laravel.com/img/notification-logo-v2.1.png" class="logo" alt="Laravel Logo">
@endif
</a>
</td>
</tr>
