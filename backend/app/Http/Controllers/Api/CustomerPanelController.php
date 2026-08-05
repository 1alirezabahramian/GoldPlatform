<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustodyAsset;
use App\Models\DeliveryRequest;
use App\Models\Order;
use App\Services\DeliveryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerPanelController extends Controller
{
    public function overview(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'balances' => [],
            'balance_source' => 'kimia',
            'balance_status' => 'unavailable',
            'balance_error_code' => 'KIMIA_FINANCIAL_BALANCE_SOURCE_REQUIRED',
            'open_orders' => Order::query()->where('user_id', $user->id)->whereNotIn('status', ['completed','rejected','expired','cancelled','failed'])->count(),
            'custody_count' => CustodyAsset::query()->where('user_id', $user->id)->count(),
            'delivery_count' => DeliveryRequest::query()->where('user_id', $user->id)->count(),
        ]);
    }

    public function orders(Request $request): JsonResponse
    {
        return response()->json(Order::query()->where('user_id', $request->user()->id)->latest('id')->paginate(25));
    }

    public function custody(Request $request): JsonResponse
    {
        return response()->json(CustodyAsset::query()->where('user_id', $request->user()->id)->latest('id')->paginate(25));
    }

    public function deliveries(Request $request): JsonResponse
    {
        return response()->json(DeliveryRequest::query()->where('user_id', $request->user()->id)->latest('id')->paginate(25));
    }

    public function requestDelivery(Request $request, CustodyAsset $custodyAsset, DeliveryService $deliveries): JsonResponse
    {
        $data = $request->validate([
            'branch_code' => ['nullable','string','max:80'],
            'requested_for' => ['nullable','date'],
        ]);
        $delivery = $deliveries->request($custodyAsset, $request->user(), $data);
        return response()->json($delivery, 201);
    }
}
