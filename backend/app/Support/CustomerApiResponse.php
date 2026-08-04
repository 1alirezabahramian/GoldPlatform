<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CustomerApiResponse
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

    /** @param array<string, list<string>> $errors */
    public static function error(
        Request $request,
        string $message,
        string $code,
        int $status,
        array $errors = []
    ): JsonResponse {
        return response()->json([
            'message' => $message,
            'code' => $code,
            'errors' => (object) $errors,
            'request_id' => $request->attributes->get('request_id'),
        ], $status);
    }
}
