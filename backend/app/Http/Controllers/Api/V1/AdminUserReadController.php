<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\ReadModels\AdminUserReadModel;
use App\Support\AdminOperatorApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AdminUserReadController extends Controller
{
    public function __invoke(Request $request, AdminUserReadModel $readModel): JsonResponse
    {
        return AdminOperatorApiResponse::success($request, $readModel->index($request));
    }
}
