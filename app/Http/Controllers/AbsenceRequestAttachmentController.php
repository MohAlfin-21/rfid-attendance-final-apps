<?php

namespace App\Http\Controllers;

use App\Models\AbsenceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AbsenceRequestAttachmentController extends Controller
{
    public function __invoke(Request $request, AbsenceRequest $absenceRequest): StreamedResponse
    {
        abort_unless($this->canView($request->user(), $absenceRequest), 403);
        abort_if(blank($absenceRequest->attachment_path), 404);
        abort_unless(Storage::disk('public')->exists($absenceRequest->attachment_path), 404);

        return Storage::disk('public')->response($absenceRequest->attachment_path);
    }

    private function canView(User $user, AbsenceRequest $absenceRequest): bool
    {
        if ($user->hasRole(['admin', 'secretary'])) {
            return true;
        }

        if ((int) $absenceRequest->user_id === (int) $user->id) {
            return true;
        }

        if (! $user->hasRole('teacher')) {
            return false;
        }

        return $user->homeroomClassrooms()
            ->active()
            ->whereHas('students', fn ($students) => $students
                ->whereKey($absenceRequest->user_id)
                ->where('classroom_students.is_active', true))
            ->exists();
    }
}
