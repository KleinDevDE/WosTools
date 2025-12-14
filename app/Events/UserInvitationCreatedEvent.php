<?php

namespace App\Events;

use App\Models\UserInvitation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserInvitationCreatedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(private readonly UserInvitation $userInvitation)
    {
    }
}
