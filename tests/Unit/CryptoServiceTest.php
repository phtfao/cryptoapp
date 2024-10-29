<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\CryptoService;
use App\Clients\CoingeckoClient;
use App\Exceptions\InvalidCryptoSymbolExceptio;
use App\Exceptions\BusinessException;
use App\Models\Crypto;
use Carbon\Carbon;
use Mockery;


class CryptoServiceTest extends TestCase
{
    private $client;
    private $cryptoService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = Mockery::mock(CoingeckoClient::class);
        $this->cryptoService = new CryptoService($this->client);
    }

    public function testIsValidCrypto()
    {
        $this->assertTrue($this->cryptoService->isValidCrypto('BTC'));
        $this->assertFalse($this->cryptoService->isValidCrypto('INVALID'));
    }

    public function testGetCryptoName()
    {
        $this->assertEquals('bitcoin', $this->cryptoService->getCryptoName('BTC'));
    }

    public function testGetCryptoNameThrowsException()
    {
        $this->expectException(InvalidCryptoSymbolExceptio::class);
        $this->cryptoService->getCryptoName('INVALID');
    }

    public function testGetMostRecentPrice()
    {
        $this->client->shouldReceive('getCurrentPrice')->with('bitcoin')->andReturn(50000);
        $price = $this->cryptoService->getMostRecentPrice('BTC');
        $this->assertEquals(50000, $price);
    }

    public function testGetPriceAtDateTime()
    {
        $date = '2023-01-01 01:23:45';
        $this->client->shouldReceive('getPriceAtDateTime')->with('bitcoin', '01-01-2023 01:23:45')->andReturn(40000);
        $price = $this->cryptoService->getPriceAtDateTime('BTC', $date);
        $this->assertEquals(40000, $price);
    }

    public function testGetCryptoWithDate()
    {
        $date = '2023-01-01 01:23:45';
        $this->client->shouldReceive('getPriceAtDateTime')->with('bitcoin', '01-01-2023 01:23:45')->andReturn(40000);
        $crypto = $this->cryptoService->getCrypto('BTC', $date);
        $this->assertInstanceOf(Crypto::class, $crypto);
        $this->assertEquals('BTC', $crypto->symbol);
        $this->assertEquals('bitcoin', $crypto->name);
        $this->assertEquals(40000, $crypto->price);
    }

    public function testGetCryptoWithoutDate()
    {
        $this->client->shouldReceive('getCurrentPrice')->with('bitcoin')->andReturn(40000);
        $crypto = $this->cryptoService->getCrypto('BTC');
        $this->assertInstanceOf(Crypto::class, $crypto);
        $this->assertEquals('BTC', $crypto->symbol);
        $this->assertEquals('bitcoin', $crypto->name);
        $this->assertEquals(40000, $crypto->price);
    }

    public function testValidateDateThrowsException()
    {
        $this->expectException(BusinessException::class);
        $this->cryptoService->getCrypto('BTC', 'invalid-date');
    }
}
