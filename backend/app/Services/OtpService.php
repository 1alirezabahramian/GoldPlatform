<?php

namespace App\Services\Sms;

use App\Models\Otp;
use Carbon\Carbon;
use Illuminate\Support\Str;

class OtpService
{
    const EXPIRE_MINUTES = 2;

    const MAX_ATTEMPTS = 5;

    public function generate(): string
    {
        return (string) random_int(100000,999999);
    }

    public function create(string $mobile): Otp
    {
        Otp::where('mobile',$mobile)
            ->where('verified',false)
            ->delete();

        return Otp::create([

            'mobile' => $mobile,

            'otp' => $this->generate(),

            'purpose' => 'login',

            'attempts' => 0,

            'verified' => false,

            'expires_at' => now()->addMinutes(self::EXPIRE_MINUTES),

            'ip_address' => request()->ip(),

            'user_agent' => Str::limit(request()->userAgent(),255),

        ]);
    }

    public function isExpired(Otp $otp): bool
    {
        return Carbon::now()->greaterThan($otp->expires_at);
    }

    public function increaseAttempts(Otp $otp): void
    {
        $otp->increment('attempts');
    }

    public function isBlocked(Otp $otp): bool
    {
        return $otp->attempts >= self::MAX_ATTEMPTS;
    }

    public function verify(Otp $otp,string $code): bool
    {
        if($this->isExpired($otp))
            return false;

        if($this->isBlocked($otp))
            return false;

        if($otp->otp !== $code){

            $this->increaseAttempts($otp);

            return false;
        }

        $otp->update([

            'verified'=>true,

            'verified_at'=>now(),

        ]);

        return true;
    }
}