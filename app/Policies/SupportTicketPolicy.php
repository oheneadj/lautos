<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\SupportTicket;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class SupportTicketPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SupportTicket');
    }

    public function view(AuthUser $authUser, SupportTicket $supportTicket): bool
    {
        return $authUser->can('View:SupportTicket');
    }

    public function update(AuthUser $authUser, SupportTicket $supportTicket): bool
    {
        return $authUser->can('Update:SupportTicket');
    }
}
