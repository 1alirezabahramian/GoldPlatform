<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CustodyAsset;
use App\Models\DeliveryRequest;
use App\Models\Order;
use App\Support\CustomerApiResponse;
use App\Support\CustomerReadPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CustomerReadController extends Controller
{
    public function orders(Request $request, CustomerReadPresenter $presenter): JsonResponse
    {
        $page = Order::query()
            ->where('user_id', $request->user()->id)
            ->latest('id')
            ->paginate(25);

        return CustomerApiResponse::success($request, [
            'items' => collect($page->items())->map(fn (Order $order) => $presenter->order($order))->all(),
        ], $this->pagination($page));
    }

    public function custodies(Request $request, CustomerReadPresenter $presenter): JsonResponse
    {
        $page = CustodyAsset::query()
            ->where('user_id', $request->user()->id)
            ->latest('id')
            ->paginate(25);

        return CustomerApiResponse::success($request, [
            'items' => collect($page->items())->map(fn (CustodyAsset $asset) => $presenter->custody($asset))->all(),
        ], $this->pagination($page));
    }

    public function deliveries(Request $request, CustomerReadPresenter $presenter): JsonResponse
    {
        $page = DeliveryRequest::query()
            ->with('custodyAsset:id,uuid')
            ->where('user_id', $request->user()->id)
            ->latest('id')
            ->paginate(25);

        return CustomerApiResponse::success($request, [
            'items' => collect($page->items())->map(fn (DeliveryRequest $delivery) => $presenter->delivery($delivery))->all(),
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
