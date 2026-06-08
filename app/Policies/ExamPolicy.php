<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\User;

class ExamPolicy
{

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Exam $exam): bool
    {
        if ($user->isAdmin() || $user->isDean()) {
            return true;
        }

        if ($user->isChair() && $user->department_id === $exam->department_id) {
            return true;
        }

        if ($user->isInstructor() && $exam->instructor_id === $user->id) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isDean() || $user->isChair() || $user->isInstructor();
    }

    public function update(User $user, Exam $exam): bool
    {
        if ($user->isAdmin() || $user->isDean()) {
            return true;
        }

        if ($user->isChair() && $user->department_id === $exam->department_id) {
            return true;
        }

        if ($user->isInstructor() && $exam->instructor_id === $user->id) {
            return true;
        }

        return false;
    }

    public function delete(User $user, Exam $exam): bool
    {
        return $this->update($user, $exam);
    }
}
