<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Customer\CustodyResource;
use App\Http\Resources\Api\V1\Customer\DeliveryResource;
use App\Http\Resources\Api\V1\Customer\OrderResource;
use App\Models\CustodyAsset;
use App\Models\DeliveryRequest;
use App\Models\Order;
use App\Support\CustomerApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CustomerReadController extends Controller
{
    public function orders(Request $request): JsonResponse
    {
        $page = Order::query()
            ->where('user_id', $request->user()->id)
            ->latest('id')
            ->paginate(25);

        return CustomerApiResponse::success($request, [
            'items' => OrderResource::collection(collect($page->items()))->resolve($request),
        ], $this->pagination($page));
    }

    public function custodies(Request $request): JsonResponse
    {
        $page = CustodyAsset::query()
            ->where('user_id', $request->user()->id)
            ->latest('id')
            ->paginate(25);

        return CustomerApiResponse::success($request, [
            'items' => CustodyResource::collection(collect($page->items()))->resolve($request),
        ], $this->pagination($page));
    }

    public function deliveries(Request $request): JsonResponse
    {
        $page = DeliveryRequest::query()
            ->with('custodyAsset:id,uuid')
            ->where('user_id', $request->user()->id)
            ->latest('id')
            ->paginate(25);

        return CustomerApiResponse::success($request, [
            'items' => DeliveryResource::collection(collect($page->items()))->resolve($request),
        ], $this->pagination($page));
    }

    private function pagination($page): array
    {
        return [
            'pagination' => [
                'current_page' => $page->currentPage(),
                'per_page' => $page->perPage(),
                'total' => $page->total(),
                'last_page' => $page->lastPage(),
                'has_more' => $page->hasMorePages(),
            ],
        ];
    }
}
