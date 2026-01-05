<?php

namespace App\Models;

use App\Objects\PlayerInfo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerProfile extends Model
{
    protected $fillable = [
        'player_id',
        'player_name',
        'state',
        'furnace_level',
        'furnace_level_icon',
        'player_avatar_url',
        'total_recharge_amount',
    ];

    /**
     * Create or update player profile from PlayerInfo object
     */
    public static function createIfChanged(PlayerInfo $playerInfo): self
    {
        $playerProfile = self::where('player_id', $playerInfo->playerID)->orderBy('created_at', 'desc')->first();

        if ($playerProfile && $playerInfo->isSame($playerProfile)) {
            return $playerProfile;
        }

        return self::create([
            'player_id' => $playerInfo->playerID,
            'player_name' => $playerInfo->playerName,
            'state' => $playerInfo->state,
            'furnace_level' => $playerInfo->furnaceLevel,
            'furnace_level_icon' => $playerInfo->furnaceLevelIcon,
            'player_avatar_url' => $playerInfo->playerAvatarURL,
            'total_recharge_amount' => $playerInfo->totalRechargeAmount,
        ]);
    }
}
