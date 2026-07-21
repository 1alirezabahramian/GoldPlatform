<?php

namespace App\Services\Sms\DTO;

class SmsResult
{
    public function __construct(
        public bool $success,
        public ?string $message = null,
        public ?string $providerId = null,
        public ?array $response = null,
    ) {
    }
}