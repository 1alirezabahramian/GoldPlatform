<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\ReadModels\AdminNotificationReadModel;
use App\Support\AdminOperatorApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AdminNotificationReadController extends Controller
{
    public function __invoke(Request $request, AdminNotificationReadModel $readModel): JsonResponse
    {
        return AdminOperatorApiResponse::success(
            request: $request,
            data: $readModel->overview(),
        );
    }
}
