<?php

namespace App\Policies;

use App\Models\Character;
use App\Models\User;

class CharacterPolicy
{
    public function viewAny(User $user): bool
    {
        $character = $user->activeCharacter();

        if (!$character) {
            return false;
        }

        // Developer can see all
        if ($character->hasRole('developer')) {
            return true;
        }

        // R4 and R5 can see alliance members
        return $character->hasAnyRole(['wos_r4', 'wos_r5']);
    }

    public function view(User $user, Character $character): bool
    {
        $activeCharacter = $user->activeCharacter();

        if (!$activeCharacter) {
            return false;
        }

        // Developer can view all
        if ($activeCharacter->hasRole('developer')) {
            return true;
        }

        // Own character
        if ($character->user_id === $user->id) {
            return true;
        }

        // Must be same state and alliance
        if ($character->state !== $activeCharacter->state ||
            $character->alliance_id !== $activeCharacter->alliance_id) {
            return false;
        }

        // R4 and R5 can view alliance members
        return $activeCharacter->hasAnyRole(['wos_r4', 'wos_r5']);
    }

    public function create(User $user): bool
    {
        // Only developer can create characters directly
        return $user->activeCharacter()?->hasRole('developer') ?? false;
    }

    public function update(User $user, Character $character): bool
    {
        $activeCharacter = $user->activeCharacter();

        if (!$activeCharacter) {
            return false;
        }

        // Developer can update all
        if ($activeCharacter->hasRole('developer')) {
            return true;
        }

        // Must be same state and alliance
        if ($character->state !== $activeCharacter->state ||
            $character->alliance_id !== $activeCharacter->alliance_id) {
            return false;
        }

        // R5 and R4 can update alliance members
        if ($activeCharacter->hasAnyRole(['wos_r5', 'wos_r4'])) {
            return true;
        }

        // User can update own character
        return $character->user_id === $user->id;
    }

    public function delete(User $user, Character $character): bool
    {
        // Only user can delete their own characters
        return $character->user_id === $user->id;
    }

    public function kick(User $user, Character $character): bool
    {
        $activeCharacter = $user->activeCharacter();

        if (!$activeCharacter) {
            return false;
        }

        // Developer can kick all
        if ($activeCharacter->hasRole('developer')) {
            return true;
        }

        // Must be same state and alliance
        if ($character->state !== $activeCharacter->state ||
            $character->alliance_id !== $activeCharacter->alliance_id) {
            return false;
        }

        // R5 can kick anyone except themselves
        if ($activeCharacter->hasRole('wos_r5')) {
            return $character->id !== $activeCharacter->id;
        }

        // R4 can kick only normal users
        if ($activeCharacter->hasRole('wos_r4')) {
            return !$character->hasAnyRole(['wos_r4', 'wos_r5']);
        }

        return false;
    }

    public function invite(User $user): bool
    {
        $activeCharacter = $user->activeCharacter();

        if (!$activeCharacter) {
            return false;
        }

        // Developer, R5, R4 can invite
        return $activeCharacter->hasAnyRole(['developer', 'wos_r5', 'wos_r4']);
    }
}
