    <div class="space-y-6">
        {{-- Page Header --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h1 class="text-[28px] font-semibold text-base-content leading-tight">{{ __('My Car Orders') }}</h1>
                <p class="text-[14px] text-base-content/50 mt-1">{{ __('Track and manage all your vehicle imports') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('cars.index') }}" wire:navigate class="inline-flex items-center gap-2 rounded-xl bg-primary px-[18px] py-[10px] text-[13px] font-medium text-white hover:brightness-110 transition-all duration-150">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    {{ __('Browse Cars') }}
                </a>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="bg-base-100 border border-base-content/5 rounded-xl p-4">
            <div class="flex items-center gap-1 bg-base-200/50 rounded-lg p-1 flex-wrap">
                <button wire:click="$set('statusFilter', '')"
                    class="px-4 py-2 text-[12px] font-bold rounded-md transition-all duration-150 cursor-pointer {{ $statusFilter === '' ? 'bg-base-100 text-base-content shadow-sm' : 'text-base-content/50 hover:text-base-content' }}">
                    {{ __('All') }}
                </button>

                @foreach (\App\Enums\OrderStatus::cases() as $status)
                    <button wire:click="$set('statusFilter', '{{ $status->value }}')"
                        class="px-4 py-2 text-[12px] font-bold rounded-md transition-all duration-150 cursor-pointer {{ $statusFilter === $status->value ? 'bg-base-100 text-base-content shadow-sm' : 'text-base-content/50 hover:text-base-content' }}">
                        {{ $status->label() }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Orders Table --}}
        @if ($this->orders->isEmpty())
            <x-ui.card class="p-14 text-center">
                <svg class="mx-auto w-12 h-12 text-base-content/20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                <p class="mt-3 text-[15px] font-bold text-base-content">{{ __('No orders found') }}</p>
                <p class="mt-1 text-[13px] text-base-content/40">{{ $statusFilter ? __('Try a different filter or browse our catalogue.') : __('Start by browsing our available cars!') }}</p>
            </x-ui.card>
        @else
            <x-ui.card class="flex flex-col overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-base-200 border-b border-base-content/5">
                            <tr>
                                <th class="px-6 py-3 text-[11px] font-bold uppercase tracking-widest text-base-content/60">{{ __('VEHICLE') }}</th>
                                <th class="px-6 py-3 text-[11px] font-bold uppercase tracking-widest text-base-content/60">{{ __('YEAR') }}</th>
                                <th class="px-6 py-3 text-[11px] font-bold uppercase tracking-widest text-base-content/60">{{ __('AMOUNT') }}</th>
                                <th class="px-6 py-3 text-[11px] font-bold uppercase tracking-widest text-base-content/60">{{ __('STATUS') }}</th>
                                <th class="px-6 py-3 text-[11px] font-bold uppercase tracking-widest text-base-content/60">{{ __('PAYMENT') }}</th>
                                <th class="px-6 py-3 text-[11px] font-bold uppercase tracking-widest text-base-content/60">{{ __('DATE') }}</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="text-[13px] text-base-content divide-y divide-base-content/5">
                            @foreach ($this->orders as $order)
                                <tr class="hover:bg-base-200/40 transition-colors duration-150">
                                    <td class="px-6 py-3.5">
                                        <div class="flex items-center gap-3">
                                            {{-- Car Thumbnail --}}
                                            <div class="w-10 h-10 flex-shrink-0 rounded-lg bg-base-200 overflow-hidden">
                                                @if ($order->car_thumbnail_path)
                                                    <img src="{{ Storage::url($order->car_thumbnail_path) }}" alt="" class="size-full object-cover" />
                                                @else
                                                    <div class="flex size-full items-center justify-center text-[10px] font-bold text-base-content/40 uppercase">
                                                        {{ substr($order->car_make_name, 0, 2) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <span class="font-medium text-base-content">
                                                {{ $order->car_make_name }} {{ $order->car_model_name }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-3.5 text-base-content/60">
                                        {{ $order->car_year }}
                                    </td>
                                    <td class="px-6 py-3.5 font-bold text-base-content">
                                        ${{ number_format($order->total_usd_cents / 100, 0) }}
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <x-ui.badge :type="$order->status->colour()">
                                            {{ $order->status->label() }}
                                        </x-ui.badge>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        @if ($order->paymentProofs && $order->paymentProofs->count() > 0)
                                            <x-ui.badge type="success">{{ __('Paid') }}</x-ui.badge>
                                        @else
                                            <x-ui.badge type="warning">{{ __('Unpaid') }}</x-ui.badge>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3.5 text-[12px] text-base-content/40 font-medium whitespace-nowrap">
                                        {{ $order->created_at->format('d M, H:i') }}
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <a href="{{ route('dashboard.orders.show', $order->uuid) }}" wire:navigate class="inline-flex items-center gap-1 text-[11px] font-bold text-base-content/50 hover:text-primary transition-colors duration-150">
                                            {{ __('View') }}
                                            <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-ui.card>

            <div class="pt-4">
                {{ $this->orders->links() }}
            </div>
        @endif
    </div>
