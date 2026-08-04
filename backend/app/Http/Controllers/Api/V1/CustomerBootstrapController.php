<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\CustodyStatus;
use App\Enums\DeliveryStatus;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Services\CustomerActivityReadModel;
use App\Support\CustomerApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CustomerBootstrapController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return CustomerApiResponse::success($request, [
            'api' => [
                'version' => 'v1',
                'timezone' => 'UTC',
            ],
            'contracts' => [
                'activity_event_types' => CustomerActivityReadModel::eventTypes(),
                'order_statuses' => $this->statuses(OrderStatus::cases()),
                'custody_statuses' => $this->statuses(CustodyStatus::cases()),
                'delivery_statuses' => $this->statuses(DeliveryStatus::cases()),
            ],
        ]);
    }

    /** @param array<int, OrderStatus|CustodyStatus|DeliveryStatus> $statuses */
    private function statuses(array $statuses): array
    {
        return array_map(
            static fn (OrderStatus|CustodyStatus|DeliveryStatus $status): array => [
                'code' => $status->value,
                'is_terminal' => $status->isTerminal(),
            ],
            $statuses,
        );
    }
}
