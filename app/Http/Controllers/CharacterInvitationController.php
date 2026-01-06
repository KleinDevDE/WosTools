<?php

namespace App\Http\Controllers;

use App\Models\CharacterInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CharacterInvitationController extends Controller
{
    /**
     * Display pending invitations for the authenticated user
     */
    public function index(): View
    {
        $character = auth('character')->user();

        // Get pending invitations for alliances managed by the active character
        $sentInvitations = CharacterInvitation::query()
            ->where('invited_by_character_id', $character?->id)
            ->whereIn('status', [CharacterInvitation::STATUS_PENDING])
            ->with(['alliance'])
            ->get();

        return view('invitations.index', compact('sentInvitations'));
    }

    /**
     * Create a new character invitation
     */
    public function create(Request $request): RedirectResponse
    {
        $character = auth('character')->user();

        // Check permission to invite
        if (!$character || !$character->hasAnyRole(['wos_r4', 'wos_r5', 'developer'])) {
            abort(403, 'You do not have permission to invite characters.');
        }

        $validated = $request->validate([
            'player_id' => 'required|integer',
        ]);

        // Check if character already exists
        if (\App\Models\Character::where('player_id', $validated['player_id'])->exists()) {
            return redirect()->back()->withErrors(['player_id' => 'A character with this player ID already exists.']);
        }

        // Check if pending invitation already exists
        $existingInvitation = CharacterInvitation::query()
            ->where('player_id', $validated['player_id'])
            ->where('alliance_id', $character->alliance_id)
            ->where('status', CharacterInvitation::STATUS_PENDING)
            ->first();

        if ($existingInvitation) {
            return redirect()->back()->withErrors(['player_id' => 'An invitation for this player already exists.']);
        }

        // Create new invitation
        $invitation = CharacterInvitation::create([
            'player_id' => $validated['player_id'],
            'alliance_id' => $character->alliance_id,
            'invited_by_character_id' => $character->id,
            'token' => Str::random(64),
            'status' => CharacterInvitation::STATUS_PENDING,
        ]);

        return redirect()->back()->with('success', 'Invitation created successfully. URL: ' . $invitation->invitation_url);
    }

    /**
     * Accept an invitation and create a character
     */
    public function accept(Request $request, string $token): RedirectResponse|View
    {
        $invitation = CharacterInvitation::where('token', $token)
            ->where('status', CharacterInvitation::STATUS_PENDING)
            ->firstOrFail();

        // If user is not authenticated, show registration form with token
        if (!auth('web')->check()) {
            return view('auth.register', compact('invitation'));
        }

        // User is authenticated, create character and accept invitation
        $user = auth('web')->user();

        // Check if character already exists
        if (\App\Models\Character::where('player_id', $invitation->player_id)->exists()) {
            return redirect()->route('dashboard')->withErrors(['error' => 'Character already exists.']);
        }

        // Create new character
        $character = \App\Models\Character::create([
            'user_id' => $user->id,
            'player_id' => $invitation->player_id,
            'player_name' => 'Player ' . $invitation->player_id, // Placeholder, should be updated
            'state' => $invitation->alliance->state,
            'alliance_id' => $invitation->alliance_id,
        ]);

        // Assign default 'user' role
        $character->assignRole('user');

        // Update invitation status
        $invitation->update([
            'status' => CharacterInvitation::STATUS_ACCEPTED,
            'accepted_at' => now(),
        ]);

        // Login with the new character
        auth()->guard('character')->login($character, true);

        return redirect()->route('dashboard')->with('success', 'Welcome! Your character has been created.');
    }

    /**
     * Decline an invitation
     */
    public function decline(string $token): RedirectResponse
    {
        $invitation = CharacterInvitation::where('token', $token)
            ->where('status', CharacterInvitation::STATUS_PENDING)
            ->firstOrFail();

        $invitation->update([
            'status' => CharacterInvitation::STATUS_DECLINED,
            'declined_at' => now(),
        ]);

        return redirect()->route('auth.login')->with('info', 'Invitation declined.');
    }

    /**
     * Revoke a sent invitation
     */
    public function revoke(CharacterInvitation $invitation): RedirectResponse
    {
        $character = auth('character')->user();

        // Verify permission
        if ($invitation->invited_by_character_id !== $character->id) {
            abort(403, 'Unauthorized to revoke this invitation.');
        }

        if ($invitation->status !== CharacterInvitation::STATUS_PENDING) {
            return redirect()->back()->withErrors(['error' => 'Cannot revoke a non-pending invitation.']);
        }

        $invitation->update([
            'status' => CharacterInvitation::STATUS_REVOKED,
            'revoked_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Invitation revoked.');
    }
}
