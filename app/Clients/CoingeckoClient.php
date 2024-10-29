<?php

namespace App\Clients;

use GuzzleHttp\Client;

class CoingeckoClient
{
    protected $client;
    protected $baseUrl = 'https://api.coingecko.com';

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 30,
            'headers' => [
                'Content-Type' => 'application/json',
                'x-cg-demo-api-key' => getenv('COINGECKO_API_KEY'),
            ],
        ]);
    }

    public function getCurrentPrice($crypto)
    {
        $response = $this->client->get('/api/v3/simple/price', [
            'query' => [
                'ids' => $crypto,
                'vs_currencies' => 'usd',
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        return $data[$crypto]['usd'];
    }

    public function getPriceAtDateTime($crypto, $dateTime)
    {
        $response = $this->client->get('/api/v3/coins/' . $crypto . '/history', [
            'query' => [
                'date' => $dateTime,
                'localization' => 'false',
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        return $data['market_data']['current_price']['usd'];
    }
}
