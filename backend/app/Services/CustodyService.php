<?php

namespace App\Services;

use App\Enums\CustodyStatus;
use App\Models\CustodyAsset;
use Illuminate\Support\Facades\DB;
use LogicException;

class CustodyService
{
    public function receive(array $data): CustodyAsset
    {
        return DB::transaction(function () use ($data): CustodyAsset {
            if (! empty($data['uuid'])) {
                $existing = CustodyAsset::query()->where('uuid', $data['uuid'])->first();
                if ($existing) {
                    return $existing;
                }
            }

            return CustodyAsset::query()->create($data + [
                'status' => CustodyStatus::InCustody,
                'acquired_at' => now(),
            ]);
        });
    }

    public function reserve(CustodyAsset $asset): CustodyAsset
    {
        return $this->transition($asset, CustodyStatus::Reserved, [CustodyStatus::InCustody]);
    }

    public function markDeliveryRequested(CustodyAsset $asset): CustodyAsset
    {
        return $this->transition($asset, CustodyStatus::DeliveryRequested, [
            CustodyStatus::InCustody,
            CustodyStatus::Reserved,
        ]);
    }

    public function markReady(CustodyAsset $asset): CustodyAsset
    {
        return $this->transition($asset, CustodyStatus::ReadyForPickup, [
            CustodyStatus::DeliveryRequested,
        ], ['ready_at' => now()]);
    }

    public function markDelivered(CustodyAsset $asset): CustodyAsset
    {
        return $this->transition($asset, CustodyStatus::Delivered, [
            CustodyStatus::ReadyForPickup,
        ], ['delivered_at' => now()]);
    }

    public function closeAs(CustodyAsset $asset, CustodyStatus $status, string $reference): CustodyAsset
    {
        if (! in_array($status, [
            CustodyStatus::Resold,
            CustodyStatus::ConvertedToGold,
            CustodyStatus::ConvertedToMoney,
        ], true)) {
            throw new LogicException('Unsupported custody close status.');
        }
        if (trim($reference) === '') {
            throw new LogicException('A financial reference is required.');
        }

        return $this->transition($asset, $status, [CustodyStatus::InCustody], [
            'metadata' => array_merge($asset->metadata ?? [], ['financial_reference' => $reference]),
        ]);
    }

    private function transition(CustodyAsset $asset, CustodyStatus $to, array $from, array $extra = []): CustodyAsset
    {
        return DB::transaction(function () use ($asset, $to, $from, $extra): CustodyAsset {
            $locked = CustodyAsset::query()->lockForUpdate()->findOrFail($asset->id);
            if ($locked->status === $to) {
                return $locked;
            }
            if ($locked->status->isTerminal() || ! in_array($locked->status, $from, true)) {
                throw new LogicException("Invalid custody transition {$locked->status->value} -> {$to->value}.");
            }
            $locked->forceFill($extra + ['status' => $to])->save();
            return $locked->refresh();
        });
    }
}
