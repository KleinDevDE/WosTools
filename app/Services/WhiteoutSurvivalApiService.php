<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service for interacting with the Whiteout Survival external API.
 *
 * This service is responsible for building and sending HTTP requests to the
 * official Whiteout Survival gift code API, including generating the required
 * request signatures and headers. It currently provides functionality to
 * retrieve player information by player ID and handles logging of any
 * non-successful responses or exceptions encountered during API calls.
 */
class WhiteoutSurvivalApiService
{
    private const API_BASE_URL = 'https://wos-giftcode-api.centurygame.com';
    private const API_PLAYER_ENDPOINT = '/api/player';
    private const API_SECRET = 'tB87#kPtkxqOS2';
    private const CORS_ORIGIN = 'https://wos-giftcode.centurygame.com';

    /**
     * Fetch player information from Whiteout Survival API
     *
     * @param string $playerId The player ID (fid)
     * @return array|null Player data or null on failure
     */
    public function getPlayerInfo(string $playerId): ?array
    {
        try {
            $timestamp = (int)(microtime(true) * 1000);
            $data = [
                'fid' => $playerId,
                'time' => $timestamp,
            ];

            $sign = $this->generateSignature($data);
            $data['sign'] = $sign;

            $response = Http::asForm()
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Origin' => self::CORS_ORIGIN,
                ])
                ->post(self::API_BASE_URL . self::API_PLAYER_ENDPOINT, $data);

            if (!$response->successful()) {
                Log::error('WoS API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $result = $response->json();

            if (!isset($result['msg']) || $result['msg'] !== 'success') {
                Log::warning('WoS API returned non-success message', [
                    'response' => $result,
                ]);
                return null;
            }

            return $result['data'] ?? null;

        } catch (\Exception $e) {
            Log::error('Exception while fetching WoS player data', [
                'exception' => $e->getMessage(),
                'player_id' => $playerId,
            ]);
            return null;
        }
    }

    /**
     * Generate MD5 signature for API request
     *
     * @param array $data Request data (without sign)
     * @return string MD5 hash signature
     */
    private function generateSignature(array $data): string
    {
        // Sort data by key
        ksort($data);

        // Build URL-encoded query string
        $queryString = http_build_query($data, '', '&', PHP_QUERY_RFC3986);

        // Append secret and generate MD5 hash
        $stringToSign = $queryString . self::API_SECRET;

        return md5($stringToSign);
    }
}
