<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BusinessException extends Exception
{
    protected $messageDefault = 'Precondition Failed';

    public function __construct(string $mensagem = null)
    {
        if (!$mensagem) {
            $mensagem = $this->messageDefault;
        }
        parent::__construct($mensagem);
    }

    /**
     * Report the exception.
     */
    public function report(): void
    {
        // ...
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json(["message" => $this->getMessage()], Response::HTTP_PRECONDITION_FAILED);
    }
}
