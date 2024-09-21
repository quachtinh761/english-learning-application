<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class InternalServerErrorResponse
{
    public static function create(string $message = ''): JsonResponse
    {
        return response()->json([
            'message' => $message ?: 'Internal server error',
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
