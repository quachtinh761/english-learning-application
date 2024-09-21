<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

class PaginationResponse
{
    public static function create(
        LengthAwarePaginator $queryResult,
        string | null $transformerClass = null
    ): JsonResponse {
        return response()->json([
            'data' => $transformerClass
            ? $transformerClass::transformItems($queryResult->items())
            : $queryResult->items(),
            'meta' => [
                'total' => $queryResult->total(),
                'page' => $queryResult->currentPage(),
                'per_page' => $queryResult->perPage(),
            ],
        ]);
    }
}
