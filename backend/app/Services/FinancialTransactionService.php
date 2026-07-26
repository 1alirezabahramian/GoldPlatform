<?php

namespace App\Services;

use App\Models\FinancialTransaction;

class FinancialTransactionService
{
    public function create(array $data): FinancialTransaction
    {
        return FinancialTransaction::create([
            'reference_type' => $data['reference_type'],
            'reference_id'   => $data['reference_id'],
            'type'           => $data['type'],
            'status'         => 'pending',
            'description'    => $data['description'] ?? null,
        ]);
    }
}