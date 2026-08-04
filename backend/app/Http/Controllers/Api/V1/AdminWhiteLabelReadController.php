<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\ReadModels\AdminWhiteLabelReadModel;
use App\Support\AdminOperatorApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AdminWhiteLabelReadController extends Controller
{
    public function __invoke(Request $request, AdminWhiteLabelReadModel $readModel): JsonResponse
    {
        return AdminOperatorApiResponse::success(
            request: $request,
            data: $readModel->overview(),
        );
    }
}
