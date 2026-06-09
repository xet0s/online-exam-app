<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Classroom extends Model
{
    protected $fillable = ['name', 'building', 'capacity', 'department_id'];


    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function exams(): BelongsToMany
    {
        return $this->belongsToMany(Exam::class);
    }
}
