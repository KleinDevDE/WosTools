<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToAlliance
{
    protected static function bootBelongsToAlliance(): void
    {
        static::addGlobalScope('alliance', function (Builder $query) {
            $user = auth()->user();

            if (!$user) {
                return;
            }

            $activeCharacter = $user->activeCharacter();

            if (!$activeCharacter) {
                return;
            }

            // Developer in God Mode: No filtering
            if (session('god_mode_enabled') && $activeCharacter->hasRole('developer')) {
                return;
            }

            // Normal filtering: Only own state and alliance
            $query->where('state', $activeCharacter->state)
                  ->where('alliance_id', $activeCharacter->alliance_id);
        });
    }
}
