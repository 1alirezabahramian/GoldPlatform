<?php

namespace App\ReadModels;

use App\Models\AuditLog;
use App\Models\DeliveryRequest;
use App\Models\Order;
use App\Models\OutboxMessage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

final class AdminOperatorOperationalQueueReadModel
{
    /** @return array<string, mixed> */
    public function orders(Request $request): array
    {
        $query = Order::query()
            ->select(['id', 'type', 'asset_type', 'asset_quantity', 'asset_unit', 'status', 'created_at', 'expires_at'])
            ->whereIn('status', ['pending', 'approved', 'executing', 'settling'])
            ->oldest('id');

        $this->applyAllowedStatusFilter($query, $request, ['pending', 'approved', 'executing', 'settling']);

        return $this->present($query->paginate($this->perPage($request)));
    }

    /** @return array<string, mixed> */
    public function deliveries(Request $request): array
    {
        $query = DeliveryRequest::query()
            ->select(['uuid', 'branch_code', 'requested_for', 'status', 'created_at'])
            ->whereIn('status', ['requested', 'approved', 'ready'])
            ->oldest('id');

        $this->applyAllowedStatusFilter($query, $request, ['requested', 'approved', 'ready']);

        return $this->present($query->paginate($this->perPage($request)));
    }

    /** @return array<string, mixed> */
    public function audit(Request $request): array
    {
        $query = AuditLog::query()
            ->select(['id', 'actor_id', 'action', 'subject_type', 'request_id', 'created_at'])
            ->latest('id');

        if ($request->filled('action')) {
            $query->where('action', $request->string('action')->toString());
        }

        if ($request->filled('request_id')) {
            $query->where('request_id', $request->string('request_id')->toString());
        }

        return $this->present($query->paginate($this->perPage($request)));
    }

    /** @return array<string, mixed> */
    public function outbox(Request $request): array
    {
        $query = OutboxMessage::query()
            ->select(['uuid', 'event_type', 'aggregate_type', 'available_at', 'processed_at', 'attempts', 'created_at'])
            ->latest('id');

        if ($request->filled('event_type')) {
            $query->where('event_type', $request->string('event_type')->toString());
        }

        return $this->present($query->paginate($this->perPage($request)));
    }

    /** @param Builder<*> $query @param list<string> $allowed */
    private function applyAllowedStatusFilter(Builder $query, Request $request, array $allowed): void
    {
        if (! $request->filled('status')) {
            return;
        }

        $status = $request->string('status')->toString();
        abort_unless(in_array($status, $allowed, true), 422, 'Unsupported status filter.');
        $query->where('status', $status);
    }

    private function perPage(Request $request): int
    {
        return min(max($request->integer('per_page', 25), 1), 50);
    }

    /** @return array<string, mixed> */
    private function present(LengthAwarePaginator $paginator): array
    {
        return [
            'items' => collect($paginator->items())->map(fn ($item) => $item->toArray())->values()->all(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ];
    }
}
