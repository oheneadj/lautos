    <div class="space-y-6" wire:poll.30s="refreshOrder">
        {{-- Back Link & Header --}}
        <div>
            <a href="{{ route('dashboard.orders') }}" wire:navigate class="inline-flex items-center gap-2 text-[11px] font-bold uppercase tracking-widest text-base-content/60 hover:text-error transition-colors mb-4">
                <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
                {{ __('Back to Orders') }}
            </a>
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                    <h1 class="text-[28px] font-semibold text-base-content leading-tight">
                        @if ($order->car)
                            {{ $order->car->year }} {{ $order->car->make->name }} {{ $order->car->carModel->name }}
                        @else
                            {{ __('Order Details') }}
                        @endif
                    </h1>
                    <p class="text-[14px] text-base-content/50 mt-1">{{ __('Order placed') }}: {{ $order->created_at->format('d F, Y') }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <x-ui.badge :type="$order->status->colour()">
                        {{ $order->status->label() }}
                    </x-ui.badge>
                </div>
            </div>
        </div>

        {{-- Demurrage Warning --}}
        @if ($this->showDemurrageWarning)
            <x-ui.alert type="warning" title="Important: Clearing Required">
                {{ __('Your car has arrived at Tema Port. Please arrange customs clearance promptly to avoid demurrage and storage penalties.') }}
            </x-ui.alert>
        @endif

        <div class="grid gap-4 lg:grid-cols-3">
            {{-- Left: Timeline & Payment (2 cols) --}}
            <div class="lg:col-span-2 space-y-4">
                {{-- Shipment Timeline --}}
                <div class="bg-white border border-base-content/5 rounded-xl shadow-sm">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-base-content/5">
                        <h2 class="text-lg font-semibold text-base-content">{{ __('Shipment Timeline') }}</h2>
                    </div>
                    <div class="p-6">
                        <div class="relative">
                            @foreach ($this->pipeline as $index => $stage)
                                <div class="flex gap-4 {{ !$loop->last ? 'pb-7' : '' }}">
                                    {{-- Timeline Dot & Line --}}
                                    <div class="relative flex flex-col items-center">
                                        @if ($stage['completed'])
                                            <div class="flex w-7 h-7 items-center justify-center rounded-full bg-success text-white">
                                                <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                            </div>
                                        @elseif ($stage['current'])
                                            <div class="flex w-7 h-7 items-center justify-center rounded-full bg-primary text-white ring-4 ring-primary/15 animate-pulse">
                                                <div class="w-2 h-2 rounded-full bg-white"></div>
                                            </div>
                                        @else
                                            <div class="flex w-7 h-7 items-center justify-center rounded-full border-2 border-base-300 bg-base-200">
                                                <div class="w-1.5 h-1.5 rounded-full bg-base-300"></div>
                                            </div>
                                        @endif

                                        {{-- Connecting Line --}}
                                        @if (!$loop->last)
                                            <div class="absolute top-7 h-full w-0.5 {{ $stage['completed'] ? 'bg-success' : 'bg-base-200' }}"></div>
                                        @endif
                                    </div>

                                    {{-- Stage Content --}}
                                    <div class="flex-1 pt-0.5">
                                        <p class="text-[13px] font-semibold {{ $stage['current'] ? 'text-primary' : ($stage['completed'] ? 'text-base-content' : 'text-base-content/40') }}">
                                            {{ $stage['label'] }}
                                        </p>
                                        @if ($stage['date'])
                                            <p class="text-[11px] font-bold uppercase tracking-widest text-base-content/40 mt-1">{{ $stage['date'] }}</p>
                                        @elseif ($stage['current'])
                                            <p class="text-[11px] font-bold uppercase tracking-widest text-primary mt-1">{{ __('Current stage') }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Payment Proof Upload --}}
                @if ($this->canUploadProof)
                    <div class="bg-white border border-base-content/5 rounded-xl shadow-sm">
                        <div class="px-6 py-4 border-b border-base-content/5">
                            <h2 class="text-lg font-semibold text-base-content">{{ __('Upload Payment Proof') }}</h2>
                        </div>
                        <div class="p-6">
                            <form wire:submit="uploadPaymentProof" class="space-y-4">
                                <div>
                                    <label for="paymentProofFile" class="text-[13px] font-medium text-base-content block mb-1">
                                        {{ __('Receipt / Screenshot') }} <span class="text-error ml-0.5">*</span>
                                    </label>
                                    <input
                                        type="file"
                                        id="paymentProofFile"
                                        wire:model="paymentProofFile"
                                        accept=".jpg,.jpeg,.png,.pdf"
                                        class="mt-1.5 block w-full text-[13px] text-base-content/60 file:mr-3 file:py-[6px] file:px-3 file:rounded-lg file:border file:border-base-content/10 file:text-[12px] file:font-bold file:bg-base-100 file:text-base-content hover:file:bg-base-200 file:cursor-pointer transition-colors"
                                    />
                                    @error('paymentProofFile')
                                        <span class="text-xs text-error flex items-center gap-1 mt-1.5">⚠ {{ $message }}</span>
                                    @enderror
                                </div>

                                <x-ui.textarea
                                    label="Transaction Note (optional)"
                                    id="transactionNote"
                                    wire:model="transactionNote"
                                    placeholder="e.g. MoMo transaction ID or bank reference"
                                    rows="2"
                                />

                                <x-ui.button type="submit" variant="primary" wire:loading.attr="disabled">
                                    {{ __('Upload Proof') }}
                                </x-ui.button>
                            </form>
                        </div>
                    </div>
                @endif

                {{-- Uploaded Proofs --}}
                @if ($order->paymentProofs->isNotEmpty())
                    <div class="bg-white border border-base-content/5 rounded-xl shadow-sm">
                        <div class="px-6 py-4 border-b border-base-content/5">
                            <h2 class="text-lg font-semibold text-base-content">{{ __('Payment Proofs Submitted') }}</h2>
                        </div>
                        <div class="divide-y divide-base-content/5">
                            @foreach ($order->paymentProofs as $proof)
                                <div class="flex items-center justify-between px-6 py-3.5">
                                    <div>
                                        <p class="text-[13px] font-medium text-base-content">{{ basename($proof->file_path) }}</p>
                                        <p class="text-[12px] text-base-content/40 font-medium mt-0.5">{{ $proof->created_at->format('d M, Y h:i A') }}</p>
                                        @if ($proof->note)
                                            <p class="text-[11px] text-base-content/60 mt-1 italic">"{{ $proof->note }}"</p>
                                        @endif
                                    </div>
                                    <x-ui.badge type="success">{{ __('Submitted') }}</x-ui.badge>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Right: Order Summary & Payment Info (1 col) --}}
            <div class="space-y-4">
                {{-- Order Summary --}}
                <div class="bg-white border border-base-content/5 rounded-xl shadow-sm">
                    <div class="px-5 py-4 border-b border-base-content/5">
                        <h2 class="text-lg font-semibold text-base-content">{{ __('Order Summary') }}</h2>
                    </div>
                    <div class="p-5 space-y-4">
                        @if ($order->car && $order->car->images->first())
                            <div class="aspect-video rounded-lg bg-base-200 overflow-hidden">
                                <img src="{{ Storage::url($order->car->images->first()->path) }}" alt="" class="size-full object-cover" />
                            </div>
                        @endif

                        <div class="space-y-2.5 text-[13px]">
                            <div class="flex justify-between">
                                <span class="text-base-content/60">{{ __('Car Price') }}</span>
                                <span class="font-medium text-base-content">${{ number_format($order->price_usd_cents / 100, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-base-content/60">{{ __('Shipping') }}</span>
                                <span class="font-medium text-base-content">${{ number_format($order->shipping_cost_usd_cents / 100, 2) }}</span>
                            </div>
                            <div class="h-px bg-base-content/5"></div>
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-base-content">{{ __('Total (USD)') }}</span>
                                <span class="font-bold text-base-content text-[15px]">${{ number_format($order->total_usd_cents / 100, 2) }}</span>
                            </div>
                        </div>

                        @if ($order->estimated_arrival_date)
                            <div class="mt-3 rounded-lg bg-info/10 px-4 py-3 border border-info/20">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-info">{{ __('Est. Arrival') }}</p>
                                <p class="text-[13px] font-bold text-base-content mt-0.5">{{ $order->estimated_arrival_date->format('d F, Y') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Payment Instructions --}}
                @if ($this->canUploadProof)
                    <div class="bg-white border border-base-content/5 rounded-xl shadow-sm">
                        <div class="px-5 py-4 border-b border-base-content/5">
                            <h2 class="text-lg font-semibold text-base-content">{{ __('Payment Instructions') }}</h2>
                        </div>
                        <div class="p-5 space-y-5">
                            {{-- Bank Transfer --}}
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-base-content/40 mb-2">{{ __('Bank Transfer') }}</p>
                                <div class="space-y-2 text-[13px]">
                                    <div class="flex justify-between"><span class="text-base-content/60">{{ __('Bank') }}</span><span class="font-medium text-base-content">{{ $this->paymentInfo['bank_name'] }}</span></div>
                                    <div class="flex justify-between"><span class="text-base-content/60">{{ __('Name') }}</span><span class="font-medium text-base-content">{{ $this->paymentInfo['account_name'] }}</span></div>
                                    <div class="flex justify-between"><span class="text-base-content/60">{{ __('Account') }}</span><span class="font-medium text-base-content">{{ $this->paymentInfo['account_number'] }}</span></div>
                                </div>
                            </div>

                            <div class="h-px bg-base-content/5"></div>

                            {{-- MoMo --}}
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-base-content/40 mb-2">{{ __('Mobile Money') }}</p>
                                <div class="space-y-2 text-[13px]">
                                    <div class="flex justify-between"><span class="text-base-content/60">{{ __('Number') }}</span><span class="font-medium text-base-content">{{ $this->paymentInfo['momo_number'] }}</span></div>
                                    <div class="flex justify-between"><span class="text-base-content/60">{{ __('Name') }}</span><span class="font-medium text-base-content">{{ $this->paymentInfo['momo_name'] }}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
