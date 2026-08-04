<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\ReadModels\AdminProductPricingReadModel;
use App\Support\AdminOperatorApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AdminProductPricingReadController extends Controller
{
    public function categories(Request $request, AdminProductPricingReadModel $readModel): JsonResponse
    {
        $validated = $request->validate(['per_page' => ['sometimes', 'integer', 'min:1', 'max:50']]);

        return AdminOperatorApiResponse::success(
            request: $request,
            data: $readModel->categories((int) ($validated['per_page'] ?? 25)),
        );
    }

    public function products(Request $request, AdminProductPricingReadModel $readModel): JsonResponse
    {
        $validated = $request->validate([
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:50'],
            'category_id' => ['sometimes', 'integer', 'min:1'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        return AdminOperatorApiResponse::success(
            request: $request,
            data: $readModel->products(
                perPage: (int) ($validated['per_page'] ?? 25),
                categoryId: isset($validated['category_id']) ? (int) $validated['category_id'] : null,
                isActive: array_key_exists('is_active', $validated) ? (bool) $validated['is_active'] : null,
            ),
        );
    }

    public function pricing(Request $request, AdminProductPricingReadModel $readModel): JsonResponse
    {
        return AdminOperatorApiResponse::success(
            request: $request,
            data: $readModel->pricingOverview(),
        );
    }
}
