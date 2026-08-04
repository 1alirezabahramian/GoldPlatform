<?php

namespace App\Services\Trading;

use App\Enums\AssetType;
use App\Models\KimiaCoin;
use App\Models\KimiaCurrency;
use LogicException;

class AssetRegistryService
{
    public function assertTradable(AssetType $type, ?int $externalAssetId): void
    {
        if (! $type->requiresExternalAssetId()) {
            if ($externalAssetId !== null) {
                throw new LogicException("{$type->value} orders must not carry an external asset id.");
            }
            return;
        }

        if ($externalAssetId === null || $externalAssetId <= 0) {
            throw new LogicException("{$type->value} orders require a valid Kimia asset id.");
        }

        $exists = match ($type) {
            AssetType::Coin => KimiaCoin::query()->where('kimia_id', $externalAssetId)->where('is_visible', true)->exists(),
            AssetType::Currency => KimiaCurrency::query()->where('kimia_id', $externalAssetId)->where('is_visible', true)->exists(),
            default => true,
        };

        if (! $exists) {
            throw new LogicException("The requested {$type->value} asset is not available in the synchronized Kimia catalog.");
        }
    }
}
