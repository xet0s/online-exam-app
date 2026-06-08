<?php

namespace App\Policies;

use App\Models\Classroom;
use App\Models\User;

class ClassroomPolicy
{

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Classroom $classroom): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isDean();
    }

    public function update(User $user, Classroom $classroom): bool
    {
        return $user->isAdmin() || $user->isDean();
    }

    public function delete(User $user, Classroom $classroom): bool
    {
        return $user->isAdmin() || $user->isDean();
    }
}
