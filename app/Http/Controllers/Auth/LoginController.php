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

        if ($existingUser->status === User::STATUS_INACTIVE) {
            return redirect()->back()->withErrors(['username' => 'Your account is disabled. Please contact the management']);
        }

        if ($existingUser->status === User::STATUS_PENDING) {
            return redirect()->back()->withErrors(['username' => 'Your account is pending approval. Please wait for the approval.']);
        }

        if (!Auth::attempt(['username' => $loginRequest->username, 'password' => $loginRequest->password])) {
            return redirect()->back()->withErrors(['username' => 'Invalid credentials']);
        }

        return redirect()->intended('/');
    }
}
