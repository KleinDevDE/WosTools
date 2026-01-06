<?php

namespace App\Services;

use App\Models\CharacterStats;
use App\Objects\CharacterStatsObject;
use App\Traits\WoSAPITrait;

class WhiteoutSurvivalApiService
{
    use WoSAPITrait;
    private const API_PLAYER_ENDPOINT = '/api/player';

    public function getPlayerStats(int $playerID, bool $fromCache = true, bool $store = true): ?CharacterStatsObject
    {
        if (!\Validator::validate(['playerID' => $playerID], [
            'playerID' => 'required|integer',
        ])) {
            return null;
        }

        if ($fromCache) {
            $characterStats = CharacterStats::query()
                ->where('player_id', $playerID)
                ->whereDate('created_at', '>=', now()->subDay())
                ->orderBy('created_at',    'desc')
                ->first();
            if ($characterStats) {
                return CharacterStatsObject::fromCharacterStats($characterStats);
            }
        }

        $response = $this->request("POST", self::API_PLAYER_ENDPOINT, [], [
            'fid' => $playerID,
            'time' => (int)(microtime(true) * 1000)
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        if (empty($data["data"])) {
            return null;
        }

        $statsObject = new CharacterStatsObject(
            $data["data"]["fid"],
            $data["data"]["nickname"],
            $data["data"]["kid"],
            $data["data"]["stove_lv"],
            $data["data"]["stove_lv_content"],
            $data["data"]["avatar_image"],
            $data["data"]["total_recharge_amount"],
        );

        if ($store) {
            CharacterStats::storeIfChanged($statsObject);
        }

        return $statsObject;
    }
}
