<?php

namespace App\Domain\Trading\Validation;

use InvalidArgumentException;

final readonly class ValidationFailure
{
    public function __construct(
        private string $code,
        private string $message,
    ) {
        if (trim($code) === '' || trim($message) === '') {
            throw new InvalidArgumentException('Validation failure code and message are required.');
        }
    }

    public function code(): string
    {
        return $this->code;
    }

    public function message(): string
    {
        return $this->message;
    }
}
