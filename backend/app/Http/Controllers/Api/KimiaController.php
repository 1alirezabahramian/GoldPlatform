<?php

namespace App\Http\Controllers\Api;

use App\Application\Kimia\AccountGroupReadService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class KimiaController extends Controller
{
    public function __construct(
        protected AccountGroupReadService $accountGroups
    ) {
    }

    public function accountGroups(): JsonResponse
    {
        return response()->json(
            $this->accountGroups->read()
        );
    }
}
