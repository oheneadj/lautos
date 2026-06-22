<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-[28px] font-semibold text-base-content leading-tight">{{ __('Notifications Hub') }}</h1>
            <p class="text-[14px] text-base-content/50 mt-1">{{ __('Stay updated on your orders, KYC status, and system alerts') }}</p>
        </div>
        <div class="flex items-center gap-3">
            @if ($notifications->whereNull('read_at')->count() > 0)
                <button wire:click="markAllAsRead" class="inline-flex items-center gap-2 rounded-xl bg-base-200 border border-base-content/10 px-[18px] py-[10px] text-[13px] font-medium text-base-content hover:bg-base-300 transition-all duration-150">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                    {{ __('Mark All as Read') }}
                </button>
            @endif
        </div>
    </div>

    {{-- Notifications List --}}
    @if ($notifications->isEmpty())
        <div class="bg-base-100 border border-base-content/5 rounded-xl p-14 text-center shadow-sm">
            <svg class="mx-auto w-12 h-12 text-base-content/20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0M3.124 7.5A8.969 8.969 0 0 1 5.292 3m13.416 0a8.969 8.969 0 0 1 2.168 4.5" /></svg>
            <p class="mt-3 text-[15px] font-bold text-base-content">{{ __('No notifications') }}</p>
            <p class="mt-1 text-[13px] text-base-content/40">{{ __('You\'re all caught up! New alerts will appear here.') }}</p>
        </div>
    @else
        <div class="bg-white border border-base-content/5 shadow-sm rounded-xl overflow-hidden divide-y divide-base-content/5">
            @foreach ($notifications as $notification)
                <div class="p-5 flex items-start gap-4 transition-colors {{ is_null($notification->read_at) ? 'bg-primary/5 hover:bg-primary/10' : 'hover:bg-base-200/50' }}">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 {{ is_null($notification->read_at) ? 'bg-primary text-white shadow-sm' : 'bg-base-200 text-base-content/60' }}">
                        @if (isset($notification->data['icon']) && $notification->data['icon'] === 'check')
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        @elseif (isset($notification->data['icon']) && $notification->data['icon'] === 'truck')
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg>
                        @elseif (isset($notification->data['icon']) && $notification->data['icon'] === 'document')
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                        @else
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0M3.124 7.5A8.969 8.969 0 0 1 5.292 3m13.416 0a8.969 8.969 0 0 1 2.168 4.5" /></svg>
                        @endif
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <h3 class="text-[14px] font-bold text-base-content {{ is_null($notification->read_at) ? '' : 'opacity-80' }}">
                                {{ $notification->data['title'] ?? __('Notification') }}
                            </h3>
                            <span class="text-[11px] font-medium text-base-content/40 whitespace-nowrap">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="mt-1 text-[13px] text-base-content/70 {{ is_null($notification->read_at) ? 'font-medium' : '' }}">
                            {{ $notification->data['message'] ?? '' }}
                        </p>
                        @if (isset($notification->data['action_url']) && isset($notification->data['action_text']))
                            <div class="mt-3">
                                <a href="{{ $notification->data['action_url'] }}" class="inline-flex items-center text-[12px] font-bold text-primary hover:underline">
                                    {{ $notification->data['action_text'] }}
                                    <svg class="ml-1 w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                                </a>
                            </div>
                        @endif
                    </div>
                    @if (is_null($notification->read_at))
                        <button wire:click="markAsRead('{{ $notification->id }}')" class="w-8 h-8 rounded-full hover:bg-base-300 text-base-content/40 hover:text-base-content flex items-center justify-center transition-colors" title="{{ __('Mark as read') }}">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        </button>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="pt-4">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
