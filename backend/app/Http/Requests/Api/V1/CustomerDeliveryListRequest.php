<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\DeliveryStatus;
use Illuminate\Validation\Rule;

final class CustomerDeliveryListRequest extends CustomerPaginationRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'status' => ['sometimes', 'string', Rule::enum(DeliveryStatus::class)],
        ]);
    }
}
