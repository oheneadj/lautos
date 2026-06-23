<div class="space-y-6">
    {{-- Page Header --}}
    <div>
        <h1 class="text-[28px] font-semibold text-base-content leading-tight">{{ __('Payment History & Invoices') }}</h1>
        <p class="text-[14px] text-base-content/50 mt-1">{{ __('Review your order payments and download receipts') }}</p>
    </div>

    {{-- Billing Table --}}
    @if ($orders->isEmpty())
        <x-ui.card class="p-14 text-center">
            <svg class="mx-auto w-12 h-12 text-base-content/20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
            <p class="mt-3 text-[15px] font-bold text-base-content">{{ __('No billing history') }}</p>
            <p class="mt-1 text-[13px] text-base-content/40">{{ __('When you purchase a vehicle, your payment records will appear here.') }}</p>
        </x-ui.card>
    @else
        <x-ui.card class="flex flex-col overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-base-200 border-b border-base-content/5">
                        <tr>
                            <th class="px-6 py-3 text-[11px] font-bold uppercase tracking-widest text-base-content/60">{{ __('INVOICE ID') }}</th>
                            <th class="px-6 py-3 text-[11px] font-bold uppercase tracking-widest text-base-content/60">{{ __('DESCRIPTION') }}</th>
                            <th class="px-6 py-3 text-[11px] font-bold uppercase tracking-widest text-base-content/60">{{ __('AMOUNT') }}</th>
                            <th class="px-6 py-3 text-[11px] font-bold uppercase tracking-widest text-base-content/60">{{ __('STATUS') }}</th>
                            <th class="px-6 py-3 text-[11px] font-bold uppercase tracking-widest text-base-content/60">{{ __('DATE') }}</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="text-[13px] text-base-content divide-y divide-base-content/5">
                        @foreach ($orders as $order)
                            <tr class="hover:bg-base-200/40 transition-colors duration-150">
                                <td class="px-6 py-4 font-mono text-[12px] font-medium text-base-content/70">
                                    INV-{{ strtoupper(substr($order->uuid, 0, 8)) }}
                                </td>
                                <td class="px-6 py-4 font-medium text-base-content">
                                    @if ($order->car)
                                        {{ $order->car->year }} {{ $order->car->make->name }} {{ $order->car->carModel->name }}
                                    @else
                                        {{ __('Car Import Order') }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-bold text-base-content">
                                    ${{ number_format($order->total_usd_cents / 100, 2) }}
                                </td>
                                <td class="px-6 py-4">
                                    @if ($order->paymentProofs && $order->paymentProofs->count() > 0)
                                        <x-ui.badge type="success">{{ __('Paid') }}</x-ui.badge>
                                    @else
                                        <x-ui.badge type="warning">{{ __('Unpaid') }}</x-ui.badge>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-[12px] text-base-content/40 font-medium whitespace-nowrap">
                                    {{ $order->created_at->format('d M, Y') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('dashboard.orders.show', $order->uuid) }}" wire:navigate class="inline-flex items-center gap-1 text-[11px] font-bold text-primary hover:underline">
                                        {{ __('View Details') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        <div class="pt-4">
            {{ $orders->links() }}
        </div>
    @endif
</div>
