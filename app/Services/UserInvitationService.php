<?php

namespace App\Services;

use App\Events\UserInvitationCreatedEvent;
use App\Events\UserInvitationRevokedEvent;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Support\Str;

class UserInvitationService
{
    public static function inviteUser(string $username): UserInvitation
    {
        if (User::where('username', $username)->exists()) {
            throw new \Exception('User already exists');
        }

        $user = User::create([
            'username' => $username,
            'status' => User::STATUS_PENDING
        ]);
        $user->assignRole('user');

        $userInvitation = $user->ownInvitation()->create([
            'invited_by' => auth()->id(),
            'token' => Str::random(16),
            'status' => UserInvitation::STATUS_PENDING
        ]);

        event(new UserInvitationCreatedEvent($userInvitation));

        return $userInvitation;
    }

    public static function revokeInvitation(UserInvitation $invitation): void
    {
        $invitation->update(['status' => UserInvitation::STATUS_REVOKED]);
        event(new UserInvitationRevokedEvent($invitation));
    }
}
