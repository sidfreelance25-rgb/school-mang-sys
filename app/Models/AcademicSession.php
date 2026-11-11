<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'start_date', 'end_date', 'is_current'])]
class AcademicSession extends Model
{
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_current' => 'boolean',
        ];
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function examinations(): HasMany
    {
        return $this->hasMany(Examination::class);
    }

    public static function current(): ?self
    {
        return static::query()->where('is_current', true)->first();
    }
}
