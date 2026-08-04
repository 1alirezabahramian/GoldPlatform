<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\ReadModels\AdminBranchReadModel;
use App\Support\AdminOperatorApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AdminBranchReadController extends Controller
{
    public function __invoke(Request $request, AdminBranchReadModel $readModel): JsonResponse
    {
        return AdminOperatorApiResponse::success(
            request: $request,
            data: $readModel->overview(),
        );
    }
}
