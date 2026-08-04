<?php

namespace Tests\Unit\Financial;

use App\Domain\Financial\ValueObjects\FinancialDocumentId;
use InvalidArgumentException;
use Tests\TestCase;

final class FinancialIdentifierStrategyTest extends TestCase
{
    public function test_financial_document_identifier_is_a_uuid(): void
    {
        $identifier = FinancialDocumentId::generate();

        self::assertTrue((bool) preg_match(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $identifier->value(),
        ));
    }

    public function test_identifier_can_be_rehydrated_without_changing_its_value(): void
    {
        $original = FinancialDocumentId::generate();
        $rehydrated = FinancialDocumentId::fromString($original->value());

        self::assertTrue($original->equals($rehydrated));
    }

    public function test_invalid_identifier_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        FinancialDocumentId::fromString('123');
    }
}
