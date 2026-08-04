<?php

namespace App\Exceptions;

use RuntimeException;

class KimiaReadException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly ?int $status = null,
        public readonly ?string $endpoint = null,
    ) {
        parent::__construct($message);
    }
}
