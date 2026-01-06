<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function logout()
    {
        auth()->logout();
        session()->forget('active_character_id');
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('auth.login');
    }
}
