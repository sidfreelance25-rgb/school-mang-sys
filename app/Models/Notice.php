<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['title', 'body', 'type', 'audience', 'is_important', 'publish_date', 'expiry_date', 'created_by'])]
class Notice extends Model
{
    protected function casts(): array
    {
        return [
            'is_important' => 'boolean',
            'publish_date' => 'date',
            'expiry_date' => 'date',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereDate('publish_date', '<=', now())
            ->where(function (Builder $builder): void {
                $builder->whereNull('expiry_date')
                    ->orWhereDate('expiry_date', '>=', now());
            });
    }
}
