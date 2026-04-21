<?php

namespace App\Http\Controllers\Teacher;

use App\Domain\Attendance\Enums\AttendanceStatus;
use App\Http\Controllers\Controller;
use App\Models\AbsenceRequest;
use App\Models\Attendance;
use App\Models\Classroom;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TeacherDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today()->toDateString();

        // Get homeroom classrooms
        $classrooms = $user->homeroomClassrooms()->with('students')->active()->get();
        $studentIds = $classrooms->flatMap(fn ($c) => $c->students->pluck('id'))->unique();
        $totalStudents = $studentIds->count();

        // Today's attendance for my students
        $todayAttendances = Attendance::whereIn('user_id', $studentIds)->forDate($today)->get();
        $presentCount = $todayAttendances->whereIn('status', [AttendanceStatus::Present, AttendanceStatus::Late])->count();
        $lateCount = $todayAttendances->where('status', AttendanceStatus::Late)->count();
        $absentCount = $totalStudents - $todayAttendances->count();

        // Pending absence requests from my students
        $pendingRequests = AbsenceRequest::whereIn('user_id', $studentIds)->pending()->count();

        // Recent attendance
        $recentAttendances = Attendance::with(['user', 'classroom'])
            ->whereIn('user_id', $studentIds)
            ->latest('check_in_at')
            ->take(10)->get();

        return view('teacher.dashboard', compact(
            'classrooms', 'totalStudents', 'presentCount', 'lateCount',
            'absentCount', 'pendingRequests', 'recentAttendances', 'today'
        ));
    }

    public function classroom()
    {
        $classrooms = auth()->user()->homeroomClassrooms()
            ->with(['students' => fn($q) => $q->withPivot('academic_year', 'semester', 'is_active')->with('studentProfile')])
            ->active()->get();

        return view('teacher.classroom', compact('classrooms'));
    }

    public function attendance(Request $request)
    {
        $user = auth()->user();
        $date = $request->get('date', Carbon::today()->toDateString());
        $classrooms = $user->homeroomClassrooms()->active()->get();
        $studentIds = $classrooms->flatMap(fn($c) => $c->students->pluck('id'))->unique();

        $classroomId = $request->get('classroom_id');
        $query = Attendance::with(['user', 'classroom'])->whereIn('user_id', $studentIds)->forDate($date);
        if ($classroomId) {
            $query->forClassroom($classroomId);
        }

        $attendances = $query->latest('check_in_at')->paginate(20)->withQueryString();
        $statuses = AttendanceStatus::cases();

        return view('teacher.attendance', compact('attendances', 'classrooms', 'date', 'statuses'));
    }

    public function absenceRequests()
    {
        $user = auth()->user();
        $studentIds = $user->homeroomClassrooms()->active()->get()
            ->flatMap(fn($c) => $c->students->pluck('id'))->unique();

        $requests = AbsenceRequest::with(['user', 'reviewer'])
            ->whereIn('user_id', $studentIds)
            ->latest()
            ->paginate(15);

        return view('teacher.absence-requests', compact('requests'));
    }

    public function reviewAbsenceRequest(Request $request, AbsenceRequest $absenceRequest)
    {
        // Verify teacher has access to this student
        $user = auth()->user();
        $studentIds = $user->homeroomClassrooms()->active()->get()
            ->flatMap(fn($c) => $c->students->pluck('id'))->unique();

        if (!$studentIds->contains($absenceRequest->user_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke siswa ini.');
        }

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'review_note' => 'nullable|string|max:500',
        ]);

        $absenceRequest->update([
            'status' => $validated['status'],
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_note' => $validated['review_note'] ?? null,
        ]);

        $label = $validated['status'] === 'approved' ? 'disetujui' : 'ditolak';
        return back()->with('success', "Permohonan izin {$absenceRequest->user->name} telah {$label}.");
    }
}
