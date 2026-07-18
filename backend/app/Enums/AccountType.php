<?php

namespace App\Enums;

enum AccountType:int
{
    case Wholesale = 1;

    case Retail = 3;

    case Capital = 5;

    case Bank = 6;

    case Internal = 8;

    case Melt = 9;

    case Amanat = 10;

    case Expense = 11;

    case Employee = 12;

    public function label(): string
    {
        return match ($this) {

            self::Wholesale => 'بنکداری',

            self::Retail => 'تک فروشی',

            self::Capital => 'سرمایه و برداشت',

            self::Bank => 'بانک',

            self::Internal => 'حساب داخلی',

            self::Melt => 'ذوب',

            self::Amanat => 'امانات',

            self::Expense => 'هزینه',

            self::Employee => 'کارمندان',

        };
    }
}