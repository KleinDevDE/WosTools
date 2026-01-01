<?php

namespace App\Objects;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

readonly class PlayerStats implements Jsonable, Arrayable
{
    public function __construct(
        public int $playerID,
        public string $playerName,
        public int $state,
        public int $furnaceLevel,
        public string $furnaceLevelIcon,
        public string $playerAvatarURL,
        public int $totalRechargeAmount
    )
    {
    }

    public function toJson($options = 0): false|string
    {
        return json_encode($this->toArray(), $options);
    }

    public function toArray(): array
    {
        return [
            'playerID' => $this->playerID,
            'playerName' => $this->playerName,
            'state' => $this->state,
            'furnaceLevel' => $this->furnaceLevel,
            'furnaceLevelIcon' => $this->furnaceLevelIcon,
            'playerAvatarURL' => $this->playerAvatarURL,
            'totalRechargeAmount' => $this->totalRechargeAmount,
        ];
    }
}
