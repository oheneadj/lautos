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
                <a href="{{ route('dashboard.notifications') }}" wire:navigate class="inline-flex items-center gap-2 rounded-xl border border-base-content/10 bg-base-100 px-[18px] py-[10px] text-[13px] font-medium text-base-content hover:bg-base-200 transition-all duration-150">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>
                    {{ __('Notifications') }}
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

            {{-- Total Spend --}}
            <div class="bg-base-100 border border-base-content/5 rounded-xl p-4 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-accent/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182 1.106-.879 2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                </div>
                <div>
                    <p class="text-[20px] font-bold text-base-content">${{ number_format($this->totalSpendUsdCents / 100, 0) }}</p>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-base-content/40">{{ __('TOTAL SPEND') }}</p>
                </div>
            </div>
        </div>

        {{-- Middle Row: 4 columns --}}
        <div class="grid gap-4 lg:grid-cols-4">
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
                <div class="px-5 pb-6">
                    @if (empty($this->ordersByStage))
                        <div class="flex flex-col items-center justify-center py-8 text-center">
                            <div class="w-12 h-12 rounded-full bg-base-200 flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-base-content/20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                            <p class="text-[13px] font-medium text-base-content/40">{{ __('Your pipeline is empty.') }}</p>
                        </div>
                    @else
                        <div class="relative ml-2 space-y-3 mt-2">
                            <!-- Vertical connector line -->
                            <div class="absolute left-1.5 top-2.5 bottom-2.5 w-[2px] bg-base-content/5 rounded-full"></div>
                            
                            @foreach ($this->ordersByStage as $stage)
                                @php
                                    $dotColor = match($stage['colour']) {
                                        'success' => 'bg-success',
                                        'warning' => 'bg-warning',
                                        'info'    => 'bg-info',
                                        'danger'  => 'bg-error',
                                        default   => 'bg-primary',
                                    };
                                    $badgeStyle = match($stage['colour']) {
                                        'success' => 'bg-success/10 text-success',
                                        'warning' => 'bg-warning/20 text-warning-content dark:text-warning',
                                        'info'    => 'bg-info/10 text-info',
                                        'danger'  => 'bg-error/10 text-error',
                                        default   => 'bg-primary/10 text-primary',
                                    };
                                @endphp
                                <div class="relative flex items-center gap-4 group">
                                    <!-- Node Dot -->
                                    <div class="w-3.5 h-3.5 rounded-full {{ $dotColor }} relative z-10 ring-4 ring-base-100 shadow-sm group-hover:scale-125 group-hover:ring-base-200 transition-all duration-300"></div>
                                    
                                    <!-- Stage Card -->
                                    <div class="flex-1 flex items-center justify-between border border-base-content/5 rounded-xl px-4 py-2.5 hover:bg-base-200/50 hover:border-base-content/10 transition-all duration-200">
                                        <span class="text-[13px] font-semibold text-base-content">{{ $stage['label'] }}</span>
                                        <div class="flex items-center justify-center min-w-[28px] h-[28px] rounded-lg {{ $badgeStyle }} font-bold text-[12px] shadow-sm">
                                            {{ $stage['count'] }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Dedicated Broker --}}
            <div class="bg-base-100 border border-base-content/5 rounded-xl flex flex-col relative overflow-hidden">
                <div class="h-16 bg-primary/10 w-full absolute top-0 left-0"></div>
                <div class="px-5 pt-8 pb-5 flex-1 flex flex-col items-center text-center relative z-10 mt-2">
                    <div class="w-16 h-16 rounded-full bg-white border-4 border-base-100 shadow-sm flex items-center justify-center overflow-hidden mb-3">
                        <img src="https://ui-avatars.com/api/?name=Alexander+Davis&background=0D8ABC&color=fff&size=128" alt="Broker" class="w-full h-full object-cover">
                    </div>
                    <h3 class="text-[16px] font-bold text-base-content">{{ __('Alexander Davis') }}</h3>
                    <p class="text-[12px] font-medium text-base-content/50 mb-4">{{ __('Senior Import Specialist') }}</p>
                    
                    <p class="text-[13px] text-base-content/70 mb-5 leading-relaxed">
                        {{ __("I'm your dedicated agent. Reach out anytime with questions about your imports.") }}
                    </p>

                    <div class="mt-auto w-full grid grid-cols-2 gap-2">
                        <a href="mailto:support@livingstonautos.com" class="flex items-center justify-center gap-2 py-2 rounded-lg bg-base-200 hover:bg-base-300 transition-colors text-[12px] font-bold text-base-content">
                            <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
                            Email
                        </a>
                        <a href="#" class="flex items-center justify-center gap-2 py-2 rounded-lg bg-[#25D366]/10 text-[#25D366] hover:bg-[#25D366]/20 transition-colors text-[12px] font-bold">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                            WhatsApp
                        </a>
                    </div>
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
                                            {{ $order->car->year }} {{ $order->car->make->name }} {{ $order->car->carModel->name }}
                                        @else
                                            {{ __('Car Removed') }}
                                        @endif
                                    </p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <x-ui.badge :type="$order->status->colour()">
                                            {{ $order->status->label() }}
                                        </x-ui.badge>
                                        @if ($order->paymentProofs && $order->paymentProofs->count() > 0)
                                            <x-ui.badge type="success">{{ __('Paid') }}</x-ui.badge>
                                        @else
                                            <x-ui.badge type="warning">{{ __('Pending') }}</x-ui.badge>
                                        @endif
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

        {{-- Bottom Row: Watchlist, Tickets, Blog --}}
        <div class="grid gap-4 lg:grid-cols-3">
            {{-- Watchlist Preview --}}
            <div class="bg-base-100 border border-base-content/5 rounded-xl flex flex-col">
                <div class="flex items-center justify-between px-5 pt-5 pb-3 border-b border-base-content/5">
                    <h2 class="text-lg font-semibold text-base-content">{{ __('Recent Saved Cars') }}</h2>
                    <a href="{{ route('dashboard.saved-cars') }}" wire:navigate class="text-[11px] font-bold text-base-content/50 hover:text-primary transition-colors duration-150">{{ __('View all →') }}</a>
                </div>
                <div class="p-4 flex-1 flex flex-col gap-3">
                    @forelse ($this->recentSavedCars as $car)
                        <a href="{{ route('cars.show', $car->slug) }}" wire:navigate class="relative h-24 rounded-xl overflow-hidden group border border-base-content/5 shadow-sm">
                            @if ($car->images->isNotEmpty())
                                <img src="{{ Storage::url($car->images->first()->path) }}" alt="{{ $car->make->name }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
                            @else
                                <div class="absolute inset-0 bg-base-200"></div>
                            @endif
                            <div class="absolute bottom-0 left-0 w-full p-3 flex items-end justify-between">
                                <div>
                                    <p class="text-[14px] font-bold text-white drop-shadow-md">{{ $car->make->name }} {{ $car->carModel->name }}</p>
                                    <p class="text-[11px] font-semibold text-white/80 drop-shadow-md mt-0.5">{{ $car->year ?? 'N/A' }}</p>
                                </div>
                                <p class="text-[13px] font-black text-white drop-shadow-md bg-primary/80 px-2 py-0.5 rounded-md">${{ number_format($car->price_usd_cents / 100, 0) }}</p>
                            </div>
                        </a>
                    @empty
                        <div class="flex-1 flex flex-col items-center justify-center text-center py-6">
                            <p class="text-[13px] text-base-content/40">{{ __('No saved cars yet.') }}</p>
                            <a href="{{ route('cars.index') }}" wire:navigate class="mt-2 text-[12px] font-bold text-primary hover:underline">{{ __('Browse Inventory') }}</a>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Active Support Tickets --}}
            <div class="bg-base-100 border border-base-content/5 rounded-xl flex flex-col">
                <div class="flex items-center justify-between px-5 pt-5 pb-3 border-b border-base-content/5">
                    <h2 class="text-lg font-semibold text-base-content">{{ __('Recent Support Tickets') }}</h2>
                    <a href="{{ route('dashboard.support') }}" wire:navigate class="text-[11px] font-bold text-base-content/50 hover:text-primary transition-colors duration-150">{{ __('View all →') }}</a>
                </div>
                <div class="p-0 flex-1 flex flex-col">
                    @forelse ($this->activeTickets as $ticket)
                        <a href="{{ route('dashboard.support.show', $ticket->uuid) }}" wire:navigate class="flex items-start justify-between gap-3 px-5 py-4 border-b border-base-content/5 hover:bg-base-200/50 transition-colors last:border-0 group">
                            <div class="flex-1 min-w-0">
                                <p class="text-[13px] font-bold text-base-content truncate group-hover:text-primary transition-colors duration-150">{{ $ticket->subject }}</p>
                                <p class="text-[11px] font-medium text-base-content/40 mt-1">{{ $ticket->created_at->format('M d, Y') }}</p>
                            </div>
                            <x-ui.badge :type="$ticket->status === 'Open' ? 'warning' : 'neutral'">
                                {{ $ticket->status }}
                            </x-ui.badge>
                        </a>
                    @empty
                        <div class="flex-1 flex flex-col items-center justify-center text-center py-8">
                            <p class="text-[13px] text-base-content/40">{{ __('No active support tickets.') }}</p>
                            <a href="{{ route('dashboard.support') }}" wire:navigate class="mt-2 text-[12px] font-bold text-primary hover:underline">{{ __('Open a Ticket') }}</a>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Educational Resources --}}
            <div class="bg-base-100 border border-base-content/5 rounded-xl flex flex-col">
                <div class="flex items-center justify-between px-5 pt-5 pb-3 border-b border-base-content/5">
                    <h2 class="text-lg font-semibold text-base-content">{{ __('Guides & Updates') }}</h2>
                    <a href="{{ route('blog.index') }}" wire:navigate class="text-[11px] font-bold text-base-content/50 hover:text-primary transition-colors duration-150">{{ __('Read more →') }}</a>
                </div>
                <div class="p-4 flex-1 flex flex-col gap-4">
                    @forelse ($this->latestBlogPosts as $post)
                        <a href="{{ route('blog.show', $post->slug) }}" wire:navigate class="flex flex-col group">
                            <div class="w-full h-44 relative overflow-hidden rounded-xl bg-base-200">
                                @if ($post->cover_image_path)
                                    <img src="{{ Storage::url($post->cover_image_path) }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @endif
                                <div class="absolute top-2 left-2 px-2 py-1 bg-black/60 backdrop-blur-md rounded-md text-[10px] font-bold text-white tracking-wider uppercase shadow-sm">
                                    {{ $post->category->name ?? __('Update') }}
                                </div>
                            </div>
                            <div class="pt-3">
                                <h3 class="text-[14px] font-bold text-base-content leading-snug group-hover:text-primary transition-colors duration-200 line-clamp-2">{{ $post->title }}</h3>
                                <p class="text-[11px] font-medium text-base-content/40 mt-1.5">{{ $post->published_at->format('M d, Y') }}</p>
                            </div>
                        </a>
                    @empty
                        <div class="flex-1 flex items-center justify-center text-center py-6">
                            <p class="text-[13px] text-base-content/40">{{ __('Check back later for updates.') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
