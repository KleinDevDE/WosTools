<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\UserInvitation;
use Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function show(Request $request): RedirectResponse|View
    {
        if (!$request->hasValidSignature(true)) {
            return view('auth.register')->withErrors(['token' => 'Could not determine registration token, please insert it manually.']);
        }

        $userInvitation = UserInvitation::where('token', $request->token)->first();
        if (!$userInvitation) {
            return view('auth.register')->withErrors(['token' => 'Invalid registration token!']);
        }

        if ($userInvitation->status === UserInvitation::STATUS_ACCEPTED) {
            return redirect()->route('auth.login')->withErrors(['username' => 'Your account has already been activated, please login.']);
        }

        return view('auth.register', ['userInvitation' => $userInvitation]);
    }

    public function process(RegisterRequest $registerRequest)
    {
        $userInvitation = UserInvitation::with('user')->where('token', $registerRequest->token)->first();
        if (!$userInvitation) {
            return redirect()->back()->withInput($registerRequest->only(['username', 'token']))->withErrors(['token' => 'Invalid registration token!']);
        }

        $userInvitation->user->update(['status' => User::STATUS_ACTIVE, 'last_login_at' => now(), 'password' => ($registerRequest->password)]);
        $userInvitation->status = UserInvitation::STATUS_ACCEPTED;
        $userInvitation->accepted_at = now();
        $userInvitation->save();


        Auth::login($userInvitation->user);
        return redirect()->intended('/');
    }
}
