<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'invoice_no', 'student_id', 'fee_structure_id', 'academic_session_id', 'title',
    'amount', 'paid_amount', 'discount', 'due_date', 'status', 'notes',
])]
class FeeInvoice extends Model
{
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'discount' => 'decimal:2',
            'due_date' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function feeStructure(): BelongsTo
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(FeePayment::class);
    }

    public function getBalanceAttribute(): float
    {
        return max(0, (float) $this->amount - (float) $this->discount - (float) $this->paid_amount);
    }

    public function refreshStatus(): void
    {
        $balance = $this->balance;

        $this->status = match (true) {
            $balance <= 0 => 'paid',
            (float) $this->paid_amount > 0 => 'partial',
            default => 'unpaid',
        };

        $this->save();
    }
}
