<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CustomerCustodyListRequest;
use App\Http\Requests\Api\V1\CustomerDeliveryListRequest;
use App\Http\Requests\Api\V1\CustomerOrderListRequest;
use App\Models\CustodyAsset;
use App\Models\DeliveryRequest;
use App\Models\Order;
use App\Support\CustomerApiResponse;
use App\Support\CustomerReadPresenter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

final class CustomerReadController extends Controller
{
    public function orders(CustomerOrderListRequest $request, CustomerReadPresenter $presenter): JsonResponse
    {
        $page = Order::query()
            ->where('user_id', $request->user()->id)
            ->when($request->status(), fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($request->fromDate(), fn (Builder $query, string $from) => $query->whereDate('created_at', '>=', $from))
            ->when($request->toDate(), fn (Builder $query, string $to) => $query->whereDate('created_at', '<=', $to))
            ->orderBy('id', $request->sortDirection())
            ->paginate($request->perPage());

        return CustomerApiResponse::success($request, [
            'items' => collect($page->items())->map(fn (Order $order) => $presenter->order($order))->all(),
        ], $this->pagination($page, $request->status(), $request->sort(), $request->fromDate(), $request->toDate()));
    }

    public function custodies(CustomerCustodyListRequest $request, CustomerReadPresenter $presenter): JsonResponse
    {
        $page = CustodyAsset::query()
            ->where('user_id', $request->user()->id)
            ->when($request->status(), fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($request->fromDate(), fn (Builder $query, string $from) => $query->whereDate('created_at', '>=', $from))
            ->when($request->toDate(), fn (Builder $query, string $to) => $query->whereDate('created_at', '<=', $to))
            ->orderBy('id', $request->sortDirection())
            ->paginate($request->perPage());

        return CustomerApiResponse::success($request, [
            'items' => collect($page->items())->map(fn (CustodyAsset $asset) => $presenter->custody($asset))->all(),
        ], $this->pagination($page, $request->status(), $request->sort(), $request->fromDate(), $request->toDate()));
    }

    public function deliveries(CustomerDeliveryListRequest $request, CustomerReadPresenter $presenter): JsonResponse
    {
        $page = DeliveryRequest::query()
            ->with('custodyAsset:id,uuid')
            ->where('user_id', $request->user()->id)
            ->when($request->status(), fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($request->fromDate(), fn (Builder $query, string $from) => $query->whereDate('created_at', '>=', $from))
            ->when($request->toDate(), fn (Builder $query, string $to) => $query->whereDate('created_at', '<=', $to))
            ->orderBy('id', $request->sortDirection())
            ->paginate($request->perPage());

        return CustomerApiResponse::success($request, [
            'items' => collect($page->items())->map(fn (DeliveryRequest $delivery) => $presenter->delivery($delivery))->all(),
        ], $this->pagination($page, $request->status(), $request->sort(), $request->fromDate(), $request->toDate()));
    }

    private function pagination($page, ?string $status, string $sort, ?string $from, ?string $to): array
    {
        return [
            'filters' => [
                'status' => $status,
                'sort' => $sort,
                'from' => $from,
                'to' => $to,
            ],
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
