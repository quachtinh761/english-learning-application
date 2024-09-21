<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class NotFoundErrorResponse
{
    public static function create(string $message = ''): JsonResponse
    {
        return response()->json([
            'message' => $message ?: 'Internal server error',
        ], Response::HTTP_NOT_FOUND);
    }
}
