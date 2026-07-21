<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * آیا کاربر مجاز به ارسال درخواست است؟
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * قوانین اعتبارسنجی
     */
    public function rules(): array
    {
        return [

            'mobile' => [
                'required',
                'string',
                'size:11',
                'regex:/^09[0-9]{9}$/',
                'unique:users,mobile',
            ],

            'first_name' => [
                'nullable',
                'string',
                'min:2',
                'max:100',
            ],

            'last_name' => [
                'nullable',
                'string',
                'min:2',
                'max:100',
            ],

            'national_code' => [
                'nullable',
                'digits:10',
                'unique:users,national_code',
            ],

            'password' => [
                'required',
                'string',
                'min:6',
                'max:100',
            ],

        ];
    }

    /**
     * پیام‌های فارسی
     */
    public function messages(): array
    {
        return [

            'mobile.required' => 'شماره موبایل الزامی است.',
            'mobile.size' => 'شماره موبایل باید ۱۱ رقم باشد.',
            'mobile.regex' => 'فرمت شماره موبایل صحیح نیست.',
            'mobile.unique' => 'این شماره موبایل قبلاً ثبت شده است.',

            'first_name.min' => 'نام باید حداقل ۲ کاراکتر باشد.',
            'last_name.min' => 'نام خانوادگی باید حداقل ۲ کاراکتر باشد.',

            'national_code.digits' => 'کد ملی باید ۱۰ رقم باشد.',
            'national_code.unique' => 'این کد ملی قبلاً ثبت شده است.',

            'password.required' => 'رمز عبور الزامی است.',
            'password.min' => 'رمز عبور باید حداقل ۶ کاراکتر باشد.',
        ];
    }

    /**
     * نام فارسی فیلدها
     */
    public function attributes(): array
    {
        return [

            'mobile' => 'شماره موبایل',
            'first_name' => 'نام',
            'last_name' => 'نام خانوادگی',
            'national_code' => 'کد ملی',
            'password' => 'رمز عبور',

        ];
    }
}