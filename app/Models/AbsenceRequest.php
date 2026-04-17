<?php

namespace App\Models;

use App\Domain\Absence\Enums\AbsenceRequestStatus;
use App\Domain\Absence\Enums\AbsenceRequestType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsenceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'status',
        'date_start',
        'date_end',
        'reason',
        'attachment_path',
        'reviewed_by',
        'reviewed_at',
        'review_note',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => AbsenceRequestType::class,
            'status' => AbsenceRequestStatus::class,
            'date_start' => 'date',
            'date_end' => 'date',
            'reviewed_at' => 'datetime',
        ];
    }

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────

    /**
     * The student who submitted this request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The user who reviewed (approved/rejected) this request.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ──────────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────────

    /**
     * Scope to only pending requests.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', AbsenceRequestStatus::Pending);
    }

    /**
     * Scope to requests that cover a specific date.
     */
    public function scopeCoversDate(Builder $query, string $date): Builder
    {
        return $query->where('date_start', '<=', $date)
            ->where('date_end', '>=', $date);
    }

    // ──────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────

    /**
     * Whether this request is still pending review.
     */
    public function isPending(): bool
    {
        return $this->status === AbsenceRequestStatus::Pending;
    }

    /**
     * Whether this request has been approved.
     */
    public function isApproved(): bool
    {
        return $this->status === AbsenceRequestStatus::Approved;
    }
}
