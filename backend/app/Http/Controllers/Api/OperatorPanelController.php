<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryRequest;
use App\Models\Order;
use App\Services\AuditService;
use App\Services\DeliveryService;
use App\Services\OutboxService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperatorPanelController extends Controller
{
    public function orderQueue(): JsonResponse
    {
        return response()->json(Order::query()->whereIn('status', ['pending','approved','executing','settling'])->oldest('id')->paginate(50));
    }

    public function deliveryQueue(): JsonResponse
    {
        return response()->json(DeliveryRequest::query()->whereIn('status', ['requested','approved','ready'])->oldest('id')->paginate(50));
    }

    public function approveDelivery(Request $request, DeliveryRequest $deliveryRequest, DeliveryService $service, AuditService $audit, OutboxService $outbox): JsonResponse
    {
        $result = DB::transaction(function () use ($request, $deliveryRequest, $service, $audit, $outbox) {
            $before = $deliveryRequest->toArray();
            $delivery = $service->approve($deliveryRequest, $request->user());
            $audit->record('delivery.approved', $delivery, $before, $delivery->toArray(), request: $request);
            $outbox->enqueue('delivery.approved', ['delivery_id' => $delivery->id], $delivery);
            return $delivery;
        });
        return response()->json($result);
    }

    public function markDeliveryReady(Request $request, DeliveryRequest $deliveryRequest, DeliveryService $service, AuditService $audit, OutboxService $outbox): JsonResponse
    {
        $result = DB::transaction(function () use ($request, $deliveryRequest, $service, $audit, $outbox) {
            $before = $deliveryRequest->toArray();
            $delivery = $service->markReady($deliveryRequest);
            $audit->record('delivery.ready', $delivery, $before, $delivery->toArray(), request: $request);
            $outbox->enqueue('delivery.ready', ['delivery_id' => $delivery->id], $delivery);
            return $delivery;
        });
        return response()->json($result);
    }

    public function deliver(Request $request, DeliveryRequest $deliveryRequest, DeliveryService $service, AuditService $audit, OutboxService $outbox): JsonResponse
    {
        $data = $request->validate([
            'receiver_name' => ['required','string','max:160'],
            'receiver_identifier' => ['required','string','max:160'],
        ]);
        $result = DB::transaction(function () use ($request, $deliveryRequest, $service, $audit, $outbox, $data) {
            $before = $deliveryRequest->toArray();
            $delivery = $service->deliver($deliveryRequest, $request->user(), $data['receiver_name'], $data['receiver_identifier']);
            $audit->record('delivery.delivered', $delivery, $before, $delivery->toArray(), request: $request);
            $outbox->enqueue('delivery.delivered', ['delivery_id' => $delivery->id], $delivery);
            return $delivery;
        });
        return response()->json($result);
    }
}
