<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CustomerApiResponse
{
    /** @param array<string, mixed> $data */
    public static function success(Request $request, array $data, array $meta = []): JsonResponse
    {
        $requestId = (string) $request->attributes->get('request_id');

        return response()->json([
            'data' => $data,
            'meta' => array_merge([
                'request_id' => $requestId,
                'generated_at' => now()->toIso8601String(),
                'api_version' => 'v1',
            ], $meta),
            'message' => null,
        ])->header('X-Request-ID', $requestId);
    }

    /** @param array<string, list<string>> $errors */
    public static function error(
        Request $request,
        string $message,
        string $code,
        int $status,
        array $errors = []
    ): JsonResponse {
        $requestId = (string) $request->attributes->get('request_id');

        return response()->json([
            'message' => $message,
            'code' => $code,
            'errors' => (object) $errors,
            'request_id' => $requestId,
        ], $status)->header('X-Request-ID', $requestId);
    }
}
