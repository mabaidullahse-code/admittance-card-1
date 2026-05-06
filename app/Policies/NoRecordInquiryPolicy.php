<?php

namespace App\Policies;

use App\Models\NoRecordInquiry;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class NoRecordInquiryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('inquiries');
    }

    public function view(User $user, NoRecordInquiry $noRecordInquiry): bool
    {
        return $user->hasPermission('inquiries');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('inquiries');
    }

    public function update(User $user, NoRecordInquiry $noRecordInquiry): bool
    {
        return $user->hasPermission('inquiries');
    }

    public function delete(User $user, NoRecordInquiry $noRecordInquiry): bool
    {
        return $user->hasPermission('inquiries');
    }

    public function restore(User $user, NoRecordInquiry $noRecordInquiry): bool
    {
        return $user->hasPermission('inquiries');
    }

    public function forceDelete(User $user, NoRecordInquiry $noRecordInquiry): bool
    {
        return $user->hasPermission('inquiries');
    }
}
