<?php

namespace App\Policies;

use App\Models\Alliance;
use App\Models\User;

class AlliancePolicy
{
    public function viewAny(User $user): bool
    {
        $character = $user->activeCharacter();

        if (!$character) {
            return false;
        }

        // Everyone can view alliances (but scoped to state by default)
        return true;
    }

    public function view(User $user, Alliance $alliance): bool
    {
        $character = $user->activeCharacter();

        if (!$character) {
            return false;
        }

        // Developer can view all
        if ($character->hasRole('developer')) {
            return true;
        }

        // Must be same state
        return $alliance->state === $character->state;
    }

    public function create(User $user): bool
    {
        // Only developer can create alliances
        return $user->activeCharacter()?->hasRole('developer') ?? false;
    }

    public function update(User $user, Alliance $alliance): bool
    {
        $character = $user->activeCharacter();

        if (!$character) {
            return false;
        }

        // Developer can update all
        if ($character->hasRole('developer')) {
            return true;
        }

        // Must be same alliance and R5
        return $character->alliance_id === $alliance->id &&
               $character->hasRole('wos_r5');
    }

    public function delete(User $user, Alliance $alliance): bool
    {
        // Only developer can delete alliances
        return $user->activeCharacter()?->hasRole('developer') ?? false;
    }
}
