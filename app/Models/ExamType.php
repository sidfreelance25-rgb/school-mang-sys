<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'code', 'description'])]
class ExamType extends Model
{
    public function examinations(): HasMany
    {
        return $this->hasMany(Examination::class);
    }
}
