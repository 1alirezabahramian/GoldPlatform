<?php

namespace App\ReadModels;

use App\Models\OutboxMessage;
use Illuminate\Support\Facades\Schema;

final class AdminNotificationReadModel
{
    public function overview(): array
    {
        $smsBaseUrlConfigured = filled(config('services.smsir.base_url'));
        $smsApiKeyConfigured = filled(config('services.smsir.api_key'));
        $smsLoginTemplateConfigured = filled(config('services.smsir.templates.login'));
        $mailDriver = (string) config('mail.default', 'log');

        $outbox = [
            'supported' => Schema::hasTable('outbox_messages'),
            'total' => null,
            'pending' => null,
        ];

        if ($outbox['supported']) {
            $outbox['total'] = OutboxMessage::query()->count();
            $outbox['pending'] = OutboxMessage::query()->whereNull('processed_at')->count();
        }

        return [
            'channels' => [
                'in_app' => [
                    'supported' => false,
                    'template_store_supported' => false,
                ],
                'sms' => [
                    'provider' => 'sms.ir',
                    'configured' => $smsBaseUrlConfigured && $smsApiKeyConfigured,
                    'login_template_configured' => $smsLoginTemplateConfigured,
                    'template_store_supported' => false,
                ],
                'email' => [
                    'configured_driver' => $mailDriver,
                    'delivery_configured' => $mailDriver !== 'log' && $mailDriver !== 'array',
                    'template_store_supported' => false,
                ],
                'telegram' => [
                    'supported' => false,
                    'bot_configured' => false,
                    'customer_notifications_supported' => false,
                    'admin_operator_alerts_supported' => false,
                ],
                'push' => [
                    'supported' => false,
                    'provider_configured' => false,
                ],
            ],
            'outbox' => $outbox,
            'capabilities' => [
                'notification_center_supported' => false,
                'channel_preferences_supported' => false,
                'delivery_log_supported' => false,
                'retry_management_supported' => false,
                'telegram_admin_companion_supported' => false,
            ],
        ];
    }
}
