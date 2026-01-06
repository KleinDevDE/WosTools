<?php

namespace App\Http\Middleware;

use App\Models\Character;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoadCharacter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('active_character_id')) {
            return $next($request);
        }

        Character::setActiveCharacter(Character::find(session('active_character_id')));
        return $next($request);
    }
}
