<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\CustomerActivityReadModel;
use App\Support\CustomerApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final class CustomerActivityController extends Controller
{
    public function __invoke(Request $request, CustomerActivityReadModel $activities): JsonResponse
    {
        $data = $request->validate([
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
            'event_type' => ['nullable', 'string', Rule::in(CustomerActivityReadModel::eventTypes())],
        ]);

        $result = $activities->page(
            $request->user(),
            (int) ($data['page'] ?? 1),
            (int) ($data['per_page'] ?? 25),
            $data['event_type'] ?? null,
        );

        return CustomerApiResponse::success(
            $request,
            ['items' => $result['items']],
            ['pagination' => $result['pagination']],
        );
    }
}
