<?php

namespace App\Enums;

enum UserStatus: string
{
    /**
     * ثبت نام اولیه
     */
    case Pending = 'pending';

    /**
     * شماره موبایل تایید شده
     */
    case OtpVerified = 'otp_verified';

    /**
     * تایید شده توسط ادمین
     */
    case Approved = 'approved';

    /**
     * مسدود شده
     */
    case Blocked = 'blocked';
}