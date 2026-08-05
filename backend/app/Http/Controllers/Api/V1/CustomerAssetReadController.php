<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Support\CustomerApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CustomerAssetReadController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return $this->kimiaBalanceSourceRequired($request);
    }

    public function money(Request $request): JsonResponse
    {
        return $this->kimiaBalanceSourceRequired($request);
    }

    public function gold(Request $request): JsonResponse
    {
        return $this->kimiaBalanceSourceRequired($request);
    }

    public function coins(Request $request): JsonResponse
    {
        return $this->kimiaBalanceSourceRequired($request);
    }

    public function currencies(Request $request): JsonResponse
    {
        return $this->kimiaBalanceSourceRequired($request);
    }

    private function kimiaBalanceSourceRequired(Request $request): JsonResponse
    {
        return CustomerApiResponse::error(
            $request,
            'Financial balances are temporarily unavailable until the customer account is resolved against Kimia.',
            'KIMIA_FINANCIAL_BALANCE_SOURCE_REQUIRED',
            503,
        );
    }
}
