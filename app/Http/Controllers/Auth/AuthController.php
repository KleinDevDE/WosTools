<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function logout()
    {
        auth()->guard('character')->logout();
        auth()->guard('web')->logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('auth.login');
    }
}
