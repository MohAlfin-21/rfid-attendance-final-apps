<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Devices\Services\DeviceHealthService;
use App\Http\Controllers\Controller;
use App\Models\AbsenceRequest;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Device;
use App\Models\StudentFlag;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with summary statistics.
     */
    public function index(DeviceHealthService $healthService)
    {
        $today = Carbon::today()->toDateString();

        // Student stats
        $totalStudents = User::role('student')->active()->count();
        $todayAttendances = Attendance::forDate($today)->get();
        $presentCount = $todayAttendances->whereIn('status', ['present', 'late'])->count();
        $lateCount = $todayAttendances->where('status', 'late')->count();
        $absentToday = $totalStudents - $todayAttendances->count();

        // Device stats
        $devices = Device::active()->get();
        $devicesOnline = 0;
        $deviceSnapshots = [];
        foreach ($devices as $device) {
            $snapshot = $healthService->snapshot($device);
            $deviceSnapshots[] = ['device' => $device, 'snapshot' => $snapshot];
            if ($snapshot->status->value === 'healthy') {
                $devicesOnline++;
            }
        }

        // Pending absence requests
        $pendingRequests = AbsenceRequest::pending()->count();

        // Recent attendance
        $recentAttendances = Attendance::with(['user', 'classroom'])
            ->latest('check_in_at')
            ->take(10)
            ->get();

        // Classrooms
        $totalClassrooms = Classroom::active()->count();

        // Anomaly flags count (Inovasi 3)
        $anomalyCount = StudentFlag::unresolved()->count();

        return view('admin.dashboard', compact(
            'totalStudents', 'presentCount', 'lateCount', 'absentToday',
            'devicesOnline', 'devices', 'deviceSnapshots',
            'pendingRequests', 'recentAttendances', 'totalClassrooms', 'today',
            'anomalyCount'
        ));
    }
}
