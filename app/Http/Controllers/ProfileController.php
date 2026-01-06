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
     * Show the profile settings page
     */
    public function show(): View
    {
        return view('profile.settings', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Delete a single character
     */
    public function deleteCharacter($characterId): RedirectResponse
    {
        $user = Auth::user();
        $character = $user->characters()->findOrFail($characterId);

        // Prevent deletion of last character
        if ($user->characters()->count() <= 1) {
            return redirect()->back()
                ->withErrors(['character' => 'Cannot delete your last character.']);
        }

        // If this is the active character, switch to another one
        if (session('active_character_id') == $character->id) {
            $newCharacter = $user->characters()->where('id', '!=', $character->id)->first();
            session(['active_character_id' => $newCharacter->id]);
        }

        $character->delete();

        return redirect()->route('profile.show')
            ->with('success', 'Character deleted successfully.');
    }

    /**
     * Delete the entire user account
     */
    public function deleteAccount(): RedirectResponse
    {
        $user = Auth::user();

        // Logout first
        Auth::guard('web')->logout();
        Auth::guard('character')->logout();

        // Delete user (cascades to characters)
        $user->delete();

        return redirect()->route('auth.login')
            ->with('success', 'Your account has been deleted.');
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
                    ->withErrors(['current_password' => __('profile.password_incorrect')]);
            }

            // Set new password (will be hashed by User model's 'hashed' cast)
            $data['password'] = $request->new_password;
        }

        // Update user
        if (!empty($data)) {
            $user->update($data);
        }

        return redirect()->route('profile.show')
            ->with('success', __('profile.updated'));
    }
}
