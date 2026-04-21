<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceAnalyticsController extends Controller
{
    public function index()
    {
        $totalStudents = User::role('student')->active()->count();

        // ── Heatmap data: daily attendance rate for the last 365 days ───────
        $heatmapData = Attendance::select(
                'date',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status IN ('present','late') THEN 1 ELSE 0 END) as present_count")
            )
            ->where('date', '>=', Carbon::now()->subYear()->toDateString())
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => [
                'x' => $row->date,
                'y' => $totalStudents > 0 ? round(($row->present_count / $totalStudents) * 100, 1) : 0,
            ]);

        // ── Monthly trend: last 12 months ────────────────────────────────────
        $monthlyTrend = collect();
        for ($i = 11; $i >= 0; $i--) {
            $month     = Carbon::now()->subMonths($i);
            $label     = $month->format('M Y');
            $startDate = $month->copy()->startOfMonth()->toDateString();
            $endDate   = $month->copy()->endOfMonth()->toDateString();

            $stats = Attendance::whereBetween('date', [$startDate, $endDate])
                ->select(DB::raw('COUNT(*) as total'),
                         DB::raw("SUM(CASE WHEN status IN ('present','late') THEN 1 ELSE 0 END) as present"))
                ->first();

            $monthlyTrend->push([
                'label'   => $label,
                'present' => (int) ($stats->present ?? 0),
                'total'   => (int) ($stats->total ?? 0),
                'rate'    => ($stats->total > 0)
                    ? round(($stats->present / $stats->total) * 100, 1)
                    : 0,
            ]);
        }

        // ── Classroom ranking: last 30 days ──────────────────────────────────
        $since      = Carbon::now()->subDays(30)->toDateString();
        $classrooms = Classroom::active()->with('students')->get();

        $classroomRanking = $classrooms->map(function ($classroom) use ($since) {
            $total = Attendance::where('classroom_id', $classroom->id)
                ->where('date', '>=', $since)
                ->count();

            $present = Attendance::where('classroom_id', $classroom->id)
                ->where('date', '>=', $since)
                ->whereIn('status', ['present', 'late'])
                ->count();

            return [
                'name'    => $classroom->name,
                'present' => $present,
                'total'   => $total,
                'rate'    => $total > 0 ? round(($present / $total) * 100, 1) : 0,
            ];
        })->sortByDesc('rate')->values();

        // ── Summary stats ────────────────────────────────────────────────────
        $today        = Carbon::today()->toDateString();
        $todayStats   = Attendance::whereDate('date', $today)
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present"),
                DB::raw("SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late"),
                DB::raw("SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent")
            )->first();

        return view('admin.analytics.index', compact(
            'heatmapData', 'monthlyTrend', 'classroomRanking',
            'totalStudents', 'todayStats'
        ));
    }
}
