<?php

namespace App\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;

trait WoSAPITrait
{
    private Client $client;

    private const API_BASE_URL = 'https://wos-giftcode-api.centurygame.com';
    private const API_SECRET = 'tB87#kPtkxqOS2';
    private const CORS_ORIGIN = 'https://wos-giftcode.centurygame.com';

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
        $options = [];

        if (!empty($queryParams)) {
            $options[RequestOptions::QUERY] = $queryParams;
        }

        if (!empty($postData)) {
            $options[RequestOptions::JSON] = $postData;
            $options[RequestOptions::JSON]['sign'] = $this->generateSignature($postData);
        }

        try {
            return $this->client->request($method, self::API_BASE_URL . $endpoint, $options);
        } catch (GuzzleException $e) {
            report($e);
            return null;
        }
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
