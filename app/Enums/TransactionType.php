<?php

declare(strict_types=1);

namespace App\Enums;

enum TransactionType: string
{
    case DEPOSIT = 'deposit';
    case TRANSFER = 'transfer';
    case CONVERSION = 'conversion';
    case WITHDRAW = 'withdraw';
    case REFUND = 'refund';
    case FEE = 'fee';

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
