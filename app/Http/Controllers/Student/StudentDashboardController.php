<?php

namespace App\Http\Controllers\Student;

use App\Domain\Absence\Enums\AbsenceRequestStatus;
use App\Domain\Attendance\Enums\AttendanceStatus;
use App\Http\Controllers\Controller;
use App\Models\AbsenceRequest;
use App\Models\Attendance;
use App\Models\StudentStreak;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
            'attachment' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $folder = sprintf('absence-attachments/user-%s/%s', auth()->id(), now()->format('Y/m'));
            $filename = now()->format('YmdHis') . '-' . Str::random(10) . '.' . $file->extension();

            $attachmentPath = $file->storeAs($folder, $filename, 'public');
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

    public function leaderboard()
    {
        $user      = auth()->user();
        $classroom = $user->classrooms()->where('classrooms.is_active', true)->first();

        $topStreaks = StudentStreak::with('user')
            ->when($classroom, function ($q) use ($classroom) {
                $q->whereHas('user', fn ($u) => $u->whereHas('classrooms',
                    fn ($c) => $c->where('classrooms.id', $classroom->id)
                ));
            })
            ->orderByDesc('current_streak')
            ->take(20)
            ->get();

        $myStreak = StudentStreak::firstOrCreate(
            ['user_id' => $user->id],
            ['current_streak' => 0, 'longest_streak' => 0, 'total_points' => 0]
        );

        return view('student.leaderboard', compact('topStreaks', 'myStreak', 'classroom'));
    }
}
