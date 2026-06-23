<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('dashboard.support') }}" wire:navigate class="text-base-content/40 hover:text-base-content transition-colors">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                </a>
                <span class="text-[13px] font-medium text-base-content/50 uppercase tracking-widest">TICKET #{{ strtoupper(substr($ticket->uuid, 0, 8)) }}</span>
                @if ($ticket->status === 'Open')
                    <x-ui.badge type="primary">{{ __('Open') }}</x-ui.badge>
                @elseif ($ticket->status === 'In Progress')
                    <x-ui.badge type="warning">{{ __('In Progress') }}</x-ui.badge>
                @else
                    <x-ui.badge type="success">{{ __('Closed') }}</x-ui.badge>
                @endif
            </div>
            <h1 class="text-[28px] font-semibold text-base-content leading-tight">{{ $ticket->subject }}</h1>
        </div>
    </div>

    <x-ui.card class="overflow-hidden flex flex-col h-[600px]">
        {{-- Messages Area --}}
        <div class="flex-1 overflow-y-auto p-6 space-y-6 bg-base-200/30">
            @foreach ($messages as $msg)
                <div class="flex flex-col {{ $msg->is_admin ? 'items-start' : 'items-end' }}">
                    <div class="flex items-end gap-2 {{ $msg->is_admin ? 'flex-row' : 'flex-row-reverse' }}">
                        <div class="w-8 h-8 rounded-full bg-base-300 flex items-center justify-center flex-shrink-0 text-[11px] font-bold text-base-content/60">
                            {{ $msg->is_admin ? 'LA' : substr(Auth::user()->name, 0, 2) }}
                        </div>
                        <div class="max-w-[80%] rounded-2xl px-5 py-3 {{ $msg->is_admin ? 'bg-white border border-base-content/10 rounded-bl-sm shadow-sm' : 'bg-primary text-white rounded-br-sm shadow-sm' }}">
                            <p class="text-[14px] whitespace-pre-wrap">{{ $msg->message }}</p>
                            @if ($msg->attachment_path)
                                <div class="mt-3">
                                    <a href="{{ Storage::url($msg->attachment_path) }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg {{ $msg->is_admin ? 'bg-base-200 text-base-content hover:bg-base-300' : 'bg-white/20 text-white hover:bg-white/30' }} text-[12px] font-medium transition-colors">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" /></svg>
                                        {{ __('View Attachment') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <span class="text-[11px] text-base-content/40 mt-1 px-10">{{ $msg->created_at->format('M d, H:i') }}</span>
                </div>
            @endforeach
        </div>

        {{-- Input Area --}}
        @if ($ticket->status !== 'Closed')
            <div class="p-4 bg-white border-t border-base-content/5">
                <form wire:submit="sendMessage" class="flex gap-3 items-end">
                    <div class="flex-1 relative">
                        <textarea 
                            wire:model="message" 
                            rows="2" 
                            class="w-full bg-base-200 border border-base-content/10 rounded-xl px-4 py-3 text-[14px] text-base-content placeholder:text-base-content/40 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all resize-none"
                            placeholder="{{ __('Type your message...') }}"
                            required
                        ></textarea>
                    </div>
                    
                    <div class="flex gap-2">
                        <div class="relative overflow-hidden group">
                            <input type="file" wire:model="attachment" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*,.pdf,.doc,.docx">
                            <div class="w-11 h-11 rounded-xl bg-base-200 border border-base-content/10 text-base-content/50 flex items-center justify-center group-hover:bg-base-300 transition-colors">
                                <svg wire:loading.remove wire:target="attachment" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" /></svg>
                                <svg wire:loading wire:target="attachment" class="w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            </div>
                        </div>

                        <button type="submit" wire:loading.attr="disabled" class="w-11 h-11 rounded-xl bg-primary text-white flex items-center justify-center hover:brightness-110 transition-colors shadow-sm disabled:opacity-50">
                            <svg class="w-5 h-5 ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>
                        </button>
                    </div>
                </form>
                @if ($attachment)
                    <div class="mt-2 text-[12px] text-success flex items-center gap-1">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        {{ __('Attachment ready: ') }} {{ $attachment->getClientOriginalName() }}
                    </div>
                @endif
                @error('attachment') <span class="text-[12px] text-error mt-1 block">{{ $message }}</span> @enderror
            </div>
        @else
            <div class="p-6 bg-base-200 border-t border-base-content/5 text-center">
                <p class="text-[14px] text-base-content/50 font-medium">{{ __('This ticket is closed. If you need further assistance, please open a new ticket.') }}</p>
            </div>
        @endif
    </x-ui.card>
</div>
