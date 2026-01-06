<?php

namespace App\Http\Middleware;

use App\Models\Character;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CharacterCanAny
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$abilities): Response
    {
        if (!Character::getActiveCharacter()?->canAny($abilities)) {
            return response('Forbidden', 403);
        }

        return $next($request);
    }
}
