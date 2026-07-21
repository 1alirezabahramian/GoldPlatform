<?php

namespace App\Services\Sms\Providers;

use App\Services\Sms\Contracts\SmsProvider;
use App\Services\Sms\DTO\SmsResult;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsIrProvider implements SmsProvider
{
    public function sendVerify(
        string $mobile,
        string $template,
        array $parameters
    ): SmsResult {

        try {

            $templateId = config("services.smsir.templates.login");

            $payload = [

                "mobile" => $mobile,

                "templateId" => (int) $templateId,

                "parameters" => collect($parameters)
                    ->map(function ($value, $key) {

                        return [

                            "name" => $key,

                            "value" => $value,

                        ];

                    })
                    ->values()
                    ->toArray(),

            ];

            $response = Http::withToken(
                    config('services.smsir.api_key')
                )
                ->acceptJson()
                ->post(
                    config('services.smsir.base_url').'/send/verify',
                    $payload
                );

            if ($response->successful()) {

                return new SmsResult(

                    success: true,

                    message: 'SMS Sent',

                    providerId: data_get($response->json(),'data.messageId'),

                    response: $response->json(),

                );

            }

            Log::error('SMS.ir Error', [

                'status' => $response->status(),

                'body' => $response->json(),

            ]);

            return new SmsResult(

                success:false,

                message:data_get($response->json(),'message','SMS Error'),

                response:$response->json(),

            );

        } catch (\Throwable $e) {

            Log::error('SMS.ir Exception', [

                'message'=>$e->getMessage(),

            ]);

            return new SmsResult(

                success:false,

                message:$e->getMessage(),

            );

        }

    }
}