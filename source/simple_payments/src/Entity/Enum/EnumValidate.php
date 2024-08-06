<?php

namespace App\Entity\Enum;

interface EnumValidate
{
    public static function validate(string $item): bool;
}