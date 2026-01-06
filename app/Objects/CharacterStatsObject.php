<?php

namespace App\Objects;

use App\Models\CharacterStats;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

readonly class CharacterStatsObject implements Jsonable, Arrayable
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

    public static function fromCharacterStats(CharacterStats $characterStats): self
    {
        return new self(
            $characterStats->player_id,
            $characterStats->player_name,
            $characterStats->state,
            $characterStats->furnace_level,
            $characterStats->furnace_level_icon,
            $characterStats->player_avatar_url,
            $characterStats->total_recharge_amount
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

    public function isSame(?CharacterStats $characterStats): bool
    {
        if (!$characterStats) {
            return false;
        }

        if ($characterStats->player_id !== $this->playerID) {
            return false;
        }

        if ($characterStats->player_name !== $this->playerName) {
            return false;
        }

        if ($characterStats->state !== $this->state) {
            return false;
        }

        if ($characterStats->furnace_level !== $this->furnaceLevel) {
            return false;
        }

        if ($characterStats->total_recharge_amount !== $this->totalRechargeAmount) {
            return false;
        }

        return true;
    }

    public function getFurnaceLevelReadable(): string
    {
        return config('wos.furnace_level_mappings')[$this->furnaceLevel] ?? $this->furnaceLevel;
    }
}
