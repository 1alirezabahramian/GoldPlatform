<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CustodyAsset;
use App\Models\DeliveryRequest;
use App\Services\DeliveryService;
use App\Support\CustomerApiResponse;
use App\Support\CustomerReadPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use LogicException;

final class CustomerCustodyDeliveryController extends Controller
{
    public function showCustody(
        Request $request,
        string $reference,
        CustomerReadPresenter $presenter
    ): JsonResponse {
        $asset = CustodyAsset::query()
            ->where('uuid', $reference)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $asset) {
            return CustomerApiResponse::error(
                $request,
                'امانت موردنظر پیدا نشد.',
                'CUSTODY_NOT_FOUND',
                404
            );
        }

        return CustomerApiResponse::success($request, [
            'custody' => $presenter->custody($asset),
        ]);
    }

    public function showDelivery(
        Request $request,
        string $reference,
        CustomerReadPresenter $presenter
    ): JsonResponse {
        $delivery = DeliveryRequest::query()
            ->with('custodyAsset:id,uuid')
            ->where('uuid', $reference)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $delivery) {
            return CustomerApiResponse::error(
                $request,
                'درخواست تحویل موردنظر پیدا نشد.',
                'DELIVERY_NOT_FOUND',
                404
            );
        }

        return CustomerApiResponse::success($request, [
            'delivery' => $presenter->delivery($delivery),
        ]);
    }

    public function requestDelivery(
        Request $request,
        string $reference,
        DeliveryService $deliveries,
        CustomerReadPresenter $presenter
    ): JsonResponse {
        $data = $request->validate([
            'branch_code' => ['nullable', 'string', 'max:80'],
            'requested_for' => ['nullable', 'date'],
        ]);

        $asset = CustodyAsset::query()
            ->where('uuid', $reference)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $asset) {
            return CustomerApiResponse::error(
                $request,
                'امانت موردنظر پیدا نشد.',
                'CUSTODY_NOT_FOUND',
                404
            );
        }

        try {
            $delivery = $deliveries->request($asset, $request->user(), $data)
                ->loadMissing('custodyAsset:id,uuid');
        } catch (LogicException) {
            return CustomerApiResponse::error(
                $request,
                'درخواست تحویل در وضعیت فعلی قابل انجام نیست.',
                'DELIVERY_NOT_ALLOWED',
                409
            );
        }

        return response()->json([
            'data' => [
                'delivery' => $presenter->delivery($delivery),
            ],
            'meta' => [
                'request_id' => $request->attributes->get('request_id'),
                'generated_at' => now()->toIso8601String(),
                'api_version' => 'v1',
            ],
            'message' => null,
        ], 201);
    }
}
