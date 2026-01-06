<?php

namespace App\Http\Controllers;

use App\Models\Character;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CharacterSwitchController extends Controller
{
    /**
     * Switch to a different character (re-login with character guard)
     */
    public function switch(Request $request, Character $character): RedirectResponse
    {
        $user = auth()->guard('web')->user();

        // Verify that the character belongs to the authenticated user
        if ($character->user_id !== $user->id) {
            abort(403, 'Unauthorized to switch to this character.');
        }

        // Re-login with the new character
        auth()->guard('character')->login($character, true);

        return redirect()->back()->with('success', 'Switched to character: ' . $character->player_name);
    }

    /**
     * Get all characters for the current user grouped by state
     */
    public function list()
    {
        $user = auth()->guard('web')->user();
        $activeCharacter = auth()->guard('character')->user();

        $characters = $user->characters()
            ->with(['stateRelation', 'alliance'])
            ->get()
            ->groupBy('state');

        return response()->json([
            'characters' => $characters,
            'active_character_id' => $activeCharacter?->id,
        ]);
    }
}
