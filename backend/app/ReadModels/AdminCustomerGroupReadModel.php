<?php

namespace App\ReadModels;

use App\Models\CustomerTradingPolicy;
use App\Models\UserGroup;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

final class AdminCustomerGroupReadModel
{
    /** @return array<string, mixed> */
    public function index(Request $request): array
    {
        $validated = $request->validate([
            'status' => ['nullable', 'in:active,inactive'],
            'search' => ['nullable', 'string', 'max:80'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = UserGroup::query()
            ->select(['id', 'title', 'priority', 'is_active', 'created_at'])
            ->withCount('users')
            ->orderBy('priority')
            ->orderBy('id');

        if (($validated['status'] ?? null) === 'active') {
            $query->where('is_active', true);
        } elseif (($validated['status'] ?? null) === 'inactive') {
            $query->where('is_active', false);
        }

        if (isset($validated['search'])) {
            $query->where('title', 'like', '%'.trim($validated['search']).'%');
        }

        return $this->present($query->paginate((int) ($validated['per_page'] ?? 25)));
    }

    /** @return array<string, mixed> */
    private function present(LengthAwarePaginator $paginator): array
    {
        $groups = collect($paginator->items());
        $policies = CustomerTradingPolicy::query()
            ->select([
                'id', 'user_group_id', 'requires_available_balance', 'allow_negative_balance',
                'asset_lock_minutes', 'max_gold_weight', 'max_coin_quantity',
                'max_money_amount', 'credit_limit', 'min_order_amount', 'max_order_amount',
                'max_delivery_items', 'is_active', 'created_at', 'updated_at',
            ])
            ->whereIn('user_group_id', $groups->pluck('id'))
            ->orderBy('id')
            ->get()
            ->groupBy('user_group_id');

        return [
            'items' => $groups->map(function (UserGroup $group) use ($policies): array {
                return [
                    'id' => $group->id,
                    'title' => $group->title,
                    'priority' => $group->priority,
                    'is_active' => (bool) $group->is_active,
                    'users_count' => (int) $group->users_count,
                    'created_at' => $group->created_at?->toIso8601String(),
                    'policies' => collect($policies->get($group->id, []))->map(fn (CustomerTradingPolicy $policy): array => [
                        'id' => $policy->id,
                        'requires_available_balance' => (bool) $policy->requires_available_balance,
                        'allow_negative_balance' => (bool) $policy->allow_negative_balance,
                        'asset_lock_minutes' => $policy->asset_lock_minutes,
                        'max_gold_weight' => $policy->max_gold_weight,
                        'max_coin_quantity' => $policy->max_coin_quantity,
                        'max_money_amount' => $policy->max_money_amount,
                        'credit_limit' => $policy->credit_limit,
                        'min_order_amount' => $policy->min_order_amount,
                        'max_order_amount' => $policy->max_order_amount,
                        'max_delivery_items' => $policy->max_delivery_items,
                        'is_active' => (bool) $policy->is_active,
                        'created_at' => $policy->created_at?->toIso8601String(),
                        'updated_at' => $policy->updated_at?->toIso8601String(),
                    ])->values()->all(),
                ];
            })->values()->all(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ];
    }
}
