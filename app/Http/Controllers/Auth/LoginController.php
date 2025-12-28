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
        $existingUser = User::where('username', $loginRequest->username)->first();
        if (!$existingUser) {
            return redirect()->back()->withErrors(['username' => 'No account found with this email.']);
        }

        if ($existingUser->status === User::STATUS_LOCKED) {
            \Session::flash('error_account_locked', true);
            return redirect()->back();
        }

        if (!Auth::attempt(['username' => $loginRequest->username, 'password' => $loginRequest->password], true)) {
            return redirect()->back()->withErrors(['username' => 'Invalid credentials']);
        }

        Auth::user()->update(['last_login_at' => now()]);

        return redirect()->intended('/');
    }
}
