<?php

namespace App\Models;

use App\Domain\Attendance\Enums\AttendanceAction;
use App\Domain\Attendance\Enums\ScanRuleHit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'action',
        'rfid_uid',
        'device_id',
        'rule_hit',
        'metadata',
        'request_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'action' => AttendanceAction::class,
            'rule_hit' => ScanRuleHit::class,
            'metadata' => 'json',
        ];
    }

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────

    /**
     * The attendance record this log belongs to.
     */
    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    /**
     * The device that recorded this log entry.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
