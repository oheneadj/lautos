<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-[28px] font-semibold text-base-content leading-tight">{{ __('Support & Messages') }}</h1>
            <p class="text-[14px] text-base-content/50 mt-1">{{ __('Contact our support team regarding your orders or account') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <button wire:click="$set('showCreateModal', true)" class="inline-flex items-center gap-2 rounded-xl bg-primary px-[18px] py-[10px] text-[13px] font-medium text-white hover:brightness-110 transition-all duration-150 shadow-sm">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                {{ __('New Ticket') }}
            </button>
        </div>
    </div>

    {{-- Tickets Table --}}
    @if ($tickets->isEmpty())
        <div class="bg-base-100 border border-base-content/5 rounded-xl p-14 text-center shadow-sm">
            <svg class="mx-auto w-12 h-12 text-base-content/20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 0 1 .778-.332 48.294 48.294 0 0 0 5.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" /></svg>
            <p class="mt-3 text-[15px] font-bold text-base-content">{{ __('No support tickets') }}</p>
            <p class="mt-1 text-[13px] text-base-content/40">{{ __('You haven\'t opened any support tickets yet.') }}</p>
        </div>
    @else
        <div class="bg-white border border-base-content/5 shadow-sm rounded-xl flex flex-col overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-base-200 border-b border-base-content/5">
                        <tr>
                            <th class="px-6 py-3 text-[11px] font-bold uppercase tracking-widest text-base-content/60">{{ __('TICKET ID') }}</th>
                            <th class="px-6 py-3 text-[11px] font-bold uppercase tracking-widest text-base-content/60">{{ __('SUBJECT') }}</th>
                            <th class="px-6 py-3 text-[11px] font-bold uppercase tracking-widest text-base-content/60">{{ __('STATUS') }}</th>
                            <th class="px-6 py-3 text-[11px] font-bold uppercase tracking-widest text-base-content/60">{{ __('LAST UPDATED') }}</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="text-[13px] text-base-content divide-y divide-base-content/5">
                        @foreach ($tickets as $ticket)
                            <tr class="hover:bg-base-200/40 transition-colors duration-150">
                                <td class="px-6 py-4 font-mono text-[12px] font-medium text-base-content/70">
                                    #{{ strtoupper(substr($ticket->uuid, 0, 8)) }}
                                </td>
                                <td class="px-6 py-4 font-medium text-base-content">
                                    {{ $ticket->subject }}
                                </td>
                                <td class="px-6 py-4">
                                    @if ($ticket->status === 'Open')
                                        <x-ui.badge type="primary">{{ __('Open') }}</x-ui.badge>
                                    @elseif ($ticket->status === 'In Progress')
                                        <x-ui.badge type="warning">{{ __('In Progress') }}</x-ui.badge>
                                    @else
                                        <x-ui.badge type="success">{{ __('Closed') }}</x-ui.badge>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-[12px] text-base-content/40 font-medium whitespace-nowrap">
                                    {{ $ticket->updated_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('dashboard.support.show', $ticket->uuid) }}" wire:navigate class="inline-flex items-center gap-1 text-[11px] font-bold text-primary hover:underline">
                                        {{ __('View Details') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="pt-4">
            {{ $tickets->links() }}
        </div>
    @endif

    {{-- Create Ticket Modal --}}
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black/40 backdrop-blur-[2px]" wire:click="$set('showCreateModal', false)"></div>
            <div class="relative z-10 w-full max-w-lg rounded-2xl bg-base-100 p-6 shadow-2xl border border-base-content/10">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-base-content">{{ __('Open New Ticket') }}</h2>
                    <button wire:click="$set('showCreateModal', false)" class="text-base-content/40 hover:text-base-content">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                
                <form wire:submit="createTicket" class="space-y-4">
                    <x-ui.input
                        label="{{ __('Subject') }}"
                        id="subject"
                        wire:model="subject"
                        placeholder="{{ __('Brief description of the issue') }}"
                        required
                    />
                    
                    <x-ui.textarea
                        label="{{ __('Message') }}"
                        id="message"
                        wire:model="message"
                        placeholder="{{ __('Please describe your issue in detail...') }}"
                        rows="5"
                        required
                    />

                    <div class="flex justify-end gap-3 mt-6">
                        <x-ui.button type="button" variant="outline" wire:click="$set('showCreateModal', false)">{{ __('Cancel') }}</x-ui.button>
                        <x-ui.button type="submit" variant="primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="createTicket">{{ __('Submit Ticket') }}</span>
                            <span wire:loading wire:target="createTicket">{{ __('Submitting...') }}</span>
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
