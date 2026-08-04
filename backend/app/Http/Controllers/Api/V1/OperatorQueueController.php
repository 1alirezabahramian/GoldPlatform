<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\ReadModels\OperatorQueueReadModel;
use App\Support\BackofficeApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class OperatorQueueController extends Controller
{
    public function orders(Request $request, OperatorQueueReadModel $queues): JsonResponse
    {
        return BackofficeApiResponse::success($request, $queues->orders($request));
    }

    public function deliveries(Request $request, OperatorQueueReadModel $queues): JsonResponse
    {
        return BackofficeApiResponse::success($request, $queues->deliveries($request));
    }
}
