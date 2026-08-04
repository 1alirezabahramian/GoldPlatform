<?php

namespace Tests\Unit\Financial;

use App\Domain\Financial\ValueObjects\FinancialScope;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FinancialScopeTest extends TestCase
{
    #[Test]
    public function every_financial_scope_requires_a_tenant(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new FinancialScope('');
    }

    #[Test]
    public function branch_scope_requires_company_scope(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new FinancialScope('tenant-1', null, 'branch-1');
    }

    #[Test]
    public function it_builds_a_stable_scope_key(): void
    {
        $scope = new FinancialScope('tenant-1', 'company-1', 'branch-1');

        self::assertSame(
            'tenant:tenant-1:company:company-1:branch:branch-1',
            $scope->key(),
        );
        self::assertTrue($scope->belongsToTenant('tenant-1'));
        self::assertFalse($scope->belongsToTenant('tenant-2'));
    }
}
