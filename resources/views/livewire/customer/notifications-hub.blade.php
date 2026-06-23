<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-[28px] font-semibold text-base-content leading-tight">{{ __('Notifications') }}</h1>
            <p class="text-[14px] text-base-content/50 mt-1">{{ __('Stay updated on your orders, payments, and account activity.') }}</p>
        </div>
        <div class="flex items-center gap-3">
            @if ($unreadCount > 0)
                <button wire:click="markAllAsRead" class="inline-flex items-center gap-2 rounded-xl bg-primary px-[18px] py-[10px] text-[13px] font-bold text-white hover:bg-primary/90 transition-all duration-150 cursor-pointer">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    {{ __('Mark All as Read') }}
                </button>
            @endif
            <a href="{{ route('dashboard.index') }}" wire:navigate class="inline-flex items-center gap-2 rounded-xl border border-base-content/10 bg-base-100 px-[18px] py-[10px] text-[13px] font-medium text-base-content hover:bg-base-200 transition-all duration-150">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
                {{ __('Dashboard') }}
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-base-100 border border-base-content/5 rounded-xl p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>
                </div>
                <div>
                    <p class="text-[22px] font-bold text-base-content leading-none">{{ $totalCount }}</p>
                    <p class="text-[11px] font-medium text-base-content/40 mt-1">{{ __('Total') }}</p>
                </div>
            </div>
        </div>
        <div class="bg-base-100 border border-base-content/5 rounded-xl p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-warning/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-warning" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M5.25 9a6.75 6.75 0 0 1 13.5 0v.75c0 2.123.8 4.057 2.118 5.52a.75.75 0 0 1-.297 1.206c-1.544.57-3.16.99-4.831 1.243a3.75 3.75 0 1 1-7.48 0 24.585 24.585 0 0 1-4.831-1.244.75.75 0 0 1-.298-1.205A8.217 8.217 0 0 0 5.25 9.75V9Z" /></svg>
                </div>
                <div>
                    <p class="text-[22px] font-bold text-base-content leading-none">{{ $unreadCount }}</p>
                    <p class="text-[11px] font-medium text-base-content/40 mt-1">{{ __('Unread') }}</p>
                </div>
            </div>
        </div>
        <div class="bg-base-100 border border-base-content/5 rounded-xl p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-success/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-success" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                </div>
                <div>
                    <p class="text-[22px] font-bold text-base-content leading-none">{{ $totalCount - $unreadCount }}</p>
                    <p class="text-[11px] font-medium text-base-content/40 mt-1">{{ __('Read') }}</p>
                </div>
            </div>
        </div>
        <div class="bg-base-100 border border-base-content/5 rounded-xl p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-info/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-info" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" /></svg>
                </div>
                <div>
                    <p class="text-[22px] font-bold text-base-content leading-none">
                        {{ $notifications->count() > 0 ? $notifications->count() : '—' }}
                    </p>
                    <p class="text-[11px] font-medium text-base-content/40 mt-1">{{ __('Showing') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-base-100 border border-base-content/5 rounded-xl p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            {{-- Read Status Filter --}}
            <div class="flex items-center gap-1 bg-base-200/50 rounded-lg p-1">
                <button wire:click="setFilter('all')"
                    class="px-4 py-2 text-[12px] font-bold rounded-md transition-all duration-150 cursor-pointer {{ $filter === 'all' ? 'bg-base-100 text-base-content shadow-sm' : 'text-base-content/50 hover:text-base-content' }}">
                    {{ __('All') }}
                </button>
                <button wire:click="setFilter('unread')"
                    class="px-4 py-2 text-[12px] font-bold rounded-md transition-all duration-150 flex items-center gap-1.5 cursor-pointer {{ $filter === 'unread' ? 'bg-base-100 text-base-content shadow-sm' : 'text-base-content/50 hover:text-base-content' }}">
                    {{ __('Unread') }}
                    @if ($unreadCount > 0)
                        <span class="inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full bg-primary text-white text-[10px] font-bold">{{ $unreadCount }}</span>
                    @endif
                </button>
                <button wire:click="setFilter('read')"
                    class="px-4 py-2 text-[12px] font-bold rounded-md transition-all duration-150 cursor-pointer {{ $filter === 'read' ? 'bg-base-100 text-base-content shadow-sm' : 'text-base-content/50 hover:text-base-content' }}">
                    {{ __('Read') }}
                </button>
            </div>

            {{-- Category Filter --}}
            <div class="flex items-center gap-1 bg-base-200/50 rounded-lg p-1">
                <button wire:click="setCategory('all')"
                    class="px-4 py-2 text-[12px] font-bold rounded-md transition-all duration-150 cursor-pointer {{ $category === 'all' ? 'bg-base-100 text-base-content shadow-sm' : 'text-base-content/50 hover:text-base-content' }}">
                    {{ __('All Types') }}
                </button>
                <button wire:click="setCategory('orders')"
                    class="px-4 py-2 text-[12px] font-bold rounded-md transition-all duration-150 flex items-center gap-1.5 cursor-pointer {{ $category === 'orders' ? 'bg-base-100 text-base-content shadow-sm' : 'text-base-content/50 hover:text-base-content' }}">
                    <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg>
                    {{ __('Orders') }}
                </button>
                <button wire:click="setCategory('payments')"
                    class="px-4 py-2 text-[12px] font-bold rounded-md transition-all duration-150 flex items-center gap-1.5 cursor-pointer {{ $category === 'payments' ? 'bg-base-100 text-base-content shadow-sm' : 'text-base-content/50 hover:text-base-content' }}">
                    <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" /></svg>
                    {{ __('Payments') }}
                </button>
                <button wire:click="setCategory('kyc')"
                    class="px-4 py-2 text-[12px] font-bold rounded-md transition-all duration-150 flex items-center gap-1.5 cursor-pointer {{ $category === 'kyc' ? 'bg-base-100 text-base-content shadow-sm' : 'text-base-content/50 hover:text-base-content' }}">
                    <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" /></svg>
                    {{ __('KYC') }}
                </button>
            </div>
        </div>
    </div>

    {{-- Notifications List --}}
    @if ($notifications->isEmpty())
        <div class="bg-base-100 border border-base-content/5 rounded-xl p-14 text-center">
            <div class="w-16 h-16 rounded-full bg-base-200 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-base-content/20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0M3.124 7.5A8.969 8.969 0 0 1 5.292 3m13.416 0a8.969 8.969 0 0 1 2.168 4.5" /></svg>
            </div>
            <p class="text-[16px] font-bold text-base-content">
                @if ($filter === 'unread')
                    {{ __('No unread notifications') }}
                @elseif ($filter === 'read')
                    {{ __('No read notifications') }}
                @else
                    {{ __('No notifications yet') }}
                @endif
            </p>
            <p class="mt-1.5 text-[13px] text-base-content/40 max-w-sm mx-auto">
                @if ($filter === 'unread')
                    {{ __("You're all caught up! All your notifications have been read.") }}
                @elseif ($category !== 'all')
                    {{ __('No notifications found in this category. Try a different filter.') }}
                @else
                    {{ __('New alerts about your orders, payments, and account will appear here.') }}
                @endif
            </p>
            @if ($filter !== 'all' || $category !== 'all')
                <button wire:click="setFilter('all'); setCategory('all')" class="mt-4 text-[12px] font-bold text-primary hover:underline cursor-pointer">
                    {{ __('Clear all filters') }}
                </button>
            @endif
        </div>
    @else
        <div class="bg-base-100 border border-base-content/5 rounded-xl overflow-hidden">
            @foreach ($notifications as $notification)
                @php
                    $isUnread = is_null($notification->read_at);
                    $icon = $notification->data['icon'] ?? 'bell';
                    $hasAction = isset($notification->data['action_url']);
                @endphp
                <div
                    wire:click="openNotification('{{ $notification->id }}')"
                    class="flex items-start gap-4 p-5 transition-all duration-150 border-b border-base-content/5 last:border-b-0 cursor-pointer group
                        {{ $isUnread ? 'bg-primary/[0.03] hover:bg-primary/[0.07]' : 'hover:bg-base-200/50' }}"
                >
                    {{-- Icon --}}
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 transition-colors
                        {{ $isUnread ? 'bg-primary/10 text-primary' : 'bg-base-200 text-base-content/40' }}">
                        @if ($icon === 'check')
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        @elseif ($icon === 'truck')
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg>
                        @elseif ($icon === 'document')
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                        @else
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    @if ($isUnread)
                                        <span class="w-2 h-2 rounded-full bg-primary flex-shrink-0"></span>
                                    @endif
                                    <h3 class="text-[14px] font-bold text-base-content truncate {{ !$isUnread ? 'opacity-70' : '' }}">
                                        {{ $notification->data['title'] ?? __('Notification') }}
                                    </h3>
                                </div>
                                <p class="mt-1 text-[13px] text-base-content/60 line-clamp-2 {{ $isUnread ? 'font-medium text-base-content/70' : '' }}">
                                    {{ $notification->data['message'] ?? '' }}
                                </p>
                            </div>
                            <div class="flex flex-col items-end gap-2 flex-shrink-0">
                                <span class="text-[11px] font-medium text-base-content/40 whitespace-nowrap">{{ $notification->created_at->diffForHumans(short: true) }}</span>
                                @if ($hasAction)
                                    <span class="text-[11px] font-bold text-primary opacity-0 group-hover:opacity-100 transition-opacity duration-150 flex items-center gap-1">
                                        {{ $notification->data['action_text'] ?? __('View') }}
                                        <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Mark as Read --}}
                    @if ($isUnread)
                        <button
                            wire:click.stop="markAsRead('{{ $notification->id }}')"
                            class="w-8 h-8 rounded-lg hover:bg-base-200 text-base-content/30 hover:text-success flex items-center justify-center transition-all duration-150 flex-shrink-0 opacity-0 group-hover:opacity-100 cursor-pointer"
                            title="{{ __('Mark as read') }}"
                        >
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                        </button>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if ($notifications->hasPages())
            <div class="pt-2">
                {{ $notifications->links() }}
            </div>
        @endif
    @endif
</div>
