<?php

namespace App\Services;

use App\Events\UserInvitationCreatedEvent;
use App\Events\UserInvitationRevokedEvent;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Support\Str;

class UserInvitationService
{
    public static function inviteUser(int $playerId, string $playerName): ?UserInvitation
    {
        try {
            if (User::where('player_id', $playerId)->exists()) {
                throw new \Exception('Player already exists');
            }
            \DB::beginTransaction();

            $user = User::create([
                'player_id' => $playerId,
                'player_name' => $playerName,
                'password' => \Hash::make(Str::random(100)),
                'status' => User::STATUS_INVITED
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
