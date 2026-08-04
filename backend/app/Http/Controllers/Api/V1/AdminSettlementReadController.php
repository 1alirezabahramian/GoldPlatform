<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Settlement;
use App\ReadModels\AdminSettlementReadModel;
use App\Support\AdminOperatorApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AdminSettlementReadController extends Controller
{
    public function __construct(private readonly AdminSettlementReadModel $readModel) {}

    public function index(Request $request): JsonResponse
    {
        return AdminOperatorApiResponse::success($this->readModel->index($request));
    }

    public function show(Settlement $settlement): JsonResponse
    {
        return AdminOperatorApiResponse::success($this->readModel->show($settlement));
    }
}
