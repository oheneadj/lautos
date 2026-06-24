<div>
    {{-- Floating bubble --}}
    <button
        type="button"
        wire:click="toggle"
        aria-label="{{ __('Support chat') }}"
        class="fixed bottom-5 right-5 z-40 flex items-center gap-2 rounded-full bg-primary pl-4 pr-5 py-3.5 text-white shadow-lg transition-transform hover:scale-105 sm:bottom-6 sm:right-6"
    >
        <svg class="w-5 h-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
        </svg>
        <span class="text-[13px] font-semibold whitespace-nowrap">{{ __('Support') }}</span>
    </button>

    <x-ui.slideover closeAction="toggle" show="showSlideOver">
            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-base-content/5">
                <h2 class="text-[15px] font-semibold text-base-content">{{ __('Support') }}</h2>
                <div class="flex items-center gap-4">
                    <a href="{{ route('dashboard.support') }}" wire:navigate class="text-[12px] font-medium text-base-content/50 hover:text-primary transition-colors">
                        {{ __('View All') }}
                    </a>
                    <button type="button" wire:click="toggle" class="text-base-content/40 hover:text-base-content transition-colors">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>

            {{-- Messages --}}
            <div class="flex-1 overflow-y-auto p-5 space-y-5 bg-base-200/30">
                @forelse ($this->threadMessages as $msg)
                    <div class="flex flex-col {{ $msg->is_admin ? 'items-start' : 'items-end' }}">
                        <div class="flex items-end gap-2 {{ $msg->is_admin ? 'flex-row' : 'flex-row-reverse' }}">
                            <div class="w-7 h-7 rounded-full bg-base-300 flex items-center justify-center flex-shrink-0 text-[10px] font-bold text-base-content/60">
                                {{ $msg->is_admin ? 'LA' : substr(Auth::user()->name, 0, 2) }}
                            </div>
                            <div class="max-w-[85%] rounded-2xl px-4 py-2.5 {{ $msg->is_admin ? 'bg-white border border-base-content/10 rounded-bl-sm shadow-sm' : 'bg-primary text-white rounded-br-sm shadow-sm' }}">
                                <p class="text-[13px] whitespace-pre-wrap">{{ $msg->message }}</p>
                            </div>
                        </div>
                        <span class="text-[10px] text-base-content/40 mt-1 px-9">{{ $msg->created_at->format('M d, H:i') }}</span>
                    </div>
                @empty
                    <p class="text-[13px] text-base-content/40 text-center mt-10">
                        {{ __("Send us a message and we'll get back to you.") }}
                    </p>
                @endforelse
            </div>

            {{-- Input / new-ticket form --}}
            @if ($activeTicket)
                @if ($activeTicket->status !== 'Closed')
                    <form wire:submit="sendMessage" class="p-4 border-t border-base-content/5 flex gap-2">
                        <textarea
                            wire:model="message"
                            rows="1"
                            class="flex-1 bg-base-200 border border-base-content/10 rounded-xl px-3 py-2.5 text-[13px] text-base-content placeholder:text-base-content/40 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all resize-none"
                            placeholder="{{ __('Type your message...') }}"
                            required
                        ></textarea>
                        <button type="submit" wire:loading.attr="disabled" class="w-10 h-10 flex-shrink-0 rounded-xl bg-primary text-white flex items-center justify-center hover:brightness-110 transition-colors disabled:opacity-50">
                            <svg class="w-4 h-4 ml-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>
                        </button>
                    </form>
                    @error('message') <span class="px-4 pb-3 -mt-2 text-[12px] text-error block">{{ $message }}</span> @enderror
                @else
                    <div class="p-4 border-t border-base-content/5 text-center">
                        <p class="text-[13px] text-base-content/50">
                            {{ __('This conversation is closed.') }}
                            <a href="{{ route('dashboard.support') }}" wire:navigate class="text-primary font-medium hover:underline">{{ __('Start a new one') }}</a>
                        </p>
                    </div>
                @endif
            @else
                <form wire:submit="startTicket" class="p-4 border-t border-base-content/5 space-y-3">
                    <x-ui.input
                        label="{{ __('Subject') }}"
                        id="bubble-subject"
                        wire:model="subject"
                        placeholder="{{ __('What can we help with?') }}"
                        required
                    />
                    <x-ui.textarea
                        label="{{ __('Message') }}"
                        id="bubble-message"
                        wire:model="message"
                        placeholder="{{ __('Tell us more...') }}"
                        rows="3"
                        required
                    />
                    <x-ui.button type="submit" variant="primary" wire:loading.attr="disabled" class="w-full justify-center">
                        <span wire:loading.remove wire:target="startTicket">{{ __('Send') }}</span>
                        <span wire:loading wire:target="startTicket">{{ __('Sending...') }}</span>
                    </x-ui.button>
                </form>
            @endif
    </x-ui.slideover>
</div>
