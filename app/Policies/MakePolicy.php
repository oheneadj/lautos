<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Make;
use Illuminate\Auth\Access\HandlesAuthorization;

class MakePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Make');
    }

    public function view(AuthUser $authUser, Make $make): bool
    {
        return $authUser->can('View:Make');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Make');
    }

    public function update(AuthUser $authUser, Make $make): bool
    {
        return $authUser->can('Update:Make');
    }

    public function delete(AuthUser $authUser, Make $make): bool
    {
        return $authUser->can('Delete:Make');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Make');
    }

    public function restore(AuthUser $authUser, Make $make): bool
    {
        return $authUser->can('Restore:Make');
    }

    public function forceDelete(AuthUser $authUser, Make $make): bool
    {
        return $authUser->can('ForceDelete:Make');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Make');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Make');
    }

    public function replicate(AuthUser $authUser, Make $make): bool
    {
        return $authUser->can('Replicate:Make');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Make');
    }

}