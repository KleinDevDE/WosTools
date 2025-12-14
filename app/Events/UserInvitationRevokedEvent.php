<?php

namespace App\Events;

use App\Models\UserInvitation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserInvitationRevokedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(private readonly UserInvitation $invitation)
    {
    }
}
