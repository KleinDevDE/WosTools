<?php

namespace App\Models;

use App\Objects\CharacterStatsObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterStats extends Model
{
    protected $table = 'character_stats';

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
     * Create or update character stats from CharacterStatsObject
     */
    public static function storeIfChanged(CharacterStatsObject $statsObject): self
    {
        $existingStats = self::where('player_id', $statsObject->playerID)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($existingStats && $statsObject->isSame($existingStats)) {
            return $existingStats;
        }

        return self::create([
            'player_id' => $statsObject->playerID,
            'player_name' => $statsObject->playerName,
            'state' => $statsObject->state,
            'furnace_level' => $statsObject->furnaceLevel,
            'furnace_level_icon' => $statsObject->furnaceLevelIcon,
            'player_avatar_url' => $statsObject->playerAvatarURL,
            'total_recharge_amount' => $statsObject->totalRechargeAmount,
        ]);
    }

    public function getFurnaceLevelReadable(): string
    {
        return config('wos.furnace_level_mappings')[$this->furnace_level] ?? $this->furnace_level;
    }
}
