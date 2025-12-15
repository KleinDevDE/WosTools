<?php

namespace App\Services;

use App\Events\UserInvitationCreatedEvent;
use App\Events\UserInvitationRevokedEvent;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Support\Str;

class UserInvitationService
{
    public static function inviteUser(string $username): ?UserInvitation
    {
        try {
            if (User::where('username', $username)->exists()) {
                throw new \Exception('User already exists');
            }
            \DB::beginTransaction();

            $user = User::create([
                'username' => $username,
                'password' => \Hash::make(Str::random(100)),
                'status' => User::STATUS_PENDING
            ]);
            $user->assignRole('user');

            $userInvitation = UserInvitation::create([
                'user_id' => $user->id,
                'invited_by' => auth()->id(),
                'token' => Str::random(16),
                'status' => UserInvitation::STATUS_PENDING
            ]);

            \DB::commit();

            event(new UserInvitationCreatedEvent($userInvitation));
            return $userInvitation;
        } catch (\Exception $e) {
            \DB::rollBack();
            return null;
        }
    }

    public static function revokeInvitation(UserInvitation $invitation): void
    {
        $invitation->update(['status' => UserInvitation::STATUS_REVOKED]);
        event(new UserInvitationRevokedEvent($invitation));
    }
}
