<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Support\CustomerApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CustomerOrderStatusController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $items = collect(OrderStatus::cases())
            ->map(fn (OrderStatus $status) => [
                'code' => $status->value,
                'is_terminal' => $status->isTerminal(),
            ])
            ->values()
            ->all();

        return CustomerApiResponse::success($request, [
            'items' => $items,
        ]);
    }
}
