<?php

namespace App\Console\Commands;

use App\Domain\Devices\Services\DeviceHealthService;
use App\Mail\DeviceOfflineAlert;
use App\Models\Device;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckDeviceHealth extends Command
{
    protected $signature = 'devices:check-health
                            {--threshold=10 : Threshold in minutes before a device is considered offline}';

    protected $description = 'Check all active devices health and send email alert if any is offline.';

    public function __construct(protected DeviceHealthService $healthService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $thresholdMinutes = (int) $this->option('threshold');
        $thresholdSeconds = $thresholdMinutes * 60;
        $now              = CarbonImmutable::now();

        $devices = Device::active()->get();

        if ($devices->isEmpty()) {
            $this->info('No active devices found. Nothing to check.');
            return self::SUCCESS;
        }

        $offlineDevices = $devices->filter(function (Device $device) use ($thresholdSeconds) {
            $snapshot = $this->healthService->snapshot($device, $thresholdSeconds);
            return $snapshot->status->value === 'offline';
        });

        $this->info("Checked {$devices->count()} device(s). Offline: {$offlineDevices->count()}.");

        if ($offlineDevices->isEmpty()) {
            return self::SUCCESS;
        }

        // Get all admin emails to notify
        $adminEmails = User::role('admin')
            ->active()
            ->whereNotNull('email')
            ->pluck('email');

        if ($adminEmails->isEmpty()) {
            $this->warn('No active admin users with email found. Cannot send alerts.');
            return self::SUCCESS;
        }

        foreach ($offlineDevices as $device) {
            $minutesOffline = $device->last_heartbeat_at
                ? (int) $device->last_heartbeat_at->diffInMinutes($now)
                : $thresholdMinutes;

            foreach ($adminEmails as $email) {
                try {
                    Mail::to($email)->send(new DeviceOfflineAlert($device, $minutesOffline));
                    $this->line("  → Alert sent to {$email} for device [{$device->code}].");
                } catch (\Throwable $e) {
                    Log::error('[CheckDeviceHealth] Failed to send alert email.', [
                        'device' => $device->code,
                        'email'  => $email,
                        'error'  => $e->getMessage(),
                    ]);
                    $this->error("  ✗ Failed to send to {$email}: {$e->getMessage()}");
                }
            }
        }

        return self::SUCCESS;
    }
}
