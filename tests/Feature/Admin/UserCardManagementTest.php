<?php

namespace Tests\Feature\Admin;

use App\Models\Device;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserCardManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('cache.default', 'array');
    }

    public function test_admin_can_register_card_manually_for_user(): void
    {
        $admin = $this->makeAdmin();
        $student = User::factory()->student()->create();

        $response = $this->actingAs($admin)->postJson(
            route('admin.users.cards.store', $student),
            ['uid' => 'a1 b2-c3:d4'],
        );

        $response->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('card.uid', 'A1B2C3D4');

        $this->assertDatabaseHas('rfid_cards', [
            'user_id' => $student->id,
            'uid' => 'A1B2C3D4',
            'status' => 'active',
        ]);
    }

    public function test_admin_can_start_and_cancel_auto_read_session(): void
    {
        $admin = $this->makeAdmin();
        $student = User::factory()->student()->create();
        $device = Device::query()->create([
            'code' => 'ESP-REG-01',
            'name' => 'Reader Lab',
            'location' => 'Lab RPL',
            'token_hash' => hash('sha256', 'device-token'),
            'token_plain_encrypted' => 'device-token',
            'is_active' => true,
            'firmware_version' => '1.0.0',
            'heartbeat_interval_seconds' => 60,
            'error_count' => 0,
        ]);

        $start = $this->actingAs($admin)->postJson(
            route('admin.users.cards.enrollment.start', $student),
            ['device_id' => $device->id],
        );

        $start->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('session.status', 'waiting');

        $sessionId = $start->json('session.id');

        $status = $this->actingAs($admin)->getJson(
            route('admin.users.cards.enrollment.status', [
                'user' => $student,
                'device_id' => $device->id,
                'session_id' => $sessionId,
            ]),
        );

        $status->assertOk()
            ->assertJsonPath('status', 'waiting')
            ->assertJsonPath('active', true);

        $cancel = $this->actingAs($admin)->deleteJson(
            route('admin.users.cards.enrollment.cancel', [
                'user' => $student,
                'device_id' => $device->id,
                'session_id' => $sessionId,
            ]),
        );

        $cancel->assertOk()
            ->assertJsonPath('ok', true);

        $expired = $this->actingAs($admin)->getJson(
            route('admin.users.cards.enrollment.status', [
                'user' => $student,
                'device_id' => $device->id,
                'session_id' => $sessionId,
            ]),
        );

        $expired->assertOk()
            ->assertJsonPath('status', 'expired')
            ->assertJsonPath('active', false);
    }

    protected function makeAdmin(): User
    {
        Role::findOrCreate('admin', 'web');

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        return $admin;
    }
}
