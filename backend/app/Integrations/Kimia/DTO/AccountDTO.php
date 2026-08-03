<?php

namespace App\Integrations\Kimia\DTO;

class AccountDTO
{
    /**
     * @param array<string, mixed> $rawData
     */
    public function __construct(
        public readonly int $id,
        public readonly ?int $code,
        public readonly string $name,
        public readonly int $type,
        public readonly ?string $mobile = null,
        public readonly ?string $nationalCode = null,
        public readonly bool $isVisible = true,
        public readonly array $rawData = [],
    ) {
    }
}
