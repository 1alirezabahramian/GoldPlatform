<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\ReadModels\BackofficeOperationalDashboard;
use App\Support\BackofficeApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AdminOperationalDashboardController extends Controller
{
    public function __invoke(Request $request, BackofficeOperationalDashboard $dashboard): JsonResponse
    {
        return BackofficeApiResponse::success($request, $dashboard->admin());
    }
}
