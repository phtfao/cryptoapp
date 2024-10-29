<?php

namespace App\Http\Controllers;

use App\Models\Crypto;
use App\Services\CryptoService;
use App\Http\Requests\CryptoRequest;
use Illuminate\Http\Response;

class CryptoController extends Controller
{
    private $service;

    public function __construct(CryptoService $service)
    {
        $this->service = $service;
    }

    public function index($symbol, CryptoRequest $request)
    {
        $date = $request->validated()['date'];
        $crypto = $this->service->getCrypto($symbol, $date);
        return response()->json($crypto, Response::HTTP_OK);
    }

    public function show($symbol)
    {
        $crypto = $this->service->getCrypto($symbol);
        return response()->json($crypto, Response::HTTP_OK);
    }
}
