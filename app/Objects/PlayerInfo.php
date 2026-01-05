<?php

namespace App\Objects;

use App\Models\PlayerProfile;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

readonly class PlayerInfo implements Jsonable, Arrayable
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

    public static function fromPlayerProfile(PlayerProfile $playerProfile): self
    {
        return new self(
            $playerProfile->player_id,
            $playerProfile->player_name,
            $playerProfile->state,
            $playerProfile->furnace_level,
            $playerProfile->furnace_level_icon,
            $playerProfile->player_avatar_url,
            $playerProfile->total_recharge_amount
        );
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

    public function isSame(?PlayerProfile $playerProfile): bool
    {
        if (!$playerProfile) {
            return false;
        }

        if ($playerProfile->player_id !== $this->playerID) {
            return false;
        }

        if ($playerProfile->player_name !== $this->playerName) {
            return false;
        }

        if ($playerProfile->state !== $this->state) {
            return false;
        }

        if ($playerProfile->furnace_level !== $this->furnaceLevel) {
            return false;
        }

        if ($playerProfile->total_recharge_amount !== $this->totalRechargeAmount) {
            return false;
        }

        return true;
    }
}
