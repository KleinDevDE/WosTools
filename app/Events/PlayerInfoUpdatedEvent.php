<?php

namespace App\Events;

use App\Objects\PlayerInfo;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerInfoUpdatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly PlayerInfo $before,
        public readonly PlayerInfo $after
    )
    {}
}
