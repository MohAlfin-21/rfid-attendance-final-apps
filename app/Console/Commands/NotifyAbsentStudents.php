<?php

namespace App\Console\Commands;

use App\Domain\Attendance\Enums\AttendanceStatus;
use App\Jobs\SendWhatsappNotification;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotifyAbsentStudents extends Command
{
    protected $signature   = 'attendance:notify-absent';
    protected $description = 'Send WhatsApp notification to parents of students who have not scanned yet today.';

    public function handle(): int
    {
        $today     = Carbon::today()->toDateString();
        $students  = User::role('student')->active()->with('studentProfile')->get();

        if ($students->isEmpty()) {
            $this->info('No active students found.');
            return self::SUCCESS;
        }

        $presentUserIds = Attendance::whereDate('date', $today)
            ->whereIn('status', [AttendanceStatus::Present->value, AttendanceStatus::Late->value,
                                 AttendanceStatus::Excused->value, AttendanceStatus::Sick->value])
            ->pluck('user_id');

        $absentStudents = $students->whereNotIn('id', $presentUserIds->toArray());

        $notified = 0;
        foreach ($absentStudents as $student) {
            $parentPhone = $student->studentProfile?->parent_phone;
            if (empty($parentPhone)) {
                continue;
            }

            $parentName = $student->studentProfile->parent_name ?? 'Orang Tua';
            $message    = "Assalamu'alaikum, Yth. {$parentName}.\n"
                        . "Kami infokan bahwa *{$student->name}* belum tercatat hadir di sekolah hari ini "
                        . "({$today}).\n"
                        . "Mohon hubungi sekolah jika ada keterangan. Terima kasih.\n"
                        . "— RFID Attendance System";

            SendWhatsappNotification::dispatch($parentPhone, $message);
            $notified++;
        }

        $this->info("Absent students: {$absentStudents->count()}. WA notifications queued: {$notified}.");
        return self::SUCCESS;
    }
}
