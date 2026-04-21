<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Queued job to send a WhatsApp notification via Fonnte API.
 *
 * To activate: set FONNTE_TOKEN in .env
 * If token is not configured, the job logs a warning and exits gracefully.
 *
 * Fonnte API docs: https://fonnte.com/docs
 */
class SendWhatsappNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public readonly string $phone,
        public readonly string $message,
    ) {}

    public function handle(): void
    {
        $token = config('services.fonnte.token');

        if (empty($token)) {
            Log::warning('[SendWhatsappNotification] FONNTE_TOKEN is not configured. Skipping WA notification.', [
                'phone'   => $this->phone,
                'message' => $this->message,
            ]);
            return;
        }

        $phone = $this->normalizePhone($this->phone);

        try {
            $response = Http::withHeaders(['Authorization' => $token])
                ->post('https://api.fonnte.com/send', [
                    'target'  => $phone,
                    'message' => $this->message,
                ]);

            if (! $response->successful()) {
                Log::error('[SendWhatsappNotification] Fonnte API error.', [
                    'status'   => $response->status(),
                    'body'     => $response->body(),
                    'phone'    => $phone,
                ]);
                // Re-throw to trigger job retry
                $this->fail(new \RuntimeException('Fonnte API returned HTTP ' . $response->status()));
                return;
            }

            Log::info('[SendWhatsappNotification] WA sent successfully.', ['phone' => $phone]);
        } catch (\Throwable $e) {
            Log::error('[SendWhatsappNotification] Exception: ' . $e->getMessage());
            $this->fail($e);
        }
    }

    /**
     * Normalize phone to Indonesian format (62xxx).
     */
    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        return $phone;
    }
}
