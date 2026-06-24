<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Faq;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class FaqPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Faq');
    }

    public function view(AuthUser $authUser, Faq $faq): bool
    {
        return $authUser->can('View:Faq');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Faq');
    }

    public function update(AuthUser $authUser, Faq $faq): bool
    {
        return $authUser->can('Update:Faq');
    }

    public function delete(AuthUser $authUser, Faq $faq): bool
    {
        return $authUser->can('Delete:Faq');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Faq');
    }
}
