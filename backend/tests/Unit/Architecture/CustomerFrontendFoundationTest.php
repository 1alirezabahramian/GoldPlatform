<?php

namespace Tests\Unit\Architecture;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CustomerFrontendFoundationTest extends TestCase
{
    #[Test]
    public function framework_neutral_customer_foundation_files_exist(): void
    {
        self::assertFileExists(base_path('../frontend/customer-foundation/tokens.css'));
        self::assertFileExists(base_path('../frontend/customer-foundation/terminology.fa.json'));
        self::assertFileExists(base_path('../frontend/customer-foundation/README.md'));
    }

    #[Test]
    public function design_tokens_keep_rtl_accessibility_and_white_label_hooks(): void
    {
        $tokens = (string) file_get_contents(base_path('../frontend/customer-foundation/tokens.css'));

        foreach ([
            '--gp-direction: rtl',
            '--gp-font-family:',
            '--gp-color-brand:',
            '--gp-touch-target-min:',
            ':focus-visible',
        ] as $requiredToken) {
            self::assertStringContainsString($requiredToken, $tokens);
        }
    }

    #[Test]
    public function customer_terminology_uses_human_domain_language(): void
    {
        $contents = (string) file_get_contents(base_path('../frontend/customer-foundation/terminology.fa.json'));
        $terminology = json_decode($contents, true, flags: JSON_THROW_ON_ERROR);

        self::assertSame('دارایی‌های من', $terminology['navigation']['assets']);
        self::assertSame('پول من', $terminology['assets']['money']);
        self::assertSame('طلای من', $terminology['assets']['gold']);
        self::assertSame('امانات من', $terminology['navigation']['custodies']);
        self::assertSame('درخواست تحویل', $terminology['actions']['request_delivery']);

        $customerFacing = json_encode([
            $terminology['navigation'],
            $terminology['assets'],
            $terminology['actions'],
            $terminology['states'],
        ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);

        foreach ($terminology['forbidden_primary_terms'] as $forbiddenTerm) {
            self::assertStringNotContainsString($forbiddenTerm, $customerFacing);
        }
    }
}
