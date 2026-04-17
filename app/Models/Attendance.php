<?php

namespace App\Models;

use App\Domain\Attendance\Enums\AttendanceStatus;
use App\Domain\Attendance\Enums\CheckMethod;
use App\Domain\Attendance\Enums\CheckOutType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'classroom_id',
        'date',
        'status',
        'check_in_at',
        'check_in_method',
        'check_in_device_id',
        'check_out_at',
        'check_out_method',
        'check_out_device_id',
        'check_out_type',
        'early_checkout_reason',
        'override_by',
        'override_note',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'status' => AttendanceStatus::class,
            'check_in_at' => 'datetime',
            'check_in_method' => CheckMethod::class,
            'check_out_at' => 'datetime',
            'check_out_method' => CheckMethod::class,
            'check_out_type' => CheckOutType::class,
        ];
    }

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────

    /**
     * The student this attendance belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The classroom this attendance is for.
     */
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * The device used for check-in.
     */
    public function checkInDevice(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'check_in_device_id');
    }

    /**
     * The device used for check-out.
     */
    public function checkOutDevice(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'check_out_device_id');
    }

    /**
     * The user who performed the override (if any).
     */
    public function overriddenBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'override_by');
    }

    /**
     * Audit log entries for this attendance.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }

    // ──────────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────────

    /**
     * Scope to a specific date.
     */
    public function scopeForDate(Builder $query, string $date): Builder
    {
        return $query->where('date', $date);
    }

    /**
     * Scope to a specific classroom.
     */
    public function scopeForClassroom(Builder $query, int $classroomId): Builder
    {
        return $query->where('classroom_id', $classroomId);
    }

    /**
     * Scope to only records where the student is physically present.
     */
    public function scopePresent(Builder $query): Builder
    {
        return $query->whereIn('status', [
            AttendanceStatus::Present,
            AttendanceStatus::Late,
        ]);
    }

    // ──────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────

    /**
     * Whether the student has checked out.
     */
    public function hasCheckedOut(): bool
    {
        return $this->check_out_at !== null;
    }

    /**
     * Whether the student has checked in.
     */
    public function hasCheckedIn(): bool
    {
        return $this->check_in_at !== null;
    }
}
