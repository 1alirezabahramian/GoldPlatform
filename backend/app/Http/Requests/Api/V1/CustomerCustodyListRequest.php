<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\CustodyStatus;
use Illuminate\Validation\Rule;

final class CustomerCustodyListRequest extends CustomerPaginationRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'status' => ['sometimes', 'string', Rule::enum(CustodyStatus::class)],
        ]);
    }
}
