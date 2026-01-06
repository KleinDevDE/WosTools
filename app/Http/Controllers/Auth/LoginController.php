<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function show(): View
    {
        return view('auth.login');
    }

    public function process(LoginRequest $loginRequest)
    {
        // Step 1: Authenticate User with web guard
        if (!Auth::guard('web')->attempt(['username' => $loginRequest->username, 'password' => $loginRequest->password], true)) {
            return redirect()->back()->withErrors(['username' => 'Invalid credentials']);
        }

        $user = Auth::guard('web')->user();
        $user->update(['last_login_at' => now()]);

        // Step 2: Check if user has characters
        if ($user->characters()->count() === 0) {
            Auth::guard('web')->logout();
            return redirect()->back()->withErrors(['username' => 'No character found for this account.']);
        }

        // Step 3: Redirect to character selection
        return redirect()->route('character.select');
    }

    public function showCharacterSelect(): View
    {
        // Ensure user is authenticated via web guard
        if (!Auth::guard('web')->check()) {
            return redirect()->route('login');
        }

        $user = Auth::guard('web')->user();
        $characters = $user->characters()->with(['stateRelation', 'alliance'])->get();

        return view('auth.character-select', compact('characters'));
    }

    public function selectCharacter($characterId)
    {
        // Ensure user is authenticated via web guard
        if (!Auth::guard('web')->check()) {
            return redirect()->route('login');
        }

        $user = Auth::guard('web')->user();
        $character = $user->characters()->find($characterId);

        if (!$character) {
            return redirect()->route('character.select')->withErrors(['character' => 'Character not found.']);
        }

        // Set active character in session
        session(['active_character_id' => $character->id]);

        return redirect()->intended('/');
    }
}
