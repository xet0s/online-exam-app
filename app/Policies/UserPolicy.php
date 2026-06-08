<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{

    public function approve(User $user, User $targetUser): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($targetUser->isAdmin() || $targetUser->isDean()) {
            return false;
        }

        if ($user->isDean()) {
            return true;
        }

        return false;
    }

    public function reject(User $user, User $targetUser): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($targetUser->isAdmin() || $targetUser->isDean()) {
            return false;
        }

        if ($user->isDean()) {
            return true;
        }

        if ($user->isChair() && $targetUser->isInstructor() && $user->department_id === $targetUser->department_id) {
            return true;
        }

        return false;
    }
}
