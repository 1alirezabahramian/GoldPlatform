<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\ReadModels\AdminOperatorOperationalQueueReadModel;
use App\Support\AdminOperatorApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AdminOperatorOperationalQueueController extends Controller
{
    public function orders(Request $request, AdminOperatorOperationalQueueReadModel $readModel): JsonResponse
    {
        return AdminOperatorApiResponse::success($request, $readModel->orders($request));
    }

    public function deliveries(Request $request, AdminOperatorOperationalQueueReadModel $readModel): JsonResponse
    {
        return AdminOperatorApiResponse::success($request, $readModel->deliveries($request));
    }

    public function audit(Request $request, AdminOperatorOperationalQueueReadModel $readModel): JsonResponse
    {
        return AdminOperatorApiResponse::success($request, $readModel->audit($request));
    }

    public function outbox(Request $request, AdminOperatorOperationalQueueReadModel $readModel): JsonResponse
    {
        return AdminOperatorApiResponse::success($request, $readModel->outbox($request));
    }
}
