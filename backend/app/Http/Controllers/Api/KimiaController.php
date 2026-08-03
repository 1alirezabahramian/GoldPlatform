<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Integrations\Kimia\Repositories\KimiaAccountRepository;
use Illuminate\Http\JsonResponse;

class KimiaController extends Controller
{
    public function __construct(
        protected KimiaAccountRepository $accounts
    ) {
    }

    public function accountGroups(): JsonResponse
    {
        return response()->json(
            $this->accounts->groups()
        );
    }
}
