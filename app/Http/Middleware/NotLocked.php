<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class NotLocked
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (App::runningInConsole()) {
            return $next($request);
        }

        if (!\auth()->hasUser()) {
            return $next($request);
        }

        if (\auth()->user()->status === User::STATUS_LOCKED) {
            \Session::flash('error_account_locked', true);
            \Auth::logout();
            return redirect()->route('auth.login');
        }

        return $next($request);
    }
}
