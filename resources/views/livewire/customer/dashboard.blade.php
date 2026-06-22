    <div class="space-y-6">
        {{-- Greeting Row --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h1 class="text-[28px] font-semibold text-base-content leading-tight">
                    @php $hour = now()->hour; @endphp
                    @if ($hour < 12) {{ __('Good morning') }}, @elseif ($hour < 17) {{ __('Good afternoon') }}, @else {{ __('Good evening') }}, @endif
                    <span class="text-primary">{{ $this->user->name }}</span>
                </h1>
                <p class="text-[14px] text-base-content/50 mt-1">{{ now()->format('l, d F Y') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('cars.index') }}" wire:navigate class="inline-flex items-center gap-2 rounded-xl bg-primary px-[18px] py-[10px] text-[13px] font-bold text-white hover:bg-primary/90 transition-all duration-150">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    {{ __('Browse More Cars') }}
                </a>
                <a href="{{ route('dashboard.orders') }}" wire:navigate class="inline-flex items-center gap-2 rounded-xl border border-base-content/10 bg-base-100 px-[18px] py-[10px] text-[13px] font-medium text-base-content hover:bg-base-200 transition-all duration-150">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" /></svg>
                    {{ __('FULL REPORT') }}
                </a>
            </div>
        </div>

        {{-- Alerts: Email Verification & KYC --}}
        @if ($this->needsEmailVerification)
            <x-ui.alert type="warning" title="Email Not Verified">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <span>Please verify your email address to access all features.</span>
                    <button wire:click="resendVerification" class="text-[12px] font-medium underline hover:no-underline cursor-pointer whitespace-nowrap">
                        {{ __('Resend Verification Email') }}
                    </button>
                </div>
            </x-ui.alert>
        @endif

        @if ($this->needsKyc)
            <x-ui.alert type="info" title="KYC Incomplete">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <span>Complete your KYC documents (Ghana Card or TIN) to place orders.</span>
                    <a href="{{ route('dashboard.profile') }}" wire:navigate class="text-[12px] font-medium underline hover:no-underline whitespace-nowrap">
                        {{ __('Complete KYC →') }}
                    </a>
                </div>
            </x-ui.alert>
        @endif

        {{-- Stat Cards Row --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Total Orders (active stat card — amber/error tint like "Need Attention") --}}
            <div class="bg-error text-white border border-error/20 rounded-xl p-4 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>
                </div>
                <div>
                    <p class="text-[20px] font-bold">{{ $this->totalOrders }}</p>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-white/70">{{ __('TOTAL ORDERS') }}</p>
                </div>
            </div>

            {{-- Saved Cars --}}
            <div class="bg-base-100 border border-base-content/5 rounded-xl p-4 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-info/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-info" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" /></svg>
                </div>
                <div>
                    <p class="text-[20px] font-bold text-base-content">{{ $this->savedCarsCount }}</p>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-base-content/40">{{ __('SAVED CARS') }}</p>
                </div>
            </div>

            {{-- Open Tickets --}}
            <div class="bg-base-100 border border-base-content/5 rounded-xl p-4 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-warning/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-warning" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" /></svg>
                </div>
                <div>
                    <p class="text-[20px] font-bold text-base-content">{{ $this->openTicketsCount }}</p>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-base-content/40">{{ __('OPEN TICKETS') }}</p>
                </div>
            </div>

            {{-- Unread Notifications --}}
            <div class="bg-base-100 border border-base-content/5 rounded-xl p-4 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-accent/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>
                </div>
                <div>
                    <p class="text-[20px] font-bold text-base-content">{{ $this->unreadNotificationsCount }}</p>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-base-content/40">{{ __('NEW ALERTS') }}</p>
                </div>
            </div>
        </div>

        {{-- Middle Row: 3 columns --}}
        <div class="grid gap-4 lg:grid-cols-3">
            {{-- Order Pipeline --}}
            <div class="bg-base-100 border border-base-content/5 rounded-xl">
                <div class="flex items-center justify-between px-5 pt-5 pb-3">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-base-content/40">{{ __('ALL TIME') }}</p>
                        <h2 class="text-lg font-semibold text-base-content mt-0.5">{{ __('Order Pipeline') }}</h2>
                    </div>
                    <div class="w-8 h-8 rounded-xl bg-error/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-error" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" /></svg>
                    </div>
                </div>
                <div class="px-5 pb-5">
                    @if (empty($this->ordersByStage))
                        <p class="text-[13px] text-base-content/40 py-4 text-center">{{ __('No order data yet.') }}</p>
                    @else
                        <div class="divide-y divide-base-content/5">
                            @foreach ($this->ordersByStage as $stage)
                                <div class="flex items-center justify-between py-2.5">
                                    <span class="text-[13px] font-medium text-base-content">{{ $stage['label'] }}</span>
                                    @php
                                        $dotColor = match($stage['colour']) {
                                            'success' => 'bg-success',
                                            'warning' => 'bg-secondary',
                                            'info'    => 'bg-info',
                                            'danger'  => 'bg-error',
                                            default   => 'bg-primary',
                                        };
                                    @endphp
                                    <span class="flex items-center gap-1.5 text-[13px] font-bold text-base-content">
                                        <span class="size-2 rounded-full {{ $dotColor }}"></span>
                                        {{ $stage['count'] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Recent Orders (compact list card) --}}
            <div class="bg-base-100 border border-base-content/5 rounded-xl lg:col-span-2">
                <div class="flex items-center justify-between px-5 pt-5 pb-3">
                    <h2 class="text-lg font-semibold text-base-content">{{ __('Recent Orders') }}</h2>
                    <a href="{{ route('dashboard.orders') }}" wire:navigate class="text-[11px] font-bold text-base-content/50 hover:text-primary transition-colors duration-150">
                        {{ __('View all →') }}
                    </a>
                </div>

                @if ($this->recentOrders->isEmpty())
                    <div class="px-5 pb-8 pt-4 text-center">
                        <svg class="mx-auto w-12 h-12 text-base-content/20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        <p class="mt-2 text-[13px] text-base-content/40">{{ __('No orders yet') }}</p>
                    </div>
                @else
                    <div class="divide-y divide-base-content/5">
                        @foreach ($this->recentOrders as $order)
                            <a href="{{ route('dashboard.orders.show', $order->uuid) }}" wire:navigate class="flex items-center gap-3 px-5 py-3 hover:bg-base-200/60 transition-colors duration-150 group">
                                {{-- Avatar/Initials --}}
                                <div class="flex w-9 h-9 flex-shrink-0 items-center justify-center rounded-lg bg-base-200 text-[11px] font-bold text-base-content/60 uppercase">
                                    @if ($order->car)
                                        {{ substr($order->car->make->name, 0, 2) }}
                                    @else
                                        --
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-[13px] font-medium text-base-content truncate group-hover:text-primary transition-colors duration-150">
                                        @if ($order->car)
                                            {{ $order->car->make->name }} {{ $order->car->carModel->name }}
                                        @else
                                            {{ __('Car Removed') }}
                                        @endif
                                    </p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <x-ui.badge :type="$order->status->colour()">
                                            {{ $order->status->label() }}
                                        </x-ui.badge>
                                    </div>
                                </div>

                                <div class="text-right flex-shrink-0">
                                    <p class="text-[13px] font-bold text-base-content">${{ number_format($order->total_usd_cents / 100, 0) }}</p>
                                    <p class="text-[12px] text-base-content/40 font-medium mt-0.5">{{ $order->created_at->format('d M') }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Full-Width Recent Orders Table --}}
        <div class="bg-white border border-base-content/5 shadow-sm rounded-xl flex flex-col overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-base-content/5">
                <div>
                    <h2 class="text-lg font-semibold text-base-content">{{ __('All Recent Orders') }}</h2>
                    <p class="text-[14px] text-base-content/50 mt-1">{{ __('Latest orders across all statuses') }}</p>
                </div>
                <a href="{{ route('dashboard.orders') }}" wire:navigate class="text-[11px] font-bold text-base-content/50 hover:text-primary transition-colors duration-150">
                    {{ __('View all →') }}
                </a>
            </div>

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
                        @forelse ($this->recentOrders as $order)
                            <tr class="hover:bg-base-200/40 transition-colors duration-150">
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="flex w-8 h-8 flex-shrink-0 items-center justify-center rounded-lg bg-base-200 text-[10px] font-bold text-base-content/60 uppercase">
                                            @if ($order->car)
                                                {{ substr($order->car->make->name, 0, 2) }}
                                            @else
                                                --
                                            @endif
                                        </div>
                                        <span class="font-medium">
                                            @if ($order->car)
                                                {{ $order->car->make->name }} {{ $order->car->carModel->name }}
                                            @else
                                                {{ __('Car Removed') }}
                                            @endif
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-3.5 text-base-content/60">
                                    {{ $order->car ? $order->car->year : '—' }}
                                </td>
                                <td class="px-6 py-3.5 font-bold">
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
                                        <x-ui.badge type="warning">{{ __('Pending') }}</x-ui.badge>
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
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-[13px] text-base-content/40">
                                    {{ __('No orders to display yet.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
