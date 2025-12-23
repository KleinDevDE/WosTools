<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->getLocale($request);

        // Validate locale
        $availableLocales = config('app.available_locales', ['en', 'de', 'tr']);
        if (!in_array($locale, $availableLocales)) {
            $locale = config('app.fallback_locale', 'en');
        }

        // Set application locale
        App::setLocale($locale);

        // Set Carbon locale for date formatting
        setlocale(LC_TIME, $this->getCarbonLocale($locale));

        // Store in session for non-authenticated users
        Session::put('locale', $locale);

        return $next($request);
    }

    /**
     * Get the locale from various sources.
     */
    private function getLocale(Request $request): string
    {
        // Priority 1: Authenticated user's preference
        if (Auth::check() && Auth::user()->locale) {
            return Auth::user()->locale;
        }

        // Priority 2: Session
        if (Session::has('locale')) {
            return Session::get('locale');
        }

        // Priority 3: Browser language
        $browserLang = substr($request->server('HTTP_ACCEPT_LANGUAGE', ''), 0, 2);
        $availableLocales = config('app.available_locales', ['en', 'de', 'tr']);
        if (in_array($browserLang, $availableLocales)) {
            return $browserLang;
        }

        // Priority 4: Default fallback
        return config('app.locale', 'en');
    }

    /**
     * Get the Carbon locale string for setlocale().
     */
    private function getCarbonLocale(string $locale): string
    {
        return match ($locale) {
            'de' => 'de_DE.UTF-8',
            'tr' => 'tr_TR.UTF-8',
            default => 'en_US.UTF-8',
        };
    }
}