<?php

namespace App\Domain\Devices\Services;

use App\Domain\Devices\Enums\CardStatus;
use App\Models\RfidCard;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class RfidCardRegistrationService
{
    public function register(User $user, string $rawUid): RfidCard
    {
        $uid = RfidCard::normalizeUid($rawUid);

        if ($uid === '') {
            throw ValidationException::withMessages([
                'uid' => __('UID kartu wajib diisi.'),
            ]);
        }

        $existingCard = RfidCard::query()
            ->with('user')
            ->byUid($uid)
            ->first();

        if ($existingCard && (int) $existingCard->user_id !== (int) $user->id) {
            throw ValidationException::withMessages([
                'uid' => __('UID kartu sudah terdaftar untuk :name.', [
                    'name' => $existingCard->user?->name ?? __('pengguna lain'),
                ]),
            ]);
        }

        if ($existingCard) {
            $existingCard->fill([
                'status' => CardStatus::Active,
                'lost_at' => null,
                'registered_at' => now(),
            ])->save();

            return $existingCard->refresh();
        }

        /** @var RfidCard $card */
        $card = $user->rfidCards()->create([
            'uid' => $uid,
            'status' => CardStatus::Active,
            'registered_at' => now(),
            'lost_at' => null,
        ]);

        return $card;
    }
}
