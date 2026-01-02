<?php

namespace App\Services;

use App\Objects\PlayerInfo;
use App\Traits\WoSAPITrait;

class WhiteoutSurvivalApiService
{
    use WoSAPITrait;
    private const API_PLAYER_ENDPOINT = '/api/player';

    public function getPlayerStats(int $playerID): ?PlayerInfo
    {
        if (!\Validator::validate(['playerID' => $playerID], [
            'playerID' => 'required|integer',
        ])) {
            return null;
        }

        $response = $this->request("POST", self::API_PLAYER_ENDPOINT, [], [
            'fid' => $playerID,
            'time' => (int)(microtime(true) * 1000)
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        if (empty($data["data"])) {
            return null;
        }

        return new PlayerInfo(
            $data["data"]["fid"],
            $data["data"]["nickname"],
            $data["data"]["kid"],
            $data["data"]["stove_lv"],
            $data["data"]["stove_lv_content"],
            $data["data"]["avatar_image"],
            $data["data"]["total_recharge_amount"],
        );
    }
}
