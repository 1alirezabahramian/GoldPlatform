<?php

namespace App\Services;

class OtpService
{
    public function generate(int $length = 6): string
    {
        $min = (int) pow(10, $length - 1);
        $max = (int) pow(10, $length) - 1;

        return (string) random_int($min, $max);
    }

    public function expiresAt(): \DateTime
    {
        return new \DateTime('+2 minutes');
    }

    public function verify(string $code, string $input): bool
    {
        return hash_equals($code, $input);
    }
}