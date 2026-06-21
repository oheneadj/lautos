{{-- Append-only internal notes — never shown to the customer. --}}
@if ($order->notes->isEmpty())
    <p class="text-sm text-gray-500">No notes yet.</p>
@else
    <div class="space-y-3">
        @foreach ($order->notes as $note)
            <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                <p class="text-sm">{{ $note->note }}</p>
                <p class="mt-1 text-xs text-gray-500">
                    {{ $note->admin?->name ?? 'Unknown' }} · {{ $note->created_at->format('M j, Y g:ia') }}
                </p>
            </div>
        @endforeach
    </div>
@endif
