<?php

namespace App\Entity\Enum;

trait ValidateEnumTrait
{
    public static function validate(string $item): bool
    {
        if (!self::tryFrom($item) instanceof self) {
            throw new ValidatorException($item . ' is not a valid backing value for enum ' . self::class);
        }

        return true;
    }
}