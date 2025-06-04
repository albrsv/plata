<?php

declare(strict_types=1);

namespace App\Enums;

enum BalanceStatus: string
{
    case ACTIVE = 'active';
    case FROZEN = 'frozen';
    case BLOCKED = 'blocked';

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
