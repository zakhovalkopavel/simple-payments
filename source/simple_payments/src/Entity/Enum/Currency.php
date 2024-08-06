<?php

namespace App\Entity\Enum;

use App\Entity\Enum\EnumValidate;

use App\Entity\Enum\ValidateEnumTrait;

enum Currency: string implements EnumValidate {
    use ValidateEnumTrait;
    case USD = 'USD';
    case EUR = 'EUR';
    case CHF = 'CHF';
}