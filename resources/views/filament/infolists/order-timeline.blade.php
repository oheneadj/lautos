{{-- Visual 9-stage shipment timeline — completed stages get a timestamp from order_status_histories. --}}
@php
    $stages = \App\Enums\OrderStatus::pipeline();
    $currentIndex = array_search($order->status, $stages);
    $historyByStatus = $order->statusHistories->keyBy(fn ($h) => $h->status->value);
@endphp

<ol class="space-y-4">
    @foreach ($stages as $index => $stage)
        @php
            $isComplete = $index <= $currentIndex;
            $history = $historyByStatus->get($stage->value);
        @endphp
        <li class="flex items-start gap-3">
            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-semibold
                {{ $isComplete ? 'bg-success-500 text-white' : 'bg-gray-200 text-gray-500 dark:bg-gray-700' }}">
                {{ $isComplete ? '✓' : $index + 1 }}
            </span>
            <div>
                <p class="font-medium {{ $isComplete ? 'text-gray-950 dark:text-white' : 'text-gray-500' }}">
                    {{ $stage->label() }}
                </p>
                @if ($history)
                    <p class="text-xs text-gray-500">{{ $history->created_at->format('M j, Y g:ia') }}</p>
                @endif
            </div>
        </li>
    @endforeach
</ol>
