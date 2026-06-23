<?php

/**
 * Customer notifications centre with filters and mark-as-read functionality.
 *
 * Notification data shape (from toArray):
 *   title, message, icon (check|truck|document|bell), action_url, action_text
 *
 * @author Ohene Adjei
 */

namespace App\Livewire\Customer;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Notifications')]
#[Layout('layouts.app')]
class NotificationsHub extends Component
{
    use WithPagination;

    #[Url]
    public string $filter = 'all'; // all | unread | read

    #[Url]
    public string $category = 'all'; // all | orders | payments | kyc

    /**
     * Maps notification class basenames to UI categories.
     */
    private const CATEGORY_MAP = [
        'OrderPlacedNotification'              => 'orders',
        'OrderStageUpdatedNotification'        => 'orders',
        'ReservationLostNotification'          => 'orders',
        'PaymentConfirmedNotification'         => 'payments',
        'PaymentRejectedNotification'          => 'payments',
        'PaymentProofUploadedNotification'     => 'payments',
        'PaymentProofReceivedNotification'     => 'payments',
        'KycDocumentsSubmittedNotification'    => 'kyc',
        'KycResubmissionRequestedNotification' => 'kyc',
    ];

    public function markAsRead(string $id): void
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();

        if ($notification && is_null($notification->read_at)) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead(): void
    {
        Auth::user()->unreadNotifications->markAsRead();
        $this->dispatch('toast', message: __('All notifications marked as read.'));
    }

    /**
     * Click-to-read: mark as read and navigate to the action URL if one exists.
     */
    public function openNotification(string $id): void
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();

        if (! $notification) {
            return;
        }

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        $url = $notification->data['action_url'] ?? null;

        if ($url) {
            $this->redirect($url, navigate: true);
        }
    }

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    public function setCategory(string $category): void
    {
        $this->category = $category;
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        $query = $user->notifications();

        // Read/Unread filter
        if ($this->filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($this->filter === 'read') {
            $query->whereNotNull('read_at');
        }

        // Category filter — match on the notification type class suffix
        if ($this->category !== 'all') {
            $classNames = array_keys(array_filter(
                self::CATEGORY_MAP,
                fn ($cat) => $cat === $this->category
            ));

            if (! empty($classNames)) {
                $query->where(function ($q) use ($classNames) {
                    foreach ($classNames as $className) {
                        $q->orWhere('type', 'like', '%\\' . $className);
                    }
                });
            }
        }

        // Stats for the filter tabs
        $unreadCount = $user->unreadNotifications()->count();
        $totalCount = $user->notifications()->count();

        return view('livewire.customer.notifications-hub', [
            'notifications' => $query->paginate(15),
            'unreadCount'   => $unreadCount,
            'totalCount'    => $totalCount,
        ]);
    }
}
