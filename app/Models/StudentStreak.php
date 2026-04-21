<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentStreak extends Model
{
    protected $fillable = [
        'user_id',
        'current_streak',
        'longest_streak',
        'total_points',
        'last_streak_date',
    ];

    protected function casts(): array
    {
        return [
            'last_streak_date' => 'date',
            'current_streak'   => 'integer',
            'longest_streak'   => 'integer',
            'total_points'     => 'integer',
        ];
    }

    // ──────────────────────────────────────────────
    // Badge helpers
    // ──────────────────────────────────────────────

    /**
     * Return the highest earned badge based on longest_streak.
     * Returns null if no badge earned yet.
     */
    public function badge(): ?array
    {
        if ($this->longest_streak >= 100) {
            return ['label' => '💎 100 Hari', 'color' => 'text-purple-700', 'bg' => 'bg-purple-100'];
        }
        if ($this->longest_streak >= 30) {
            return ['label' => '🥇 30 Hari',  'color' => 'text-amber-700',  'bg' => 'bg-amber-100'];
        }
        if ($this->longest_streak >= 7) {
            return ['label' => '⭐ 7 Hari',   'color' => 'text-indigo-700', 'bg' => 'bg-indigo-100'];
        }
        return null;
    }

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
