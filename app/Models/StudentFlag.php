<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentFlag extends Model
{
    protected $fillable = [
        'user_id',
        'flag_type',
        'details',
        'flagged_date',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'details'     => 'array',
            'flagged_date' => 'date',
            'resolved_at' => 'datetime',
        ];
    }

    // Flag type constants
    public const TYPE_LATE_PATTERN        = 'late_pattern';
    public const TYPE_CONSECUTIVE_ABSENT  = 'consecutive_absent';
    public const TYPE_FAST_CHECKOUT       = 'fast_checkout';

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ──────────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────────

    public function scopeUnresolved(Builder $query): Builder
    {
        return $query->whereNull('resolved_at');
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('flag_type', $type);
    }
}
