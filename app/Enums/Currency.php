<?php

declare(strict_types=1);

namespace App\Enums;

enum Currency: string
{
    case USD = 'USD';
    case EUR = 'EUR';
    case RUB = 'RUB';

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
