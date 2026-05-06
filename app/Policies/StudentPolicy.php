<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StudentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('students');
    }

    public function view(User $user, Student $student): bool
    {
        return $user->hasPermission('students');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('students');
    }

    public function update(User $user, Student $student): bool
    {
        return $user->hasPermission('students');
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->hasPermission('students');
    }

    public function restore(User $user, Student $student): bool
    {
        return $user->hasPermission('students');
    }

    public function forceDelete(User $user, Student $student): bool
    {
        return $user->hasPermission('students');
    }
}
