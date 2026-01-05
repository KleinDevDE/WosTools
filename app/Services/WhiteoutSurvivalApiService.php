<?php

namespace App\Services;

use App\Models\PlayerProfile;
use App\Objects\PlayerInfo;
use App\Traits\WoSAPITrait;

class WhiteoutSurvivalApiService
{
    use WoSAPITrait;
    private const API_PLAYER_ENDPOINT = '/api/player';

    public function getPlayerStats(int $playerID, bool $fromCache = true, bool $store = true): ?PlayerInfo
    {
        if (!\Validator::validate(['playerID' => $playerID], [
            'playerID' => 'required|integer',
        ])) {
            return null;
        }

        if ($fromCache) {
            $playerProfile = PlayerProfile::query()
                ->where('player_id', $playerID)
                ->whereDate('created_at', '>=', now()->subDay())
                ->orderBy('created_at',    'desc')
                ->first();
            if ($playerProfile) {
                return PlayerInfo::fromPlayerProfile($playerProfile);
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

        $playerInfo = new PlayerInfo(
            $data["data"]["fid"],
            $data["data"]["nickname"],
            $data["data"]["kid"],
            $data["data"]["stove_lv"],
            $data["data"]["stove_lv_content"],
            $data["data"]["avatar_image"],
            $data["data"]["total_recharge_amount"],
        );

        if ($store) {
            PlayerProfile::storeIfChanged($playerInfo);
        }

        return $playerInfo;
    }
}
