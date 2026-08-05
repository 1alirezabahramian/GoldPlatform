<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Support\CustomerApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CustomerDashboardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return CustomerApiResponse::error(
            $request,
            'Customer dashboard balances are temporarily unavailable until the customer account is resolved against Kimia.',
            'KIMIA_FINANCIAL_BALANCE_SOURCE_REQUIRED',
            503,
        );
    }
}
