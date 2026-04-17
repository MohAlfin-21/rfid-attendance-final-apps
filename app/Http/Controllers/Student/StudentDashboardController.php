<?php

namespace App\Http\Controllers\Student;

use App\Domain\Absence\Enums\AbsenceRequestStatus;
use App\Domain\Absence\Enums\AbsenceRequestType;
use App\Domain\Attendance\Enums\AttendanceStatus;
use App\Http\Controllers\Controller;
use App\Models\AbsenceRequest;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today()->toDateString();

        // Today's attendance
        $todayAttendance = Attendance::where('user_id', $user->id)->forDate($today)->first();

        // Monthly stats
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();
        $monthlyAttendances = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])->get();

        $stats = [
            'present' => $monthlyAttendances->where('status', AttendanceStatus::Present)->count(),
            'late' => $monthlyAttendances->where('status', AttendanceStatus::Late)->count(),
            'sick' => $monthlyAttendances->where('status', AttendanceStatus::Sick)->count(),
            'excused' => $monthlyAttendances->where('status', AttendanceStatus::Excused)->count(),
            'absent' => $monthlyAttendances->where('status', AttendanceStatus::Absent)->count(),
        ];

        // Classroom info
        $classroom = $user->classrooms()->where('classrooms.is_active', true)->first();

        // Pending absence requests
        $pendingRequests = AbsenceRequest::where('user_id', $user->id)->pending()->count();

        // Recent attendance
        $recentAttendances = Attendance::where('user_id', $user->id)
            ->with('classroom')
            ->latest('date')
            ->take(10)->get();

        return view('student.dashboard', compact('todayAttendance', 'stats', 'classroom', 'pendingRequests', 'recentAttendances'));
    }

    public function attendance(Request $request)
    {
        $user = auth()->user();
        $month = $request->get('month', Carbon::now()->format('Y-m'));
        $startDate = Carbon::parse($month . '-01');
        $endDate = $startDate->copy()->endOfMonth();

        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->with('classroom')
            ->orderBy('date', 'desc')
            ->get();

        return view('student.attendance', compact('attendances', 'month'));
    }

    public function absenceRequestsIndex()
    {
        $requests = AbsenceRequest::where('user_id', auth()->id())
            ->with('reviewer')
            ->latest()
            ->paginate(10);

        return view('student.absence-requests.index', compact('requests'));
    }

    public function absenceRequestsCreate()
    {
        return view('student.absence-requests.create');
    }

    public function absenceRequestsStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:permission,sick,other',
            'date_start' => 'required|date|after_or_equal:today',
            'date_end' => 'required|date|after_or_equal:date_start',
            'reason' => 'required|string|max:1000',
            'attachment' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('absence-attachments', 'public');
        }

        AbsenceRequest::create([
            'user_id' => auth()->id(),
            'type' => $validated['type'],
            'status' => AbsenceRequestStatus::Pending->value,
            'date_start' => $validated['date_start'],
            'date_end' => $validated['date_end'],
            'reason' => $validated['reason'],
            'attachment_path' => $attachmentPath,
        ]);

        return redirect()->route('student.absence-requests.index')
            ->with('success', 'Permohonan izin berhasil dikirim. Menunggu persetujuan.');
    }
}
