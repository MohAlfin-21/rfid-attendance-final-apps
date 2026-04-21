<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AttendanceExportController extends Controller
{
    public function export(Request $request): Response|\Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'classroom_id' => 'nullable|exists:classrooms,id',
            'date_from'    => 'required|date',
            'date_to'      => 'required|date|after_or_equal:date_from',
            'format'       => 'required|in:csv,print',
        ]);

        $query = Attendance::with(['user', 'classroom'])
            ->whereBetween('date', [$validated['date_from'], $validated['date_to']])
            ->orderBy('date')
            ->orderBy('classroom_id');

        if (! empty($validated['classroom_id'])) {
            $query->where('classroom_id', $validated['classroom_id']);
        }

        $attendances = $query->get();

        if ($validated['format'] === 'csv') {
            return $this->exportCsv($attendances, $validated);
        }

        return $this->exportPrint($attendances, $validated);
    }

    // ──────────────────────────────────────────────
    // Private helpers
    // ──────────────────────────────────────────────

    private function exportCsv($attendances, array $params): Response
    {
        $classroom = ! empty($params['classroom_id'])
            ? Classroom::find($params['classroom_id'])?->name ?? 'Semua'
            : 'Semua';

        $filename = 'rekap-absensi_'
            . str_replace(' ', '-', $classroom) . '_'
            . $params['date_from'] . '_sd_' . $params['date_to']
            . '.csv';

        $rows   = [];
        $rows[] = ['No', 'Tanggal', 'NIS', 'Nama Siswa', 'Kelas', 'Status',
                   'Check-in', 'Check-out', 'Metode Check-in', 'Keterangan'];

        $i = 1;
        foreach ($attendances as $a) {
            $rows[] = [
                $i++,
                $a->date->format('d/m/Y'),
                $a->user->nis ?? '—',
                $a->user->name ?? '—',
                $a->classroom->name ?? '—',
                $a->status->label(),
                $a->check_in_at?->setTimezone('Asia/Jakarta')->format('H:i') ?? '—',
                $a->check_out_at?->setTimezone('Asia/Jakarta')->format('H:i') ?? '—',
                $a->check_in_method?->value ?? '—',
                $a->override_note ?? '',
            ];
        }

        $output = '';
        foreach ($rows as $row) {
            $output .= implode(',', array_map(fn ($v) => '"' . str_replace('"', '""', $v) . '"', $row)) . "\n";
        }

        return response($output, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function exportPrint($attendances, array $params): Response
    {
        $classroom = ! empty($params['classroom_id'])
            ? Classroom::find($params['classroom_id'])?->name ?? 'Semua Kelas'
            : 'Semua Kelas';

        $html = view('admin.attendances.export-print', compact('attendances', 'params', 'classroom'))->render();

        return response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }
}
