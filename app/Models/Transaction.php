<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TransactionType;
use App\ValueObjects\Amount;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $from_balance_id
 * @property int $to_balance_id
 * @property string $type
 * @property App\ValueObjects\Amount $amount
 * @property string $status
 * @property string|null $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Transaction extends Model
{
    protected $fillable = [
        'from_balance_id',
        'to_balance_id',
        'type',
        'status',
        'amount',
        'comment',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::creating(function (self $transaction) {
            $transaction->updated_at = null;
        });
    }

    protected function amount(): Attribute
    {
        return Attribute::make(
            get: fn($value) => new Amount($value),
            set: fn(Amount|string $value) => (string) $value,
        );
    }

    public function fromBalance(): BelongsTo
    {
        return $this->belongsTo(Balance::class, 'from_balance_id');
    }

    public function toBalance(): BelongsTo
    {
        return $this->belongsTo(Balance::class, 'to_balance_id');
    }

    public function balanceByTransactionType(Transaction $transaction): BelongsTo
    {
        return match ($transaction->type) {
            TransactionType::WITHDRAW->value => $transaction->fromBalance(),
            TransactionType::DEPOSIT->value => $transaction->toBalance(),
            default => throw new \InvalidArgumentException('Invalid transaction type'),
        };
    }
}
