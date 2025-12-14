<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\UserInvitation;
use Auth;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function show(Request $request): View
    {
        if (!$request->hasValidSignature(true)) {
            return view('auth.register')->withErrors(['token' => 'Could not determine registration token, please insert it manually.']);
        }

        return view('auth.register', ['token' => $request->token]);
    }

    public function process(RegisterRequest $registerRequest)
    {
        $userInvitation = UserInvitation::with('user')->where('token', $registerRequest->token)->first();
        if (!$userInvitation) {
            return redirect()->back()->withInput($registerRequest->only(['username', 'token']))->withErrors(['token' => 'Invalid registration token!']);
        }

        $userInvitation->user->update(['status' => User::STATUS_ACTIVE]);
        $userInvitation->status = UserInvitation::STATUS_ACCEPTED;
        $userInvitation->accepted_at = now();
        $userInvitation->save();


        Auth::login($userInvitation->user);
        return redirect()->intended('/');
    }
}
