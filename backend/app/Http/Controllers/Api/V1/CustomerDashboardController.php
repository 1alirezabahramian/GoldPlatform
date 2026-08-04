<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\CustomerDashboardReadModel;
use App\Support\CustomerApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CustomerDashboardController extends Controller
{
    public function __invoke(Request $request, CustomerDashboardReadModel $dashboard): JsonResponse
    {
        return CustomerApiResponse::success(
            $request,
            $dashboard->for($request->user()),
        );
    }
}
