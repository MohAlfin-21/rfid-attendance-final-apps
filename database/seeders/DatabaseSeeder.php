<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Safe reference data
        $this->call(RolePermissionSeeder::class);
        $this->call(SystemSettingSeeder::class);

        if (! $this->shouldSeedDemoData()) {
            $this->command?->warn('Skipping demo seeders. Set SEED_DEMO_DATA=true to seed sample users, class, and device.');

            return;
        }

        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'email' => 'Admin@smk.id',
                'nis' => null,
                'locale' => 'id',
                'is_active' => true,
                'password' => 'admin',
                'email_verified_at' => now(),
            ],
        );
        $admin->assignRole('admin');

        $this->call(KelasXiRpl1Seeder::class);

        $existingDevice = Device::where('code', 'READER-01')->first();

        if ($existingDevice === null) {
            $plainToken = env('SEED_DEVICE_TOKEN', 'dev-token-' . Str::random(32));

            Device::create([
                'code' => 'READER-01',
                'name' => 'Reader Utama - Gerbang',
                'location' => 'Gerbang Utama Sekolah',
                'token_hash' => hash('sha256', $plainToken),
                'token_plain_encrypted' => $plainToken,
                'is_active' => true,
                'firmware_version' => '1.0.0',
                'heartbeat_interval_seconds' => 60,
                'error_count' => 0,
            ]);

            $this->command?->info("  Device token: {$plainToken}");
        } else {
            $this->command?->info('  Sample device READER-01 already exists; keeping current token.');
        }
    }

    protected function shouldSeedDemoData(): bool
    {
        if (app()->environment('testing')) {
            return true;
        }

        return filter_var((string) env('SEED_DEMO_DATA', 'false'), FILTER_VALIDATE_BOOLEAN);
    }
}
