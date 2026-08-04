<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Support\CustomerApiResponse;
use App\Support\CustomerBalancePresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

final class CustomerAssetReadController extends Controller
{
    public function __construct(private readonly CustomerBalancePresenter $presenter)
    {
    }

    public function index(Request $request): JsonResponse
    {
        return $this->respond($request, null);
    }

    public function money(Request $request): JsonResponse
    {
        return $this->respond($request, 'money');
    }

    public function gold(Request $request): JsonResponse
    {
        return $this->respond($request, 'gold');
    }

    public function coins(Request $request): JsonResponse
    {
        return $this->respond($request, 'coin');
    }

    public function currencies(Request $request): JsonResponse
    {
        return $this->respond($request, 'currency');
    }

    private function respond(Request $request, ?string $assetType): JsonResponse
    {
        $accounts = $request->user()->wallet?->accounts()
            ->where('is_active', true)
            ->when($assetType !== null, fn ($query) => $query->where('asset_type', $assetType))
            ->with([
                'ledgerEntries:id,wallet_account_id,entry_type,amount',
                'balanceReservations' => fn ($query) => $query
                    ->where('status', 'active')
                    ->select(['id', 'wallet_account_id', 'amount', 'status']),
            ])
            ->orderBy('asset_type')
            ->orderBy('id')
            ->get() ?? collect();

        return CustomerApiResponse::success($request, [
            'items' => $this->present($accounts),
        ], [
            'asset_type' => $assetType,
            'count' => $accounts->count(),
        ]);
    }

    /** @param Collection<int, \App\Models\WalletAccount> $accounts */
    private function present(Collection $accounts): array
    {
        return $accounts
            ->map(fn ($account) => $this->presenter->present($account))
            ->values()
            ->all();
    }
}
