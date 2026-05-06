<?php

namespace App\Policies;

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ComplaintPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('complaints');
    }

    public function view(User $user, Complaint $complaint): bool
    {
        return $user->hasPermission('complaints');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('complaints');
    }

    public function update(User $user, Complaint $complaint): bool
    {
        return $user->hasPermission('complaints');
    }

    public function delete(User $user, Complaint $complaint): bool
    {
        return $user->hasPermission('complaints');
    }

    public function restore(User $user, Complaint $complaint): bool
    {
        return $user->hasPermission('complaints');
    }

    public function forceDelete(User $user, Complaint $complaint): bool
    {
        return $user->hasPermission('complaints');
    }
}
