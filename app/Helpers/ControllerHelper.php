<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ControllerHelper
{
    public function successJsonResponse(string $message = 'Operação realizada com sucesso', $item = null, int $responseCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'item' => $item,
        ], $responseCode);
    }

    public function errorJsonResponse(string $error, ?string $message = null, int $responseCode = Response::HTTP_BAD_REQUEST, $item = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => $error,
            'message' => $message ?? $error,
            'item' => $item,
        ], $responseCode);
    }
}
