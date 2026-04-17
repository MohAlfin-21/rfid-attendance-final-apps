<?php

namespace App\Models;

use App\Domain\Devices\Enums\CardStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RfidCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'uid',
        'user_id',
        'status',
        'registered_at',
        'lost_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => CardStatus::class,
            'registered_at' => 'datetime',
            'lost_at' => 'datetime',
        ];
    }

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────

    /**
     * The user who owns this RFID card.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ──────────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────────

    /**
     * Scope to only active cards.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', CardStatus::Active);
    }

    /**
     * Scope to find a card by its normalized UID.
     */
    public function scopeByUid(Builder $query, string $uid): Builder
    {
        return $query->where('uid', strtoupper(str_replace([':', '-', ' '], '', $uid)));
    }

    // ──────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────

    /**
     * Normalize a raw UID string (uppercase, no separators).
     */
    public static function normalizeUid(string $uid): string
    {
        return strtoupper(str_replace([':', '-', ' '], '', trim($uid)));
    }
}
