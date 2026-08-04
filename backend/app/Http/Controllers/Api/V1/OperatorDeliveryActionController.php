<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DeliveryRequest;
use App\Services\AuditService;
use App\Services\DeliveryService;
use App\Services\OutboxService;
use App\Support\AdminOperatorApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use LogicException;

final class OperatorDeliveryActionController extends Controller
{
    public function approve(Request $request, DeliveryRequest $deliveryRequest, DeliveryService $service, AuditService $audit, OutboxService $outbox): JsonResponse
    {
        return $this->execute($request, $deliveryRequest, 'delivery.approved', fn () => $service->approve($deliveryRequest, $request->user()), $audit, $outbox);
    }

    public function ready(Request $request, DeliveryRequest $deliveryRequest, DeliveryService $service, AuditService $audit, OutboxService $outbox): JsonResponse
    {
        return $this->execute($request, $deliveryRequest, 'delivery.ready', fn () => $service->markReady($deliveryRequest), $audit, $outbox);
    }

    public function complete(Request $request, DeliveryRequest $deliveryRequest, DeliveryService $service, AuditService $audit, OutboxService $outbox): JsonResponse
    {
        $data = $request->validate([
            'receiver_name' => ['required', 'string', 'max:160'],
            'receiver_identifier' => ['required', 'string', 'max:160'],
        ]);

        return $this->execute(
            $request,
            $deliveryRequest,
            'delivery.delivered',
            fn () => $service->deliver($deliveryRequest, $request->user(), $data['receiver_name'], $data['receiver_identifier']),
            $audit,
            $outbox,
        );
    }

    private function execute(Request $request, DeliveryRequest $deliveryRequest, string $event, callable $transition, AuditService $audit, OutboxService $outbox): JsonResponse
    {
        try {
            $delivery = DB::transaction(function () use ($request, $deliveryRequest, $event, $transition, $audit, $outbox): DeliveryRequest {
                $before = $deliveryRequest->fresh()->toArray();
                /** @var DeliveryRequest $result */
                $result = $transition();
                $audit->record($event, $result, $before, $result->toArray(), request: $request);
                $outbox->enqueue($event, ['delivery_id' => $result->id], $result);
                return $result;
            });
        } catch (LogicException $exception) {
            return response()->json([
                'data' => null,
                'meta' => [
                    'request_id' => $request->attributes->get('request_id'),
                    'api_version' => 'v1',
                ],
                'message' => 'Delivery transition is not allowed.',
                'error' => ['code' => 'invalid_delivery_transition'],
            ], 409);
        }

        return AdminOperatorApiResponse::success(
            request: $request,
            data: [
                'delivery' => [
                    'uuid' => $delivery->uuid,
                    'status' => $delivery->status->value,
                    'approved_at' => $delivery->approved_at?->toISOString(),
                    'ready_at' => $delivery->ready_at?->toISOString(),
                    'delivered_at' => $delivery->delivered_at?->toISOString(),
                    'updated_at' => $delivery->updated_at?->toISOString(),
                ],
            ],
        );
    }
}
