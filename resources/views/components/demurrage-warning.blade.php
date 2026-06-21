@php
    // I read the warning text from settings so admin can update it without a deployment.
    $message = \App\Models\Setting::get('demurrage_warning');
@endphp

@if ($message)
    <div {{ $attributes->merge(['class' => 'bg-warning/5 border border-warning/20 rounded-xl p-5 flex items-start gap-4']) }}>
        <div class="w-9 h-9 rounded-lg bg-warning/15 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zM12 15.75h.008" />
            </svg>
        </div>
        <div>
            <p class="text-[13px] font-bold uppercase tracking-widest text-warning mb-1">Clearing &amp; Demurrage Notice</p>
            <p class="text-[13px] text-base-content/70 leading-relaxed">{{ $message }}</p>
        </div>
    </div>
@endif
