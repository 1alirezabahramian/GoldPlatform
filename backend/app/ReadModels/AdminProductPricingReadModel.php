<?php

namespace App\ReadModels;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class AdminProductPricingReadModel
{
    /** @return array<string, mixed> */
    public function categories(int $perPage): array
    {
        if (! Schema::hasTable('product_categories')) {
            return $this->emptyPage($perPage);
        }

        $page = DB::table('product_categories')
            ->select(['id', 'title', 'slug', 'is_active', 'sort_order', 'created_at', 'updated_at'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate($perPage);

        return $this->page($page, static fn (object $row): array => [
            'id' => (int) $row->id,
            'title' => (string) $row->title,
            'slug' => (string) $row->slug,
            'is_active' => (bool) $row->is_active,
            'sort_order' => (int) $row->sort_order,
            'created_at' => $row->created_at,
            'updated_at' => $row->updated_at,
        ]);
    }

    /** @return array<string, mixed> */
    public function products(int $perPage, ?int $categoryId, ?bool $isActive): array
    {
        if (! Schema::hasTable('products')) {
            return $this->emptyPage($perPage);
        }

        $query = DB::table('products as p')
            ->leftJoin('product_categories as c', 'c.id', '=', 'p.category_id')
            ->select([
                'p.id', 'p.kimia_product_id', 'p.category_id', 'c.title as category_title',
                'p.title', 'p.barcode', 'p.weight', 'p.fineness', 'p.buy_price',
                'p.sell_price', 'p.stock', 'p.is_active', 'p.created_at', 'p.updated_at',
            ]);

        if ($categoryId !== null) {
            $query->where('p.category_id', $categoryId);
        }

        if ($isActive !== null) {
            $query->where('p.is_active', $isActive);
        }

        $page = $query->orderBy('p.id')->paginate($perPage);

        return $this->page($page, static fn (object $row): array => [
            'id' => (int) $row->id,
            'kimia_product_id' => $row->kimia_product_id === null ? null : (int) $row->kimia_product_id,
            'category' => [
                'id' => (int) $row->category_id,
                'title' => $row->category_title,
            ],
            'title' => (string) $row->title,
            'barcode' => $row->barcode,
            'weight' => (string) $row->weight,
            'fineness' => (int) $row->fineness,
            'stored_prices' => [
                'buy' => (string) $row->buy_price,
                'sell' => (string) $row->sell_price,
                'unit' => 'unspecified_in_schema',
            ],
            'stock' => (int) $row->stock,
            'is_active' => (bool) $row->is_active,
            'created_at' => $row->created_at,
            'updated_at' => $row->updated_at,
        ]);
    }

    /** @return array<string, mixed> */
    public function pricingOverview(): array
    {
        return [
            'stored_product_prices_supported' => Schema::hasColumns('products', ['buy_price', 'sell_price']),
            'formula_management_supported' => false,
            'spread_management_supported' => false,
            'rounding_management_supported' => false,
            'dynamic_coin_catalog_supported' => false,
            'dynamic_currency_catalog_supported' => false,
            'kimia_product_sync_supported' => false,
            'notes' => [
                'Only stored product buy_price and sell_price columns are available in the confirmed schema.',
                'Price unit is not declared by the products table schema and is therefore not inferred.',
            ],
        ];
    }

    /** @param callable(object): array<string, mixed> $map */
    private function page(LengthAwarePaginator $page, callable $map): array
    {
        return [
            'items' => collect($page->items())->map($map)->values()->all(),
            'pagination' => [
                'current_page' => $page->currentPage(),
                'per_page' => $page->perPage(),
                'total' => $page->total(),
                'last_page' => $page->lastPage(),
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function emptyPage(int $perPage): array
    {
        return [
            'items' => [],
            'pagination' => [
                'current_page' => 1,
                'per_page' => $perPage,
                'total' => 0,
                'last_page' => 1,
            ],
        ];
    }
}
