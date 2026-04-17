<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Attendance\Enums\AttendanceStatus;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Classroom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('date', Carbon::today()->toDateString());
        $classrooms = Classroom::active()->get();

        $query = Attendance::with(['user', 'classroom'])->forDate($date);

        if ($classroomId = $request->get('classroom_id')) {
            $query->forClassroom($classroomId);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $attendances = $query->latest('check_in_at')->paginate(20)->withQueryString();

        // Summary counts for the filtered date
        $summary = Attendance::forDate($date);
        if ($classroomId) {
            $summary = $summary->forClassroom($classroomId);
        }
        $summary = $summary->get();
        $counts = [
            'present' => $summary->where('status', AttendanceStatus::Present)->count(),
            'late' => $summary->where('status', AttendanceStatus::Late)->count(),
            'excused' => $summary->where('status', AttendanceStatus::Excused)->count(),
            'sick' => $summary->where('status', AttendanceStatus::Sick)->count(),
            'absent' => $summary->where('status', AttendanceStatus::Absent)->count(),
        ];

        $statuses = AttendanceStatus::cases();

        return view('admin.attendances.index', compact('attendances', 'classrooms', 'date', 'counts', 'statuses'));
    }

    public function override(Request $request, Attendance $attendance): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:present,late,excused,sick,absent',
            'override_note' => 'required|string|max:500',
        ]);

        $attendance->update([
            'status' => $validated['status'],
            'override_by' => auth()->id(),
            'override_note' => $validated['override_note'],
        ]);

        return back()->with('success', __('Status absensi :name berhasil diubah.', ['name' => $attendance->user->name]));
    }
}
