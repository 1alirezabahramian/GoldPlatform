<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AdminOperatorApiResponse
{
    /** @param array<string, mixed> $data */
    public static function success(Request $request, array $data, array $meta = []): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'meta' => array_merge([
                'request_id' => $request->attributes->get('request_id'),
                'generated_at' => now()->toIso8601String(),
                'api_version' => 'v1',
            ], $meta),
            'message' => null,
        ]);
    }
}
