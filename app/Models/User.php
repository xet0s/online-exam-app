<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{

    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class, 'instructor_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDean(): bool
    {
        return $this->role === 'dekan';
    }

    public function isChair(): bool
    {
        return $this->role === 'bolum_baskani';
    }

    public function isInstructor(): bool
    {
        return $this->role === 'egitmen';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
