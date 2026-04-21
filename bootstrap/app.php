<?php

use App\Console\Commands\CheckDeviceHealth;
use App\Console\Commands\DetectAttendanceAnomalies;
use App\Console\Commands\NotifyAbsentStudents;
use App\Console\Commands\UpdateStudentStreaks;
use App\Http\Middleware\DeviceTokenAuth;
use App\Http\Middleware\RequestId;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(RequestId::class);

        $middleware->alias([
            'role'         => RoleMiddleware::class,
            'device.token' => DeviceTokenAuth::class,
        ]);

        $middleware->appendToGroup('web', [
            SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        // ── Inovasi 8: Device Health Monitoring ─────────────────────────────
        // Check every 5 minutes; send email alert if any device is offline >10 min.
        $schedule->command(CheckDeviceHealth::class)->everyFiveMinutes();

        // ── Inovasi 2: Notify absent students' parents via WhatsApp ─────────
        // Run at 07:30 weekdays only — after check-in window typically closes.
        $schedule->command(NotifyAbsentStudents::class)->weekdays()->at('07:30');

        // ── Inovasi 3: Anomaly Detection ─────────────────────────────────────
        // Run every Friday night so weekly patterns can be detected over Mon-Fri.
        $schedule->command(DetectAttendanceAnomalies::class)->weekly()->fridays()->at('22:00');

        // ── Inovasi 7: Streak Updates ────────────────────────────────────────
        // Runs at 09:00 on weekdays — processes YESTERDAY's attendance so data
        // is definitely complete. Using 09:00 instead of 08:00 avoids race
        // conditions with late check-in windows or slow queue backlogs.
        $schedule->command(UpdateStudentStreaks::class)->weekdays()->at('09:00');
    })
    ->create();

