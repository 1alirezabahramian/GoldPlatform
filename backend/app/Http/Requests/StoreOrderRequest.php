<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:buy,sell'],
            'gold_weight' => ['required', 'decimal:0,3', 'gt:0'],
            'gold_price' => ['required', 'integer', 'min:0'],
            'commission' => ['sometimes', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:2000'],
            'user_id' => ['prohibited'],
            'status' => ['prohibited'],
            'total_price' => ['prohibited'],
        ];
    }
}
