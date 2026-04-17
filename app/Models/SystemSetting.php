<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
    ];

    /**
     * Cache TTL in seconds (1 hour).
     */
    private const CACHE_TTL = 3600;

    /**
     * Cache key prefix.
     */
    private const CACHE_PREFIX = 'system_setting:';

    // ──────────────────────────────────────────────
    // Static Accessors
    // ──────────────────────────────────────────────

    /**
     * Get a setting value by key, with type casting.
     *
     * @param  string  $key      The setting key (e.g. 'attendance.timezone').
     * @param  mixed   $default  Default value if not found.
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = Cache::remember(
            self::CACHE_PREFIX . $key,
            self::CACHE_TTL,
            fn () => static::where('key', $key)->first(),
        );

        if (! $setting) {
            return $default;
        }

        return self::castValue($setting->value, $setting->type);
    }

    /**
     * Set a setting value by key.
     *
     * @param  string       $key    The setting key.
     * @param  mixed        $value  The value to store.
     * @param  string|null  $type   Value type hint (string, integer, boolean, json).
     */
    public static function set(string $key, mixed $value, ?string $type = null): void
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : (string) $value,
                'type' => $type ?? 'string',
            ],
        );

        Cache::forget(self::CACHE_PREFIX . $key);
        Cache::forget('system_settings:all');
    }

    /**
     * Get all settings as a keyed array.
     *
     * @return array<string, mixed>
     */
    public static function allKeyed(): array
    {
        return Cache::remember('system_settings:all', self::CACHE_TTL, function () {
            return static::all()
                ->mapWithKeys(fn (self $s) => [
                    $s->key => self::castValue($s->value, $s->type),
                ])
                ->toArray();
        });
    }

    /**
     * Get settings for a specific group.
     *
     * @param  string  $group  The group name (e.g. 'attendance', 'device').
     * @return array<string, mixed>
     */
    public static function forGroup(string $group): array
    {
        return static::where('group', $group)
            ->get()
            ->mapWithKeys(fn (self $s) => [
                $s->key => self::castValue($s->value, $s->type),
            ])
            ->toArray();
    }

    /**
     * Build the attendance settings array expected by AttendanceWindowService.
     *
     * @return array<string, string|null>
     */
    public static function attendanceWindowSettings(): array
    {
        return [
            'timezone' => self::get('attendance.timezone', config('attendance.timezone')),
            'check_in_start' => self::get('attendance.check_in_start', config('attendance.check_in_start')),
            'check_in_end' => self::get('attendance.check_in_end', config('attendance.check_in_end')),
            'late_after' => self::get('attendance.late_after', config('attendance.late_after')),
            'check_out_start' => self::get('attendance.check_out_start', config('attendance.check_out_start')),
            'check_out_end' => self::get('attendance.check_out_end', config('attendance.check_out_end')),
        ];
    }

    // ──────────────────────────────────────────────
    // Private Helpers
    // ──────────────────────────────────────────────

    /**
     * Cast a raw string value to its declared type.
     */
    private static function castValue(string $value, string $type): mixed
    {
        return match ($type) {
            'integer', 'int' => (int) $value,
            'boolean', 'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($value, true),
            'float', 'double' => (float) $value,
            default => $value,
        };
    }
}
