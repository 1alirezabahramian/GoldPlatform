<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\KimiaReadException;
use App\Http\Controllers\Controller;
use App\Services\Kimia\AuthenticatedCustomerKimiaBalanceReadService;
use App\Services\Kimia\CustomerKimiaFinancialAssetReadService;
use App\Support\CustomerApiResponse;
use App\Tenancy\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Throwable;

final class CustomerAssetReadController extends Controller
{
    public function index(
        Request $request,
        CustomerKimiaFinancialAssetReadService $assets,
        TenantContext $tenantContext,
    ): JsonResponse {
        return $this->customerAssetResponse($request, $assets, $tenantContext);
    }

    public function money(
        Request $request,
        CustomerKimiaFinancialAssetReadService $assets,
        TenantContext $tenantContext,
    ): JsonResponse {
        return $this->customerAssetResponse($request, $assets, $tenantContext, 'money');
    }

    public function gold(
        Request $request,
        CustomerKimiaFinancialAssetReadService $assets,
        TenantContext $tenantContext,
    ): JsonResponse {
        return $this->customerAssetResponse($request, $assets, $tenantContext, 'gold');
    }

    public function coins(
        Request $request,
        CustomerKimiaFinancialAssetReadService $assets,
        TenantContext $tenantContext,
    ): JsonResponse {
        return $this->customerAssetResponse($request, $assets, $tenantContext, 'coins');
    }

    public function currencies(
        Request $request,
        CustomerKimiaFinancialAssetReadService $assets,
        TenantContext $tenantContext,
    ): JsonResponse {
        return $this->customerAssetResponse($request, $assets, $tenantContext, 'currencies');
    }

    /**
     * V2 verification entrypoint. This remains unregistered in production routes and
     * intentionally exposes raw evidence only to the isolated integration test harness.
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

    private function customerAssetResponse(
        Request $request,
        CustomerKimiaFinancialAssetReadService $assets,
        TenantContext $tenantContext,
        ?string $section = null,
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

        try {
            $result = $assets->read($user, $tenantContext);
        } catch (KimiaReadException|InvalidArgumentException $exception) {
            report($exception);

            return $this->financialBalanceUnavailable($request);
        } catch (Throwable $exception) {
            report($exception);

            return $this->financialBalanceUnavailable($request);
        }

        if (! $result['resolved'] || ! is_array($result['assets'])) {
            return $this->financialBalanceUnavailable($request);
        }

        $projection = $result['assets'];

        if ($section === null) {
            return CustomerApiResponse::success($request, $projection);
        }

        if (! array_key_exists($section, $projection)) {
            return $this->financialBalanceUnavailable($request);
        }

        return CustomerApiResponse::success($request, [
            'source' => 'kimia',
            $section => $projection[$section],
        ]);
    }

    private function financialBalanceUnavailable(Request $request): JsonResponse
    {
        return CustomerApiResponse::error(
            $request,
            'Financial balances are temporarily unavailable.',
            'KIMIA_FINANCIAL_BALANCE_UNAVAILABLE',
            503,
        );
    }
}
