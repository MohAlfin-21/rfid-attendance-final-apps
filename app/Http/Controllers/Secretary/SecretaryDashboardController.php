<?php

namespace App\Http\Controllers\Secretary;

use App\Domain\Attendance\Enums\AttendanceStatus;
use App\Http\Controllers\Controller;
use App\Models\AbsenceRequest;
use App\Models\Attendance;
use App\Models\Classroom;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SecretaryDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today()->toDateString();

        // Own attendance (if student too)
        $todayAttendance = Attendance::where('user_id', $user->id)->forDate($today)->first();

        // School-wide stats for today
        $classrooms = Classroom::active()->with('students')->get();
        $allStudentIds = $classrooms->flatMap(fn($c) => $c->students->pluck('id'))->unique();
        $todayAttendances = Attendance::whereIn('user_id', $allStudentIds)->forDate($today)->get();
        $presentCount = $todayAttendances->whereIn('status', [AttendanceStatus::Present, AttendanceStatus::Late])->count();

        // Pending absence requests
        $pendingRequests = AbsenceRequest::pending()->count();
        $totalRequests = AbsenceRequest::count();

        return view('secretary.dashboard', compact(
            'todayAttendance', 'presentCount', 'allStudentIds',
            'pendingRequests', 'totalRequests', 'classrooms'
        ));
    }

    public function absenceRequests()
    {
        $requests = AbsenceRequest::with(['user', 'reviewer'])
            ->latest()
            ->paginate(15);

        return view('secretary.absence-requests', compact('requests'));
    }

    public function reviewAbsenceRequest(Request $request, AbsenceRequest $absenceRequest)
    {
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
