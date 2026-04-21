<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsenceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AbsenceRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = AbsenceRequest::with(['user', 'reviewer']);

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        } else {
            $query->pending();  // Default: show pending first
        }

        $requests = $query->latest()->paginate(15)->withQueryString();
        return view('admin.absence-requests.index', compact('requests'));
    }

    public function show(AbsenceRequest $absenceRequest)
    {
        $absenceRequest->load(['user', 'reviewer']);
        return view('admin.absence-requests.show', compact('absenceRequest'));
    }

    public function update(Request $request, AbsenceRequest $absenceRequest): RedirectResponse
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

        $message = $validated['status'] === 'approved'
            ? __('Permohonan izin :name telah disetujui.', ['name' => $absenceRequest->user->name])
            : __('Permohonan izin :name telah ditolak.', ['name' => $absenceRequest->user->name]);

        return redirect()->route('admin.absence-requests.index')->with('success', $message);
    }
}
