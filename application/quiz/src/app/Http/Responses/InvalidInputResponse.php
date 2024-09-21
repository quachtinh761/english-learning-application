<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class InvalidInputResponse
{
    public static function create(array $errors = []): JsonResponse
    {
        return response()->json([
            'errors' => $errors,
            'message' => 'Invalid input',
        ], Response::HTTP_BAD_REQUEST);
    }
}
