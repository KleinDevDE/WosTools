<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    /**
     * Switch the application locale.
     */
    public function switch(Request $request): RedirectResponse
    {
        $locale = $request->input('locale');

        // Validate locale
        $availableLocales = config('app.available_locales', ['en', 'de', 'tr']);
        if (!in_array($locale, $availableLocales)) {
            return back()->with('error', 'Invalid language selected.');
        }

        // Update user's locale preference if authenticated
        if (Auth::check()) {
            Auth::user()->update(['locale' => $locale]);
        }

        // Store in session
        Session::put('locale', $locale);

        // Store in localStorage via cookie for Vue.js SPA
        cookie()->queue('locale', $locale, 525600); // 1 year

        return back()->with('success', 'Language changed successfully.');
    }
}