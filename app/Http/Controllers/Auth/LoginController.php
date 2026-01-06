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

        // Step 2: Get the user's first character
        $firstCharacter = $user->characters()->first();

        if (!$firstCharacter) {
            Auth::guard('web')->logout();
            return redirect()->back()->withErrors(['username' => 'No character found for this account.']);
        }

        // Step 3: Authenticate Character with character guard
        session(['active_character_id' => $firstCharacter->id]);

        return redirect()->intended('/');
    }
}
