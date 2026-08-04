<?php

namespace App\ReadModels;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final class AdminUserReadModel
{
    /** @return array<string, mixed> */
    public function index(Request $request): array
    {
        $validated = $request->validate([
            'status' => ['nullable', 'in:active,inactive'],
            'mobile_verified' => ['nullable', 'boolean'],
            'group_id' => ['nullable', 'integer', 'min:1'],
            'role' => ['nullable', 'string', 'max:80'],
            'search' => ['nullable', 'string', 'max:80'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = User::query()
            ->select([
                'id', 'name', 'first_name', 'last_name', 'mobile', 'group_id',
                'mobile_verified', 'is_active', 'last_login_at', 'created_at',
            ])
            ->with([
                'group:id,title,is_active',
                'roles:id,name',
            ])
            ->latest('id');

        if (($validated['status'] ?? null) === 'active') {
            $query->where('is_active', true);
        } elseif (($validated['status'] ?? null) === 'inactive') {
            $query->where('is_active', false);
        }

        if (array_key_exists('mobile_verified', $validated)) {
            $query->where('mobile_verified', (bool) $validated['mobile_verified']);
        }

        if (isset($validated['group_id'])) {
            $query->where('group_id', $validated['group_id']);
        }

        if (isset($validated['role'])) {
            $query->whereHas('roles', fn ($roleQuery) => $roleQuery->where('name', $validated['role']));
        }

        if (isset($validated['search'])) {
            $term = trim($validated['search']);
            $query->where(function ($searchQuery) use ($term): void {
                $searchQuery
                    ->where('name', 'like', '%'.$term.'%')
                    ->orWhere('first_name', 'like', '%'.$term.'%')
                    ->orWhere('last_name', 'like', '%'.$term.'%')
                    ->orWhere('mobile', 'like', '%'.$term.'%');
            });
        }

        return $this->present($query->paginate((int) ($validated['per_page'] ?? 25)));
    }

    /** @return array<string, mixed> */
    private function present(LengthAwarePaginator $paginator): array
    {
        return [
            'items' => collect($paginator->items())->map(function (User $user): array {
                $displayName = trim((string) $user->name);
                if ($displayName === '') {
                    $displayName = trim(implode(' ', array_filter([$user->first_name, $user->last_name])));
                }

                return [
                    'id' => $user->id,
                    'display_name' => $displayName !== '' ? $displayName : null,
                    'mobile_masked' => $this->maskMobile($user->mobile),
                    'is_active' => (bool) $user->is_active,
                    'mobile_verified' => (bool) $user->mobile_verified,
                    'last_login_at' => $user->last_login_at?->toIso8601String(),
                    'created_at' => $user->created_at?->toIso8601String(),
                    'group' => $user->group ? [
                        'id' => $user->group->id,
                        'title' => $user->group->title,
                        'is_active' => (bool) $user->group->is_active,
                    ] : null,
                    'roles' => $user->roles->pluck('name')->sort()->values()->all(),
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

    private function maskMobile(?string $mobile): ?string
    {
        if ($mobile === null || $mobile === '') {
            return null;
        }

        $length = Str::length($mobile);
        if ($length <= 4) {
            return str_repeat('*', $length);
        }

        return Str::substr($mobile, 0, 3)
            .str_repeat('*', max($length - 6, 3))
            .Str::substr($mobile, -3);
    }
}
