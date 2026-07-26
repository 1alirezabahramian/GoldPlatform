<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    public function store(Request $request)
    {
        $order = $this->orderService->create($request->all());

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }
}