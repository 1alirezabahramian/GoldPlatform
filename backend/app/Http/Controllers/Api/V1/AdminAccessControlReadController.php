<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\ReadModels\AdminAccessControlReadModel;
use App\Support\AdminOperatorApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AdminAccessControlReadController extends Controller
{
    public function roles(Request $request, AdminAccessControlReadModel $readModel): JsonResponse
    {
        return AdminOperatorApiResponse::success($request, $readModel->roles());
    }

    public function permissions(Request $request, AdminAccessControlReadModel $readModel): JsonResponse
    {
        return AdminOperatorApiResponse::success($request, $readModel->permissions());
    }

    public function matrix(Request $request, AdminAccessControlReadModel $readModel): JsonResponse
    {
        return AdminOperatorApiResponse::success($request, $readModel->matrix());
    }
}
