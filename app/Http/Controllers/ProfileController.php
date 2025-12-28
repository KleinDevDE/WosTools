<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the profile edit form
     */
    public function show(): View
    {
        return view('profile.edit', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Update the user's profile
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $data = [];

        // Update display_name
        if ($request->has('display_name')) {
            $data['display_name'] = $request->input('display_name') ?: null;
        }

        // Update username
        if ($request->filled('username') && $request->username !== $user->username) {
            $data['username'] = $request->username;
        }

        // Update password if provided
        if ($request->filled('current_password') && $request->filled('new_password')) {
            // Verify current password (already SHA256 hashed from frontend)
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()
                    ->withInput($request->except(['current_password', 'new_password']))
                    ->withErrors(['current_password' => 'Current password is incorrect.']);
            }

            // Set new password (will be hashed by User model's 'hashed' cast)
            $data['password'] = $request->new_password;
        }

        // Update user
        if (!empty($data)) {
            $user->update($data);
        }

        return redirect()->route('profile.show')
            ->with('success', 'Profile updated successfully.');
    }
}
