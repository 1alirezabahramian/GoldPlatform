<?php

namespace App\Services\Kimia;

use Illuminate\Support\Facades\DB;

final class CustomerAccountReconciliationService
{
    /**
     * Read-only reconciliation between the legacy local accounts binding table and the
     * current Kimia external account projection. This service never links, creates,
     * updates, deletes, or backfills records.
     *
     * @return array{summary: array<string, int>, rows: list<array<string, int|string|null>>}
     */
    public function inspect(): array
    {
        $accounts = DB::table('accounts')
            ->select(['id', 'kimia_id'])
            ->orderBy('id')
            ->get();

        $kimiaExternalAccounts = DB::table('external_accounts')
            ->where('provider', 'kimia')
            ->select(['id', 'external_id'])
            ->orderBy('id')
            ->get();

        $userBindingCounts = DB::table('users')
            ->whereNotNull('account_id')
            ->select('account_id', DB::raw('COUNT(*) as user_count'))
            ->groupBy('account_id')
            ->pluck('user_count', 'account_id');

        $externalByAccountId = [];
        foreach ($kimiaExternalAccounts as $externalAccount) {
            $externalByAccountId[(string) $externalAccount->external_id] = $externalAccount;
        }

        $rows = [];
        $seenKimiaIds = [];

        foreach ($accounts as $account) {
            $kimiaId = (string) $account->kimia_id;
            $seenKimiaIds[$kimiaId] = true;
            $externalAccount = $externalByAccountId[$kimiaId] ?? null;
            $userCount = (int) ($userBindingCounts[$account->id] ?? 0);

            $status = match (true) {
                $userCount > 1 => 'duplicate_user_binding',
                $externalAccount !== null && $userCount === 1 => 'matched_linked',
                $externalAccount !== null => 'matched_unlinked',
                $userCount === 1 => 'account_only_linked',
                default => 'account_only_unlinked',
            };

            $rows[] = [
                'status' => $status,
                'local_account_id' => (int) $account->id,
                'kimia_account_id' => (int) $account->kimia_id,
                'external_account_id' => $externalAccount !== null ? (int) $externalAccount->id : null,
                'user_count' => $userCount,
            ];
        }

        foreach ($kimiaExternalAccounts as $externalAccount) {
            $kimiaId = (string) $externalAccount->external_id;
            if (isset($seenKimiaIds[$kimiaId])) {
                continue;
            }

            $rows[] = [
                'status' => 'external_only',
                'local_account_id' => null,
                'kimia_account_id' => (int) $externalAccount->external_id,
                'external_account_id' => (int) $externalAccount->id,
                'user_count' => 0,
            ];
        }

        $orphanedUserBindings = DB::table('users')
            ->leftJoin('accounts', 'accounts.id', '=', 'users.account_id')
            ->whereNotNull('users.account_id')
            ->whereNull('accounts.id')
            ->count();

        $summary = [
            'accounts' => $accounts->count(),
            'kimia_external_accounts' => $kimiaExternalAccounts->count(),
            'matched_linked' => 0,
            'matched_unlinked' => 0,
            'account_only_linked' => 0,
            'account_only_unlinked' => 0,
            'external_only' => 0,
            'duplicate_user_binding' => 0,
            'orphaned_user_bindings' => $orphanedUserBindings,
        ];

        foreach ($rows as $row) {
            $summary[$row['status']]++;
        }

        return [
            'summary' => $summary,
            'rows' => $rows,
        ];
    }
}
