<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Support\BackofficeApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AdminBootstrapController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return BackofficeApiResponse::success($request, [
            'panel' => 'admin',
            'navigation' => [
                ['code' => 'audit_logs', 'path' => '/admin/audit-logs'],
                ['code' => 'outbox', 'path' => '/admin/outbox'],
                ['code' => 'customer_policies', 'path' => '/admin/customer-policies'],
            ],
            'capabilities' => [
                'audit_logs.read',
                'outbox.read',
                'customer_policies.read',
                'customer_policies.update',
            ],
        ]);
    }
}
