<?php

namespace App\ReadModels;

use Illuminate\Support\Facades\Schema;

final class AdminSettlementActionCapabilityReadModel
{
    public function overview(): array
    {
        return [
            'settlement_store' => [
                'supported' => Schema::hasTable('settlements'),
            ],
            'actions' => [
                'retry' => [
                    'supported' => false,
                    'reason' => 'settlement_retry_service_not_confirmed',
                ],
                'approve' => [
                    'supported' => false,
                    'reason' => 'settlement_approval_workflow_not_confirmed',
                ],
                'cancel' => [
                    'supported' => false,
                    'reason' => 'settlement_cancellation_rule_not_confirmed',
                ],
                'kimia_write' => [
                    'supported' => false,
                    'reason' => 'kimia_write_contract_not_confirmed',
                ],
            ],
            'required_controls' => [
                'idempotency' => true,
                'row_locking' => true,
                'audit_log' => true,
                'outbox' => true,
                'permission' => true,
                'approval_workflow' => true,
            ],
            'discovery' => [
                'status' => 'blocked_pending_ground_truth',
                'write_endpoints_exposed' => false,
            ],
        ];
    }
}
