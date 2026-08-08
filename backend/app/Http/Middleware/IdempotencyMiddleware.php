<?php

namespace App\Http\Middleware;

use App\Models\IdempotencyRecord;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class IdempotencyMiddleware
{
    private const NON_REPLAYABLE_RESPONSE_SCOPES = [
        'staff.create',
    ];

    public function handle(Request $request, Closure $next, string $scope = 'api'): Response
    {
        $key = trim((string) $request->header('Idempotency-Key'));
        if ($key === '') {
            return response()->json(['message' => 'Idempotency-Key header is required.'], 422);
        }

        $keyHash = hash('sha256', $key);
        $requestHash = hash('sha256', $request->method().'|'.$request->path().'|'.json_encode($request->all(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $userId = $request->user()?->getKey();
        $nonReplayableResponse = in_array($scope, self::NON_REPLAYABLE_RESPONSE_SCOPES, true);

        return DB::transaction(function () use ($request, $next, $scope, $keyHash, $requestHash, $userId, $nonReplayableResponse): Response {
            $record = IdempotencyRecord::query()
                ->where('user_id', $userId)
                ->where('scope', $scope)
                ->where('key_hash', $keyHash)
                ->lockForUpdate()
                ->first();

            if ($record?->completed_at) {
                if (! hash_equals($record->request_hash, $requestHash)) {
                    return response()->json(['message' => 'Idempotency key was used with a different request.'], 409);
                }

                if ($nonReplayableResponse) {
                    return response()->json([
                        'message' => 'The operation already completed; sensitive credentials are not replayed.',
                        'code' => 'IDEMPOTENT_SECRET_RESPONSE_NOT_REPLAYABLE',
                    ], 409);
                }

                return response()->json($record->response_body ?? [], $record->response_status ?? 200);
            }

            if ($record && ! hash_equals($record->request_hash, $requestHash)) {
                return response()->json(['message' => 'Idempotency key conflict.'], 409);
            }

            $record ??= IdempotencyRecord::query()->create([
                'user_id' => $userId,
                'scope' => $scope,
                'key_hash' => $keyHash,
                'request_hash' => $requestHash,
                'locked_at' => now(),
            ]);

            $response = $next($request);
            $body = $response instanceof JsonResponse ? $response->getData(true) : ['content' => $response->getContent()];
            $record->forceFill([
                'response_status' => $response->getStatusCode(),
                'response_body' => $nonReplayableResponse ? null : $body,
                'completed_at' => now(),
            ])->save();

            return $response;
        });
    }
}
