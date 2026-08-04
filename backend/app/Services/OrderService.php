<?php

namespace App\Services;

use App\Enums\AssetType;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\Trading\AssetRegistryService;
use App\Support\Decimal;
use LogicException;

class OrderService
{
    public function __construct(private readonly AssetRegistryService $assets) {}

    public function create(array $data): Order
    {
        $assetType = AssetType::from((string) ($data['asset_type'] ?? AssetType::Gold->value));
        $externalAssetId = isset($data['external_asset_id']) ? (int) $data['external_asset_id'] : null;
        $this->assets->assertTradable($assetType, $externalAssetId);

        $quantity = (string) ($data['asset_quantity'] ?? $data['gold_weight'] ?? '0');
        $unitPrice = (string) ($data['unit_price'] ?? $data['gold_price'] ?? '0');
        $commission = (string) ($data['commission'] ?? '0');

        if (Decimal::compare($quantity, '0') <= 0 || Decimal::compare($unitPrice, '0') <= 0) {
            throw new LogicException('Order quantity and unit price must be positive.');
        }

        $totalPrice = isset($data['total_price'])
            ? Decimal::normalize((string) $data['total_price'], 2)
            : Decimal::add(Decimal::multiply($quantity, $unitPrice, 2), $commission, 2);

        return Order::query()->create([
            'user_id' => $data['user_id'],
            'type' => $data['type'],
            'asset_type' => $assetType,
            'external_asset_id' => $externalAssetId,
            'asset_quantity' => Decimal::normalize($quantity),
            'asset_unit' => (string) ($data['asset_unit'] ?? match ($assetType) {
                AssetType::Money => 'IRR',
                AssetType::Gold => 'GOLD18',
                AssetType::Coin => 'PIECE',
                AssetType::Currency => 'UNIT',
            }),
            'status' => OrderStatus::Pending,
            'gold_weight' => $assetType === AssetType::Gold ? Decimal::normalize($quantity, 3) : null,
            'gold_price' => $assetType === AssetType::Gold ? Decimal::normalize($unitPrice, 0) : null,
            'commission' => Decimal::normalize($commission, 2),
            'total_price' => $totalPrice,
            'description' => $data['description'] ?? null,
            'expires_at' => $data['expires_at'] ?? null,
        ]);
    }
}
