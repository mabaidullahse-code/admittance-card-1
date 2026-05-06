<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('users');
    }

    public function view(User $user, User $model): bool
    {
        return $user->hasPermission('users');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('users');
    }

    public function update(User $user, User $model): bool
    {
        return $user->hasPermission('users');
    }

    public function delete(User $user, User $model): bool
    {
        // Cannot delete self
        return $user->hasPermission('users') && $user->id !== $model->id;
    }

    public function restore(User $user, User $model): bool
    {
        return $user->hasPermission('users');
    }

    public function forceDelete(User $user, User $model): bool
    {
        return $user->hasPermission('users');
    }
}
