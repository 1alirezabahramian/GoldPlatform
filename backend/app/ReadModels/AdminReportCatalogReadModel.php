<?php

namespace App\ReadModels;

final class AdminReportCatalogReadModel
{
    public function catalog(): array
    {
        return [
            'reports' => [
                ['key' => 'orders', 'supported' => true, 'source' => 'orders'],
                ['key' => 'trades', 'supported' => true, 'source' => 'trades'],
                ['key' => 'settlements', 'supported' => true, 'source' => 'settlements'],
                ['key' => 'custodies', 'supported' => true, 'source' => 'custody_assets'],
                ['key' => 'deliveries', 'supported' => true, 'source' => 'delivery_requests'],
                ['key' => 'users', 'supported' => true, 'source' => 'users'],
                ['key' => 'customer_groups', 'supported' => true, 'source' => 'user_groups'],
                ['key' => 'audit_logs', 'supported' => true, 'source' => 'audit_logs'],
                ['key' => 'outbox', 'supported' => true, 'source' => 'outbox_messages'],
                ['key' => 'revenue', 'supported' => false, 'reason' => 'financial_definition_not_confirmed'],
                ['key' => 'profit_and_loss', 'supported' => false, 'reason' => 'accounting_contract_not_confirmed'],
                ['key' => 'gold_holdings_valuation', 'supported' => false, 'reason' => 'valuation_contract_not_confirmed'],
                ['key' => 'kimia_accounting', 'supported' => false, 'reason' => 'kimia_reporting_contract_not_implemented'],
            ],
            'exports' => [
                'execution_supported' => false,
                'formats' => [
                    'json' => ['supported' => false, 'reason' => 'export_service_not_implemented'],
                    'csv' => ['supported' => false, 'reason' => 'export_service_not_implemented'],
                    'xlsx' => ['supported' => false, 'reason' => 'spreadsheet_package_not_installed'],
                    'pdf' => ['supported' => false, 'reason' => 'pdf_package_not_installed'],
                ],
                'async_export_supported' => false,
                'download_tracking_supported' => false,
            ],
        ];
    }
}
