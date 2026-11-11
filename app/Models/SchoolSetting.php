<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name', 'logo', 'address', 'city', 'state', 'postal_code', 'phone', 'email', 'website',
    'working_days', 'attendance_start_hour', 'grading_system', 'fee_notes',
])]
class SchoolSetting extends Model
{
    protected function casts(): array
    {
        return [
            'working_days' => 'array',
            'attendance_start_hour' => 'integer',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'name' => 'Sunrise Public School',
            'working_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
        ]);
    }
}
