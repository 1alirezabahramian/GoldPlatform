<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\ReadModels\AdminOperatorDashboardReadModel;
use App\Support\AdminOperatorApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AdminDashboardController extends Controller
{
    public function __invoke(Request $request, AdminOperatorDashboardReadModel $readModel): JsonResponse
    {
        return AdminOperatorApiResponse::success($request, $readModel->adminSnapshot());
    }
}
