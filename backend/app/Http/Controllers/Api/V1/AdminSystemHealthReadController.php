<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\ReadModels\AdminSystemHealthReadModel;
use App\Support\AdminOperatorApiResponse;
use Illuminate\Http\JsonResponse;

final class AdminSystemHealthReadController extends Controller
{
    public function __invoke(AdminSystemHealthReadModel $readModel): JsonResponse
    {
        return AdminOperatorApiResponse::success($readModel->read());
    }
}
