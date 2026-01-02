<?php

namespace App\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Cache;

trait WoSAPITrait
{
    private Client $client;

    private const API_BASE_URL = 'https://wos-giftcode-api.centurygame.com';
    private const API_SECRET = 'tB87#kPtkxqOS2';
    private const CORS_ORIGIN = 'https://wos-giftcode.centurygame.com';
    private const RATE_LIMIT_KEY = 'wos_api_rate_limit';
    private const RATE_LIMIT_MAX = 29;
    private const RATE_LIMIT_WINDOW = 60;

    public function __construct()
    {
        $this->client = new Client([
            'headers' => [
                'Accept' => 'application/json',
                'Origin' => self::CORS_ORIGIN,
            ],
            'timeout' => 10.0,
        ]);
    }

    protected function request(string $method, string $endpoint, array $queryParams = [], array $postData = []): ?Response {
        $this->waitForRateLimit();

        $options = [];

        if (!empty($queryParams)) {
            $options[RequestOptions::QUERY] = $queryParams;
        }

        if (!empty($postData)) {
            $options[RequestOptions::JSON] = $postData;
            $options[RequestOptions::JSON]['sign'] = $this->generateSignature($postData);
        }

        try {
            $response = $this->client->request($method, self::API_BASE_URL . $endpoint, $options);
            $this->incrementRateLimit();
            return $response;
        } catch (GuzzleException $e) {
            report($e);
            return null;
        }
    }

    private function waitForRateLimit(): void
    {
        $attempts = Cache::get(self::RATE_LIMIT_KEY, 0);

        if ($attempts >= self::RATE_LIMIT_MAX) {
            $ttl = Cache::get(self::RATE_LIMIT_KEY . '_ttl');
            if ($ttl) {
                $waitTime = max(0, $ttl - time());
                if ($waitTime > 0) {
                    sleep($waitTime);
                    Cache::forget(self::RATE_LIMIT_KEY);
                    Cache::forget(self::RATE_LIMIT_KEY . '_ttl');
                }
            }
        }
    }

    private function incrementRateLimit(): void
    {
        $attempts = Cache::get(self::RATE_LIMIT_KEY, 0);

        if ($attempts === 0) {
            Cache::put(self::RATE_LIMIT_KEY . '_ttl', time() + self::RATE_LIMIT_WINDOW, self::RATE_LIMIT_WINDOW);
        }

        Cache::put(self::RATE_LIMIT_KEY, $attempts + 1, self::RATE_LIMIT_WINDOW);
    }

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
