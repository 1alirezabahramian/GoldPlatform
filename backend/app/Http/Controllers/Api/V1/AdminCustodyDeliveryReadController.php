<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CustodyAsset;
use App\Models\DeliveryRequest;
use App\ReadModels\AdminCustodyDeliveryReadModel;
use App\Support\AdminOperatorApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AdminCustodyDeliveryReadController extends Controller
{
    public function __construct(private readonly AdminCustodyDeliveryReadModel $readModel) {}

    public function custodies(Request $request): JsonResponse
    {
        return AdminOperatorApiResponse::success($this->readModel->custodies($request));
    }

    public function custody(CustodyAsset $custodyAsset): JsonResponse
    {
        return AdminOperatorApiResponse::success($this->readModel->custody($custodyAsset));
    }

    public function deliveries(Request $request): JsonResponse
    {
        return AdminOperatorApiResponse::success($this->readModel->deliveries($request));
    }

    public function delivery(DeliveryRequest $deliveryRequest): JsonResponse
    {
        return AdminOperatorApiResponse::success($this->readModel->delivery($deliveryRequest));
    }
}
