<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BalanceStatus;
use App\ValueObjects\Amount;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property string $status
 * @property string $currency
 * @property App\ValueObjects\Amount $amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @mixin \Eloquent
 */
class Balance extends Model
{
    protected $fillable = [
        'currency',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::creating(function (self $balance) {
            $balance->status = BalanceStatus::ACTIVE->value;
            $balance->amount = 0;
            $balance->updated_at = null;
        });
    }

    protected function amount(): Attribute
    {
        return Attribute::make(
            get: fn($value) => new Amount($value),
            set: fn(Amount|string $value) => (string) $value,
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'from_balance_id', 'id')
            ->orWhere('to_balance_id', $this->id);
    }

    public function outgoingTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'from_balance_id', 'id');
    }

    public function incomingTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'to_balance_id', 'id');
    }
}
