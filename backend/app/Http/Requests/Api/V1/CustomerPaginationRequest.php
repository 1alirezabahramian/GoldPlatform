<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class CustomerPaginationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, list<string>> */
    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:50'],
            'sort' => ['sometimes', 'string', 'in:newest,oldest'],
            'from' => ['sometimes', 'date_format:Y-m-d'],
            'to' => ['sometimes', 'date_format:Y-m-d', 'after_or_equal:from'],
        ];
    }

    public function perPage(): int
    {
        return (int) $this->validated('per_page', 25);
    }

    public function status(): ?string
    {
        $status = $this->validated('status');

        return is_string($status) ? $status : null;
    }

    public function sort(): string
    {
        $sort = $this->validated('sort', 'newest');

        return $sort === 'oldest' ? 'oldest' : 'newest';
    }

    public function sortDirection(): string
    {
        return $this->sort() === 'oldest' ? 'asc' : 'desc';
    }

    public function fromDate(): ?string
    {
        $from = $this->validated('from');

        return is_string($from) ? $from : null;
    }

    public function toDate(): ?string
    {
        $to = $this->validated('to');

        return is_string($to) ? $to : null;
    }
}
