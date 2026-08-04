<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\BackofficeApiResponse;
use App\Support\BackofficeSessionBootstrap;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AdminBootstrapController extends Controller
{
    public function __invoke(Request $request, BackofficeSessionBootstrap $bootstrap): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return BackofficeApiResponse::success(
            $request,
            $bootstrap->for($user, 'admin'),
        );
    }
}
