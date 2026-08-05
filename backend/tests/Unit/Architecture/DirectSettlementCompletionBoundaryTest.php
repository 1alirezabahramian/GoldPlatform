<?php

namespace Tests\Unit\Architecture;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DirectSettlementCompletionBoundaryTest extends TestCase
{
    #[Test]
    public function direct_completion_requires_verified_kimia_result_evidence(): void
    {
        $source = file_get_contents(
            dirname(__DIR__, 3).'/app/Services/Settlement/SettlementService.php'
        );

        self::assertIsString($source);
        self::assertStringContainsString(
            'A reference string alone is not sufficient evidence.',
            $source
        );
        self::assertStringNotContainsString(
            "'status' => SettlementStatus::Completed",
            $this->completeMethod($source)
        );
    }

    private function completeMethod(string $source): string
    {
        $start = strpos($source, 'public function complete(');
        $end = strpos($source, 'public function fail(', $start ?: 0);

        self::assertNotFalse($start);
        self::assertNotFalse($end);

        return substr($source, $start, $end - $start);
    }
}
