<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Devices\Services\CardEnrollmentSessionService;
use App\Domain\Devices\Services\RfidCardRegistrationService;
use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\RfidCard;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserCardController extends Controller
{
    public function store(Request $request, User $user, RfidCardRegistrationService $registrationService): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'uid' => 'required|string|min:4|max:64',
        ]);

        $card = $registrationService->register($user, $validated['uid']);

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'message' => __('Kartu RFID berhasil didaftarkan untuk :name.', ['name' => $user->name]),
                'card' => $this->cardPayload($card),
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                ],
            ]);
        }

        return back()->with('success', __('Kartu RFID berhasil didaftarkan untuk :name.', ['name' => $user->name]));
    }

    public function startEnrollment(
        Request $request,
        User $user,
        CardEnrollmentSessionService $sessionService,
    ): JsonResponse {
        $validated = $request->validate([
            'device_id' => 'required|integer|exists:devices,id',
        ]);

        $device = Device::query()
            ->active()
            ->findOrFail($validated['device_id']);

        $session = $sessionService->start($device, $user, (int) $request->user()->id);

        return response()->json([
            'ok' => true,
            'message' => __('Mode auto-read aktif di :device. Tempelkan kartu sekarang.', [
                'device' => $device->name,
            ]),
            'session' => $this->sessionPayload($session),
        ]);
    }

    public function enrollmentStatus(
        Request $request,
        User $user,
        CardEnrollmentSessionService $sessionService,
    ): JsonResponse {
        $validated = $request->validate([
            'device_id' => 'required|integer|exists:devices,id',
            'session_id' => 'required|string',
        ]);

        $device = Device::query()->findOrFail($validated['device_id']);
        $session = $sessionService->getMatchingSession($device, $validated['session_id']);

        if (! $session || (int) $session['user_id'] !== (int) $user->id || (int) $session['started_by'] !== (int) $request->user()->id) {
            return response()->json([
                'ok' => true,
                'active' => false,
                'status' => 'expired',
                'message' => __('Sesi auto-read sudah berakhir atau digantikan sesi baru.'),
            ]);
        }

        return response()->json([
            'ok' => true,
            'active' => ($session['status'] ?? null) === 'waiting',
            'status' => $session['status'],
            'message' => $session['message'],
            'session' => $this->sessionPayload($session),
        ]);
    }

    public function cancelEnrollment(
        Request $request,
        User $user,
        CardEnrollmentSessionService $sessionService,
    ): JsonResponse {
        $validated = $request->validate([
            'device_id' => 'required|integer|exists:devices,id',
            'session_id' => 'nullable|string',
        ]);

        $device = Device::query()->findOrFail($validated['device_id']);
        $session = $validated['session_id']
            ? $sessionService->getMatchingSession($device, $validated['session_id'])
            : $sessionService->get($device);

        if ($session && ((int) $session['user_id'] !== (int) $user->id || (int) $session['started_by'] !== (int) $request->user()->id)) {
            return response()->json([
                'ok' => false,
                'message' => __('Sesi auto-read ini tidak bisa dibatalkan dari akun Anda.'),
            ], 403);
        }

        $sessionService->cancel($device, $validated['session_id'] ?? null);

        return response()->json([
            'ok' => true,
            'message' => __('Mode auto-read dihentikan.'),
        ]);
    }

    protected function cardPayload(RfidCard $card): array
    {
        return [
            'id' => $card->id,
            'uid' => $card->uid,
            'status' => $card->status->value,
            'status_label' => $card->status->label(),
            'registered_at' => $card->registered_at?->format('d M Y H:i'),
        ];
    }

    protected function sessionPayload(array $session): array
    {
        return [
            'id' => $session['id'] ?? null,
            'device_id' => $session['device_id'] ?? null,
            'device_name' => $session['device_name'] ?? null,
            'user_id' => $session['user_id'] ?? null,
            'user_name' => $session['user_name'] ?? null,
            'status' => $session['status'] ?? null,
            'message' => $session['message'] ?? null,
            'uid' => $session['uid'] ?? null,
            'card' => $session['card'] ?? null,
            'started_at' => $session['started_at'] ?? null,
            'completed_at' => $session['completed_at'] ?? null,
            'expires_at' => $session['expires_at'] ?? null,
        ];
    }
}
