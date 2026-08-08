<?php

namespace App\Services\Kimia;

use App\Models\User;
use App\Repositories\Kimia\Read\ProductReadRepository;
use App\Tenancy\TenantContext;

final class CustomerKimiaFinancialAssetReadService
{
    public function __construct(
        private readonly AuthenticatedCustomerKimiaBalanceReadService $balances,
        private readonly ProductReadRepository $products,
        private readonly RialTomanConverter $money,
    ) {}

    /**
     * Return a customer-safe projection of Kimia-owned financial balances.
     * Internal Kimia identifiers are used only for classification and are never returned.
     *
     * @return array{resolved: bool, reason: string, assets: ?array<string, mixed>}
     */
    public function read(User $user, TenantContext $context): array
    {
        $resolved = $this->balances->read($user, $context);

        if (! $resolved['resolved']) {
            return [
                'resolved' => false,
                'reason' => $resolved['reason'],
                'assets' => null,
            ];
        }

        $coinMap = $this->coinMap($this->products->coins());
        $currencyMap = $this->currencyMap($this->products->currencies());

        $moneyToman = null;
        $goldWeightGram = null;
        $coins = [];
        $currencies = [];
        $rialRowSeen = false;

        foreach ((array) $resolved['balances'] as $row) {
            if (! is_array($row) || ! array_key_exists('CurrencyId', $row)) {
                return $this->unresolved('KIMIA_BALANCE_CLASSIFICATION_UNRESOLVED');
            }

            $currencyId = (int) $row['CurrencyId'];
            $rawAmount = array_key_exists('Money', $row) && $row['Money'] !== null
                ? (string) $row['Money']
                : null;

            if (isset($coinMap[$currencyId])) {
                $coins[] = [
                    'name' => $coinMap[$currencyId],
                    'quantity' => $rawAmount,
                ];

                continue;
            }

            if (isset($currencyMap[$currencyId])) {
                $currencies[] = [
                    'name' => $currencyMap[$currencyId],
                    'symbol' => isset($row['CurrencySymbol']) ? (string) $row['CurrencySymbol'] : null,
                    'amount' => $rawAmount,
                ];

                continue;
            }

            if (($row['CurrencySymbol'] ?? null) === 'ریال') {
                if ($rialRowSeen) {
                    return $this->unresolved('KIMIA_BALANCE_CLASSIFICATION_UNRESOLVED');
                }

                $rialRowSeen = true;
                $moneyToman = $rawAmount === null ? null : $this->money->toToman($rawAmount);
                $goldWeightGram = array_key_exists('Weight', $row) && $row['Weight'] !== null
                    ? (string) $row['Weight']
                    : null;

                continue;
            }

            return $this->unresolved('KIMIA_BALANCE_CLASSIFICATION_UNRESOLVED');
        }

        if (! $rialRowSeen) {
            return $this->unresolved('KIMIA_RIAL_BALANCE_REQUIRED');
        }

        return [
            'resolved' => true,
            'reason' => 'RESOLVED',
            'assets' => [
                'source' => 'kimia',
                'money' => [
                    'amount_toman' => $moneyToman,
                    'unit' => 'toman',
                ],
                'gold' => [
                    'weight_gram' => $goldWeightGram,
                    'unit' => 'gram',
                ],
                'coins' => $coins,
                'currencies' => $currencies,
            ],
        ];
    }

    /** @param array<int, mixed> $rows @return array<int, string> */
    private function coinMap(array $rows): array
    {
        $map = [];

        foreach ($rows as $row) {
            if (is_array($row) && isset($row['CoinId'], $row['Name'])) {
                $map[(int) $row['CoinId']] = (string) $row['Name'];
            }
        }

        return $map;
    }

    /** @param array<int, mixed> $rows @return array<int, string> */
    private function currencyMap(array $rows): array
    {
        $map = [];

        foreach ($rows as $row) {
            if (is_array($row) && isset($row['CurrencyId'], $row['Name'])) {
                $map[(int) $row['CurrencyId']] = (string) $row['Name'];
            }
        }

        return $map;
    }

    /** @return array{resolved: false, reason: string, assets: null} */
    private function unresolved(string $reason): array
    {
        return [
            'resolved' => false,
            'reason' => $reason,
            'assets' => null,
        ];
    }
}
