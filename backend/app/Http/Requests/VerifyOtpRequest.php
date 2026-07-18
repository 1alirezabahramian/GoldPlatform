<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mobile' => [
                'required',
                'string',
                'regex:/^09[0-9]{9}$/',
            ],
            'otp' => [
                'required',
                'digits:6',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'mobile.required' => 'شماره موبایل الزامی است.',
            'mobile.regex' => 'شماره موبایل معتبر نیست.',
            'otp.required' => 'کد تایید الزامی است.',
            'otp.digits' => 'کد تایید باید ۶ رقم باشد.',
        ];
    }
}