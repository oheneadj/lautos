@props([
    'title',
    'description' => null,
])

<div class="flex flex-col text-center gap-1">
    <h1 class="text-xl font-semibold text-base-content">{{ $title }}</h1>
    @if ($description)
        <p class="text-[14px] text-base-content/50 font-medium leading-relaxed">{{ $description }}</p>
    @endif
</div>
