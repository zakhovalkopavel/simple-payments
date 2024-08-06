<?php

namespace App\Entity\Enum;

use App\Entity\Enum\EnumValidate;

use App\Entity\Enum\ValidateEnumTrait;

enum PaymentType: string implements EnumValidate {
    use ValidateEnumTrait;
    case SHIFT4 = 'SHIFT4';
    case ACI = 'ACI';
    public function toString(): string {
        return $this->value;
    }
}