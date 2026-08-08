<?php

namespace Tests\Unit\Kimia;

use App\Services\Kimia\RialTomanConverter;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RialTomanConverterTest extends TestCase
{
    #[DataProvider('conversionCases')]
    public function test_it_converts_rial_to_toman_without_float(string $rial, string $expected): void
    {
        $converter = new RialTomanConverter();

        $this->assertSame($expected, $converter->toToman($rial));
    }

    public function test_it_rejects_non_decimal_input(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new RialTomanConverter())->toToman('1e3');
    }

    /** @return array<string, array{string, string}> */
    public static function conversionCases(): array
    {
        return [
            'positive integer' => ['1870000000', '187000000'],
            'negative real kimia shape' => ['-2999219914', '-299921991.4'],
            'sub toman' => ['1', '0.1'],
            'zero' => ['0', '0'],
            'decimal input' => ['10.5', '1.05'],
        ];
    }
}
