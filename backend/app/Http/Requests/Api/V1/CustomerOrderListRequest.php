<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\OrderStatus;
use Illuminate\Validation\Rule;

final class CustomerOrderListRequest extends CustomerPaginationRequest
{
    /** @return array<string, list<string|Rule>> */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'status' => ['sometimes', 'string', Rule::enum(OrderStatus::class)],
        ]);
    }
}
