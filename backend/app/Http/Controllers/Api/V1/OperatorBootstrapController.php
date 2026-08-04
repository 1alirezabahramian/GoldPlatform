<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Support\BackofficeApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class OperatorBootstrapController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return BackofficeApiResponse::success($request, [
            'panel' => 'operator',
            'navigation' => [
                ['code' => 'order_queue', 'path' => '/operator/orders/queue'],
                ['code' => 'delivery_queue', 'path' => '/operator/deliveries/queue'],
            ],
            'capabilities' => [
                'orders.queue.read',
                'deliveries.queue.read',
                'deliveries.approve',
                'deliveries.ready',
                'deliveries.deliver',
            ],
        ]);
    }
}
