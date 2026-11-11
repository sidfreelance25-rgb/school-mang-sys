<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'exam_schedule_id', 'student_id', 'marks_obtained', 'grade', 'is_absent', 'remarks', 'entered_by',
])]
class ExamResult extends Model
{
    protected function casts(): array
    {
        return [
            'marks_obtained' => 'decimal:2',
            'is_absent' => 'boolean',
        ];
    }

    public function examSchedule(): BelongsTo
    {
        return $this->belongsTo(ExamSchedule::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    public static function calculateGrade(?float $percentage): string
    {
        if ($percentage === null) {
            return 'N/A';
        }

        return match (true) {
            $percentage >= 90 => 'A+',
            $percentage >= 80 => 'A',
            $percentage >= 70 => 'B+',
            $percentage >= 60 => 'B',
            $percentage >= 50 => 'C',
            $percentage >= 40 => 'D',
            $percentage >= 33 => 'E',
            default => 'F',
        };
    }
}
