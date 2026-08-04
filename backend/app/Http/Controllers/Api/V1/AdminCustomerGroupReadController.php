<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\ReadModels\AdminCustomerGroupReadModel;
use App\Support\AdminOperatorApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AdminCustomerGroupReadController extends Controller
{
    public function __invoke(Request $request, AdminCustomerGroupReadModel $readModel): JsonResponse
    {
        return AdminOperatorApiResponse::success($request, $readModel->index($request));
    }
}
