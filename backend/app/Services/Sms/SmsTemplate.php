<?php

namespace App\Services\Sms;

enum SmsTemplate:string
{
    case LoginOTP = 'login_otp';

    case RegisterOTP = 'register_otp';

    case PasswordRecovery = 'password_recovery';

    case VerifyMobile = 'verify_mobile';

    case OrderCreated = 'order_created';

    case SellCreated = 'sell_created';

    case WalletDeposit = 'wallet_deposit';

    case WalletWithdraw = 'wallet_withdraw';

    case IdentityVerified = 'identity_verified';

    case GeneralNotification = 'general_notification';
}