<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamPeriod extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'department_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date'   => 'date',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    
    public static function getForDepartment(?int $departmentId): ?self
    {
        if ($departmentId) {
            $period = self::where('department_id', $departmentId)->latest()->first();
            if ($period) return $period;
        }

        return self::whereNull('department_id')->latest()->first();
    }
}
