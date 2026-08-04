<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CustodyAsset;
use App\Models\DeliveryRequest;
use App\Models\Order;
use App\Support\CustomerApiResponse;
use App\Support\CustomerBalancePresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CustomerDashboardController extends Controller
{
    public function __invoke(Request $request, CustomerBalancePresenter $balances): JsonResponse
    {
        $user = $request->user();
        $accounts = $user->wallet?->accounts()
            ->where('is_active', true)
            ->orderBy('id')
            ->get() ?? collect();

        $terminalOrders = ['completed', 'rejected', 'expired', 'cancelled', 'failed'];

        return CustomerApiResponse::success($request, [
            'assets' => $accounts->map(fn ($account) => $balances->present($account))->values()->all(),
            'summary' => [
                'active_orders' => Order::query()
                    ->where('user_id', $user->id)
                    ->whereNotIn('status', $terminalOrders)
                    ->count(),
                'custodies' => CustodyAsset::query()->where('user_id', $user->id)->count(),
                'delivery_requests' => DeliveryRequest::query()->where('user_id', $user->id)->count(),
                'ready_deliveries' => DeliveryRequest::query()
                    ->where('user_id', $user->id)
                    ->where('status', 'ready')
                    ->count(),
            ],
        ]);
    }
}
