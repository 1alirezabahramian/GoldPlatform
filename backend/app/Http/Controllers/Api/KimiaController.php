<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Kimia\AccountRepository;
use Illuminate\Http\JsonResponse;

class KimiaController extends Controller
{
    public function __construct(
        protected AccountRepository $accounts
    ) {
    }

    public function accountGroups(): JsonResponse
    {
        return response()->json(
            $this->accounts->groups()
        );
    }
}