<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = array_keys(config('app.supported_locales', []));
        $locale = $request->session()->get('locale');

        if (! in_array($locale, $supportedLocales, true)) {
            $locale = $request->user()?->locale;
        }

        if (! in_array($locale, $supportedLocales, true)) {
            $locale = config('app.locale');
        }

        App::setLocale($locale);
        Date::setLocale($locale);
        $request->session()->put('locale', $locale);

        return $next($request);
    }
}
