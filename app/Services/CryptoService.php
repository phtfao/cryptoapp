<?php

namespace App\Services;

use App\Clients\CoingeckoClient;
use App\Exceptions\BusinessException;
use App\Exceptions\InvalidCryptoSymbolExceptio;
use App\Models\Crypto;
use Carbon\Carbon;

class CryptoService
{
    private $client;
    private $cryptosAvailable = [
        'BTC' => 'bitcoin',
        'BCH' => 'bitcoin-cash',
        'LTC' => 'litecoin',
        'ETH' => 'ethereum',
        'DACXI' => 'dacxi',
        'LINK' => 'chainlink',
        'USDT' => 'tether',
        'XLM' => 'stellar',
        'DOT' => 'polkadot',
        'ADA' => 'cardano',
        'SOL' => 'solana',
        'AVAX' => 'avalanche-2',
        'LUNC' => 'terra-luna',
        'MATIC' => 'matic-network',
        'USDC' => 'nova-usdc',
        'BNB' => 'binancecoin',
        'XRP' => 'ripple',
        'UNI' => 'uni',
        'MKR' => 'maker',
        'BAT' => 'batic',
        'SAND' => 'the-sandbox',
        'EOS' => 'eos'
    ];

    public function __construct(CoingeckoClient $client)
    {
        $this->client = $client;
    }

    public function isValidCrypto($symbol)
    {
        $symbolUpper = strtoupper($symbol);
        return array_key_exists($symbolUpper, $this->cryptosAvailable);
    }

    public function getCryptoName($symbol)
    {
        if (!$this->isValidCrypto($symbol)) {
            throw new InvalidCryptoSymbolExceptio();
        }

        $symbolUpper = strtoupper($symbol);
        return $this->cryptosAvailable[$symbolUpper];
    }

    public function getMostRecentPrice($symbol)
    {
        $cryptoName = $this->getCryptoName($symbol);
        return $this->client->getCurrentPrice($cryptoName);
    }

    public function getPriceAtDateTime($symbol, $dateTime)
    {
        $dateTime = (new \DateTime($dateTime))->format('d-m-Y h:i:s');

        $cryptoName = $this->getCryptoName($symbol);
        return $this->client->getPriceAtDateTime($cryptoName, $dateTime);
    }

    private function addCrypto($symbol, $price, $date)
    {
        $this->validateDate($date);
        $crypto = new Crypto();
        $crypto->name = $this->getCryptoName($symbol);
        $crypto->symbol = $symbol;
        $crypto->price = $price;
        $crypto->timestamp = $date;
        $crypto->save();
        return $crypto;
    }

    public function getCrypto(string $symbol, $date = null)
    {
        if ($date) {
            $this->validateDate($date);
            $date = Carbon::createFromFormat('Y-m-d h:i:s', $date)->format('d-m-Y h:i:s');
            $price = $this->getPriceAtDateTime($symbol, $date);
        } else {
            $date = Carbon::now()->format('d-m-Y h:i:s');
            $price = $this->getMostRecentPrice($symbol);
        }
        $crypto = $this->addCrypto($symbol, $price, $date);
        unset($crypto->id);
        return $crypto;
    }

    private function validateDate($date)
    {
        try {
            Carbon::parse($date);
        } catch (\Exception $e) {
            throw new BusinessException('Invalid date');
        }
    }
}
