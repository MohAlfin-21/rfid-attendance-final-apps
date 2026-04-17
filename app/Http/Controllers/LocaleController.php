<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LocaleController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $supportedLocales = array_keys(config('app.supported_locales', []));

        $validated = $request->validate([
            'locale' => ['required', 'string', Rule::in($supportedLocales)],
        ]);

        $locale = $validated['locale'];

        $request->session()->put('locale', $locale);

        if ($request->user() && $request->user()->locale !== $locale) {
            $request->user()->update(['locale' => $locale]);
        }

        return redirect()->back();
    }
}
