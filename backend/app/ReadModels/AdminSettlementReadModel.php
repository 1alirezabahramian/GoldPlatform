<?php

namespace App\ReadModels;

use App\Enums\SettlementStatus;
use App\Models\Settlement;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

final class AdminSettlementReadModel
{
    /** @return array<string, mixed> */
    public function index(Request $request): array
    {
        $validated = $request->validate([
            'status' => ['nullable', 'in:'.implode(',', array_column(SettlementStatus::cases(), 'value'))],
            'asset_type' => ['nullable', 'string', 'max:80'],
            'order_id' => ['nullable', 'integer', 'min:1'],
            'trade_id' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = Settlement::query()
            ->select([
                'id', 'uuid', 'order_id', 'trade_id', 'status', 'asset_type', 'amount',
                'failure_reason', 'processing_started_at', 'completed_at', 'failed_at',
                'created_at', 'updated_at',
            ])
            ->latest('id');

        foreach (['status', 'asset_type', 'order_id', 'trade_id'] as $filter) {
            if (isset($validated[$filter])) {
                $query->where($filter, $validated[$filter]);
            }
        }

        return $this->presentPage($query->paginate((int) ($validated['per_page'] ?? 25)));
    }

    /** @return array<string, mixed> */
    public function show(Settlement $settlement): array
    {
        $settlement->loadMissing([
            'order:id,type,asset_type,status,created_at',
            'trade:id,order_id,trade_no,status,executed_at',
        ]);

        return [
            'settlement' => $this->presentSettlement($settlement),
            'timeline' => $this->timeline($settlement),
            'order' => $settlement->order ? [
                'id' => $settlement->order->id,
                'type' => $settlement->order->type,
                'asset_type' => $settlement->order->asset_type,
                'status' => $settlement->order->status,
                'created_at' => $settlement->order->created_at?->toIso8601String(),
            ] : null,
            'trade' => $settlement->trade ? [
                'id' => $settlement->trade->id,
                'trade_no' => $settlement->trade->trade_no,
                'status' => $settlement->trade->status,
                'executed_at' => $settlement->trade->executed_at?->toIso8601String(),
            ] : null,
        ];
    }

    /** @return array<string, mixed> */
    private function presentPage(LengthAwarePaginator $paginator): array
    {
        return [
            'items' => collect($paginator->items())
                ->map(fn (Settlement $settlement): array => $this->presentSettlement($settlement))
                ->values()->all(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function presentSettlement(Settlement $settlement): array
    {
        $status = $settlement->status instanceof SettlementStatus
            ? $settlement->status->value
            : (string) $settlement->status;

        return [
            'reference' => $settlement->uuid,
            'order_id' => $settlement->order_id,
            'trade_id' => $settlement->trade_id,
            'status' => $status,
            'asset_type' => $settlement->asset_type,
            'amount' => $settlement->amount,
            'has_failure' => filled($settlement->failure_reason),
            'failure_category' => $this->failureCategory($settlement->failure_reason),
            'processing_started_at' => $settlement->processing_started_at?->toIso8601String(),
            'completed_at' => $settlement->completed_at?->toIso8601String(),
            'failed_at' => $settlement->failed_at?->toIso8601String(),
            'created_at' => $settlement->created_at?->toIso8601String(),
            'updated_at' => $settlement->updated_at?->toIso8601String(),
        ];
    }

    /** @return list<array{event:string,at:string}> */
    private function timeline(Settlement $settlement): array
    {
        $events = [
            'created' => $settlement->created_at,
            'processing_started' => $settlement->processing_started_at,
            'completed' => $settlement->completed_at,
            'failed' => $settlement->failed_at,
        ];

        $timeline = [];
        foreach ($events as $event => $at) {
            if ($at !== null) {
                $timeline[] = ['event' => $event, 'at' => $at->toIso8601String()];
            }
        }

        usort($timeline, fn (array $a, array $b): int => strcmp($a['at'], $b['at']));
        return $timeline;
    }

    private function failureCategory(?string $reason): ?string
    {
        if (blank($reason)) {
            return null;
        }

        $value = strtolower($reason);
        return match (true) {
            str_contains($value, 'timeout') => 'timeout',
            str_contains($value, 'connection') || str_contains($value, 'network') => 'connection',
            str_contains($value, 'validation') || str_contains($value, 'invalid') => 'validation',
            default => 'processing',
        };
    }
}
