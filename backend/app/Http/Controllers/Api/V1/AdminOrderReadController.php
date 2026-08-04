<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\ReadModels\AdminOrderReadModel;
use App\Support\AdminOperatorApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AdminOrderReadController extends Controller
{
    public function index(Request $request, AdminOrderReadModel $readModel): JsonResponse
    {
        return AdminOperatorApiResponse::success($request, $readModel->index($request));
    }

    public function show(Request $request, Order $order, AdminOrderReadModel $readModel): JsonResponse
    {
        return AdminOperatorApiResponse::success($request, $readModel->show($order));
    }
}
