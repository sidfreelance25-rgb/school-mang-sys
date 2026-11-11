<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['student_id', 'title', 'file_path', 'file_type'])]
class StudentDocument extends Model
{
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
