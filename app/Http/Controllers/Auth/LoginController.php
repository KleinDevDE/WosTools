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
        // Attempt to authenticate with username and password
        if (!Auth::attempt(['username' => $loginRequest->username, 'password' => $loginRequest->password], true)) {
            return redirect()->back()->withErrors(['username' => 'Invalid credentials']);
        }

        $user = Auth::user();
        $user->update(['last_login_at' => now()]);

        // Get the user's first character and set as active
        $firstCharacter = $user->characters()->first();

        if (!$firstCharacter) {
            Auth::logout();
            return redirect()->back()->withErrors(['username' => 'No character found for this account.']);
        }

        // Set active character in session
        session(['active_character_id' => $firstCharacter->id]);

        return redirect()->intended('/');
    }
}
