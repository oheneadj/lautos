<?php

namespace App\Livewire\Customer;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Notifications')]
#[Layout('layouts.app')]
class NotificationsHub extends Component
{
    use WithPagination;

    public function markAsRead($id)
    {
        Auth::user()->notifications()->where('id', $id)->first()?->markAsRead();
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        $this->dispatch('toast', message: __('All notifications marked as read.'));
    }

    public function render()
    {
        return view('livewire.customer.notifications-hub', [
            'notifications' => Auth::user()->notifications()->paginate(20),
        ]);
    }
}
