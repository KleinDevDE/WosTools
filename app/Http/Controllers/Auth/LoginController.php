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
        $existingUser = User::where('player_id', $loginRequest->player_id)->first();
        if (!$existingUser) {
            return redirect()->back()->withErrors(['username' => 'No account found with this username.']);
        }

        if (!Auth::attempt(['player_id' => $loginRequest->player_id, 'password' => $loginRequest->password], true)) {
            return redirect()->back()->withErrors(['username' => 'Invalid credentials']);
        }

        Auth::user()->update(['last_login_at' => now()]);

        return redirect()->intended('/');
    }
}
