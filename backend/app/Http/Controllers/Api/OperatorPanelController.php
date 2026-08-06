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
        $page = Order::query()
            ->whereIn('status', ['pending', 'approved', 'executing', 'settling'])
            ->oldest('id')
            ->paginate(50);

        $page->through(fn (Order $order): array => [
            'id' => $order->id,
            'user_id' => $order->user_id,
            'type' => $order->type,
            'asset_type' => $order->asset_type?->value,
            'asset_quantity' => $order->asset_quantity,
            'asset_unit' => $order->asset_unit,
            'status' => $order->status->value,
            'gold_weight' => $order->gold_weight,
            'gold_price' => $order->gold_price,
            'commission' => $order->commission,
            'total_price' => $order->total_price,
            'expires_at' => $order->expires_at?->toISOString(),
            'created_at' => $order->created_at?->toISOString(),
        ]);

        return response()->json($page);
    }

    public function deliveryQueue(): JsonResponse
    {
        $page = DeliveryRequest::query()
            ->whereIn('status', ['requested', 'approved', 'ready'])
            ->oldest('id')
            ->paginate(50);

        $page->through(fn (DeliveryRequest $delivery): array => [
            'id' => $delivery->id,
            'uuid' => $delivery->uuid,
            'custody_asset_id' => $delivery->custody_asset_id,
            'user_id' => $delivery->user_id,
            'branch_code' => $delivery->branch_code,
            'requested_for' => $delivery->requested_for?->toISOString(),
            'status' => $delivery->status->value,
            'approved_at' => $delivery->approved_at?->toISOString(),
            'ready_at' => $delivery->ready_at?->toISOString(),
            'created_at' => $delivery->created_at?->toISOString(),
        ]);

        return response()->json($page);
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

        return response()->json($this->presentDeliveryAction($result));
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

        return response()->json($this->presentDeliveryAction($result));
    }

    public function deliver(Request $request, DeliveryRequest $deliveryRequest, DeliveryService $service, AuditService $audit, OutboxService $outbox): JsonResponse
    {
        $data = $request->validate([
            'receiver_name' => ['required', 'string', 'max:160'],
            'receiver_identifier' => ['required', 'string', 'max:160'],
        ]);
        $result = DB::transaction(function () use ($request, $deliveryRequest, $service, $audit, $outbox, $data) {
            $before = $deliveryRequest->toArray();
            $delivery = $service->deliver($deliveryRequest, $request->user(), $data['receiver_name'], $data['receiver_identifier']);
            $audit->record('delivery.delivered', $delivery, $before, $delivery->toArray(), request: $request);
            $outbox->enqueue('delivery.delivered', ['delivery_id' => $delivery->id], $delivery);
            return $delivery;
        });

        return response()->json($this->presentDeliveryAction($result));
    }

    private function presentDeliveryAction(DeliveryRequest $delivery): array
    {
        return [
            'id' => $delivery->id,
            'uuid' => $delivery->uuid,
            'custody_asset_id' => $delivery->custody_asset_id,
            'user_id' => $delivery->user_id,
            'branch_code' => $delivery->branch_code,
            'status' => $delivery->status->value,
            'approved_at' => $delivery->approved_at?->toISOString(),
            'ready_at' => $delivery->ready_at?->toISOString(),
            'delivered_at' => $delivery->delivered_at?->toISOString(),
            'updated_at' => $delivery->updated_at?->toISOString(),
        ];
    }
}
