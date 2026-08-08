<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Kimia\AuthenticatedCustomerKimiaBalanceReadService;
use App\Support\CustomerApiResponse;
use App\Tenancy\TenantContext;
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

    /**
     * Inactive V2 integration entrypoint. Not wired to production routes yet.
     * It exists only to validate the authenticated Tenant -> User -> Account -> Kimia read chain.
     */
    public function resolvedKimiaBalances(
        Request $request,
        AuthenticatedCustomerKimiaBalanceReadService $balances,
        TenantContext $tenantContext,
    ): JsonResponse {
        $user = $request->user();

        if ($user === null) {
            return CustomerApiResponse::error(
                $request,
                'Authenticated customer is required.',
                'AUTHENTICATED_CUSTOMER_REQUIRED',
                401,
            );
        }

        $result = $balances->read($user, $tenantContext);

        if (! $result['resolved']) {
            return CustomerApiResponse::error(
                $request,
                'Financial balances are unavailable until the customer account is resolved against Kimia.',
                $result['reason'],
                503,
            );
        }

        return CustomerApiResponse::success($request, [
            'source' => 'kimia',
            'kimia_account_id' => $result['kimia_account_id'],
            'balances' => $result['balances'],
        ]);
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
