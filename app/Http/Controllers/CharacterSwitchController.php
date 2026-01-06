<?php

namespace App\Http\Controllers;

use App\Models\Character;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CharacterSwitchController extends Controller
{
    /**
     * Switch to a different character
     */
    public function switch(Request $request, Character $character): RedirectResponse
    {
        $user = auth()->user();

        // Verify that the character belongs to the authenticated user
        if ($character->user_id !== $user->id) {
            abort(403, 'Unauthorized to switch to this character.');
        }

        // Set the new active character in session
        session(['active_character_id' => $character->id]);

        return redirect()->back()->with('success', 'Switched to character: ' . $character->player_name);
    }

    /**
     * Get all characters for the current user grouped by state
     */
    public function list()
    {
        $user = auth()->user();
        $activeCharacterId = session('active_character_id');

        $characters = $user->characters()
            ->with(['stateRelation', 'alliance'])
            ->get()
            ->groupBy('state');

        return response()->json([
            'characters' => $characters,
            'active_character_id' => $activeCharacterId,
        ]);
    }
}
