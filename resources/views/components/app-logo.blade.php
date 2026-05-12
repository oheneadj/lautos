@props([
    'sidebar' => false,
])

<a {{ $attributes->merge(['class' => 'flex items-center gap-2 font-medium']) }}>
    <span class="flex aspect-square size-8 items-center justify-center rounded-md bg-zinc-900">
        <x-app-logo-icon class="size-5 fill-current text-white" />
    </span>
    <span class="text-sm font-semibold text-zinc-900">Laravel Starter Kit</span>
</a>
