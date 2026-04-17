<?php

namespace Tests\Feature\Api;

use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\Classroom;
use App\Models\Device;
use App\Models\RfidCard;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeviceApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('cache.default', 'array');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    public function test_device_settings_endpoint_returns_runtime_configuration(): void
    {
        [$device, $headers] = $this->makeDevice();

        $response = $this->withHeaders($headers)->getJson('/api/v1/devices/settings');

        $response->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('device.code', $device->code)
            ->assertJsonPath('attendance.timezone', config('attendance.timezone'))
            ->assertJsonPath('heartbeat_interval_seconds', $device->heartbeat_interval_seconds);

        $this->assertNotNull($device->fresh()->last_heartbeat_at);
    }

    public function test_heartbeat_updates_device_state_and_clears_errors(): void
    {
        [$device, $headers] = $this->makeDevice([
            'error_count' => 3,
            'last_error_message' => 'WiFi timeout',
            'last_error_at' => now()->subMinute(),
        ]);

        $response = $this->withHeaders($headers)->postJson('/api/v1/devices/heartbeat', [
            'firmware_version' => '1.2.0',
            'wifi_rssi' => -64,
            'free_heap' => 32768,
            'reader_uptime_ms' => 18000,
        ]);

        $response->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('heartbeat_interval_seconds', $device->heartbeat_interval_seconds);

        $freshDevice = $device->fresh();

        $this->assertSame('1.2.0', $freshDevice->firmware_version);
        $this->assertSame(0, $freshDevice->error_count);
        $this->assertNull($freshDevice->last_error_at);
        $this->assertNull($freshDevice->last_error_message);
        $this->assertNotNull($freshDevice->last_heartbeat_at);
    }

    public function test_scan_records_successful_check_in(): void
    {
        $this->freezeJakarta('2026-04-13 06:30:00');

        [$device, $headers] = $this->makeDevice();
        [$student, $classroom, $card] = $this->makeStudentContext();

        $response = $this->withHeaders($headers)->postJson('/api/v1/devices/attendance/scan', [
            'uid' => strtolower($card->uid),
            'firmware_version' => '1.0.1',
            'wifi_rssi' => -58,
            'reader_uptime_ms' => 12500,
            'free_heap' => 40200,
        ]);

        $response->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('code', 'check_in_ok')
            ->assertJsonPath('action', 'check_in')
            ->assertJsonPath('student.id', $student->id)
            ->assertJsonPath('student.classroom', $classroom->name)
            ->assertJsonPath('status', 'present');

        $attendance = Attendance::query()->where('user_id', $student->id)->firstOrFail();

        $this->assertSame($classroom->id, $attendance->classroom_id);
        $this->assertSame('present', $attendance->status->value);
        $this->assertNotNull($attendance->check_in_at);
        $this->assertNull($attendance->check_out_at);

        $this->assertDatabaseHas('attendance_logs', [
            'attendance_id' => $attendance->id,
            'device_id' => $device->id,
            'rfid_uid' => $card->uid,
            'rule_hit' => 'check_in_ok',
        ]);
    }

    public function test_scan_returns_duplicate_warning_for_immediate_repeat_tap(): void
    {
        $this->freezeJakarta('2026-04-13 06:32:00');

        [$device, $headers] = $this->makeDevice();
        [, , $card] = $this->makeStudentContext();

        $this->withHeaders($headers)->postJson('/api/v1/devices/attendance/scan', [
            'uid' => $card->uid,
        ])->assertOk();

        $duplicate = $this->withHeaders($headers)->postJson('/api/v1/devices/attendance/scan', [
            'uid' => $card->uid,
        ]);

        $duplicate->assertOk()
            ->assertJsonPath('ok', false)
            ->assertJsonPath('result', 'warning')
            ->assertJsonPath('code', 'duplicate_scan_cooldown');

        $this->assertSame(2, AttendanceLog::query()->where('rfid_uid', $card->uid)->count());
    }

    public function test_scan_records_checkout_during_checkout_window(): void
    {
        [$device, $headers] = $this->makeDevice();
        [$student, $classroom, $card] = $this->makeStudentContext();

        $this->freezeJakarta('2026-04-13 06:20:00');
        $this->withHeaders($headers)->postJson('/api/v1/devices/attendance/scan', [
            'uid' => $card->uid,
        ])->assertOk();

        $this->freezeJakarta('2026-04-13 15:20:00');
        $response = $this->withHeaders($headers)->postJson('/api/v1/devices/attendance/scan', [
            'uid' => $card->uid,
        ]);

        $response->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('code', 'check_out_ok')
            ->assertJsonPath('action', 'check_out')
            ->assertJsonPath('student.id', $student->id)
            ->assertJsonPath('student.classroom', $classroom->name);

        $attendance = Attendance::query()->where('user_id', $student->id)->firstOrFail();

        $this->assertNotNull($attendance->check_out_at);
        $this->assertSame('normal', $attendance->check_out_type->value);
    }

    public function test_scan_rejects_unregistered_card(): void
    {
        [$device, $headers] = $this->makeDevice();

        $response = $this->withHeaders($headers)->postJson('/api/v1/devices/attendance/scan', [
            'uid' => 'DEADBEEF',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('ok', false)
            ->assertJsonPath('code', 'card_not_registered')
            ->assertJsonPath('result', 'error');

        $this->assertDatabaseHas('attendance_logs', [
            'device_id' => $device->id,
            'rfid_uid' => 'DEADBEEF',
            'rule_hit' => 'card_not_registered',
        ]);
    }

    public function test_scan_requires_a_valid_device_token(): void
    {
        $response = $this->postJson('/api/v1/devices/attendance/scan', [
            'uid' => 'A1B2C3D4',
        ]);

        $response->assertUnauthorized()
            ->assertJsonPath('message', 'Missing device token.');
    }

    protected function makeDevice(array $overrides = []): array
    {
        $plainToken = $overrides['plain_token'] ?? 'dev-test-token';
        unset($overrides['plain_token']);

        $device = Device::query()->create(array_merge([
            'code' => 'ESP-01',
            'name' => 'ESP8266 Reader',
            'location' => 'Gerbang',
            'token_hash' => hash('sha256', $plainToken),
            'token_plain_encrypted' => $plainToken,
            'is_active' => true,
            'firmware_version' => '1.0.0',
            'heartbeat_interval_seconds' => 60,
            'error_count' => 0,
        ], $overrides));

        return [$device, [
            'X-Device-Token' => $plainToken,
            'Accept' => 'application/json',
        ]];
    }

    protected function makeStudentContext(): array
    {
        $student = User::factory()->student()->create([
            'name' => 'Ahmad Device',
            'username' => 'ahmad.device',
            'email' => 'ahmad.device@example.test',
            'nis' => '2026999',
        ]);

        $classroom = Classroom::query()->create([
            'code' => 'XII-RPL-1',
            'name' => 'XII RPL 1',
            'grade' => 12,
            'major' => 'RPL',
            'is_active' => true,
        ]);

        $student->classrooms()->attach($classroom->id, [
            'academic_year' => config('attendance.academic_year'),
            'semester' => config('attendance.semester'),
            'is_active' => true,
        ]);

        $card = RfidCard::query()->create([
            'uid' => 'A1B2C3D4',
            'user_id' => $student->id,
            'status' => 'active',
            'registered_at' => now(),
        ]);

        return [$student, $classroom, $card];
    }

    protected function freezeJakarta(string $dateTime): void
    {
        $utcNow = CarbonImmutable::parse($dateTime, 'Asia/Jakarta')->setTimezone('UTC');

        Carbon::setTestNow($utcNow);
        CarbonImmutable::setTestNow($utcNow);
    }
}
