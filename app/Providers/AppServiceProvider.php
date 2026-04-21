<?php

namespace App\Providers;

use App\Models\AbsenceRequest;
use App\Observers\AbsenceRequestObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * Memaksa semua URL yang di-generate Laravel menggunakan HTTPS
     * agar redirect() tidak menghasilkan http:// saat diakses via ngrok.
     */
    public function boot(): void
    {
        // Percayai semua reverse proxy (ngrok, Cloudflare Tunnel, dll.)
        // sehingga Laravel membaca X-Forwarded-Proto: https dengan benar.
        Request::setTrustedProxies(
            ['127.0.0.1', '::1', '*'],
            Request::HEADER_X_FORWARDED_FOR |
            Request::HEADER_X_FORWARDED_HOST |
            Request::HEADER_X_FORWARDED_PORT |
            Request::HEADER_X_FORWARDED_PROTO
        );

        // Paksa scheme HTTPS untuk semua URL yang di-generate Laravel.
        // Ini memastikan redirect()->route('login') → https://... bukan http://...
        if ($this->app->environment('local')) {
            URL::forceScheme('https');
        }

        // ── Observers ───────────────────────────────
        // Auto-sync absence requests yang diapprove ke tabel attendances.
        AbsenceRequest::observe(AbsenceRequestObserver::class);
    }
}
