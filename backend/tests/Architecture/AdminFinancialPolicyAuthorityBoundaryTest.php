<?php

namespace Tests\Architecture;

use Tests\TestCase;

class AdminFinancialPolicyAuthorityBoundaryTest extends TestCase
{
    public function test_admin_cannot_change_financial_policy_without_verified_ground_truth(): void
    {
        $source = file_get_contents(app_path('Http/Controllers/Api/AdminPanelController.php'));

        $this->assertIsString($source);
        $this->assertStringContainsString('FINANCIAL_POLICY_GROUND_TRUTH_REQUIRED', $source);
        $this->assertStringContainsString('503', $source);
        $this->assertStringNotContainsString("'allow_negative_balance' => ['sometimes','boolean']", $source);
        $this->assertStringNotContainsString("'credit_limit' => ['nullable','numeric']", $source);
        $this->assertStringNotContainsString('$policy->fill($data)->save();', $source);
    }
}
