<?php

namespace Tests\Unit\Kimia;

use App\Integrations\Kimia\Exceptions\KimiaException;
use App\Integrations\Kimia\Write\KimiaWritePreparationService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class KimiaWritePreparationServiceTest extends TestCase
{
    #[Test]
    public function write_preparation_is_disabled_by_default(): void
    {
        config()->set('kimia_write.enabled', false);

        $this->expectException(KimiaException::class);
        $this->expectExceptionMessage('Kimia write preparation is disabled.');

        app(KimiaWritePreparationService::class)->prepare('unknown', 'key-1', []);
    }

    #[Test]
    public function unapproved_operations_are_rejected_without_guessing(): void
    {
        config()->set('kimia_write.enabled', true);
        config()->set('kimia_write.operations', []);

        $this->expectException(KimiaException::class);
        $this->expectExceptionMessage('Kimia write operation is not approved.');

        app(KimiaWritePreparationService::class)->prepare('paper_gold_buy', 'key-1', []);
    }

    #[Test]
    public function an_explicit_test_definition_produces_a_stable_auditable_plan(): void
    {
        config()->set('kimia_write.enabled', true);
        config()->set('kimia_write.operations.test_only', [
            'approved' => true,
            'method' => 'POST',
            'uri' => '/api/test-only',
            'required_fields' => ['AccountId', 'Amount'],
            'compensation_operation' => 'test_only_reverse',
        ]);

        $service = app(KimiaWritePreparationService::class);
        $first = $service->prepare('test_only', 'idem-100', [
            'Amount' => '100.00',
            'AccountId' => 350,
        ]);
        $second = $service->prepare('test_only', 'idem-100', [
            'AccountId' => 350,
            'Amount' => '100.00',
        ]);

        $this->assertSame($first->payloadHash, $second->payloadHash);
        $this->assertSame('idem-100', $first->idempotencyKey);
        $this->assertSame('test_only_reverse', $first->compensationOperation);
        $this->assertArrayNotHasKey('payload', $first->auditContext());
    }

    #[Test]
    public function approved_required_fields_are_enforced(): void
    {
        config()->set('kimia_write.enabled', true);
        config()->set('kimia_write.operations.test_only', [
            'approved' => true,
            'method' => 'POST',
            'uri' => '/api/test-only',
            'required_fields' => ['AccountId'],
        ]);

        $this->expectException(KimiaException::class);
        $this->expectExceptionMessage('Kimia write payload is missing approved required fields.');

        app(KimiaWritePreparationService::class)->prepare('test_only', 'idem-101', []);
    }
}
