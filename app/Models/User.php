<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Currency;
use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $email_verified_at
 * @property string $status
 * @property string $password
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'id',
        'status',
        'email_verified_at',
        'password',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::creating(function (self $user) {
            $user->status = UserStatus::ACTIVE->value;
            $user->updated_at = null;
        });

        static::created(function (self $user) {
            foreach (Currency::cases() as $currency) {
                $user->balances()->create(['currency' => $currency->value]);
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function balances(): HasMany
    {
        return $this->hasMany(Balance::class);
    }

    public function getBalance(Currency $currency): Balance
    {
        return $this->balances()->where('currency', $currency->value)->firstOrFail();
    }

    public function outgoingTransactions(): HasManyThrough
    {
        return $this->hasManyThrough(
            Transaction::class,
            Balance::class,
            'user_id',
            'from_balance_id'
        )->select([
            'transactions.*',
            'balances.user_id',
            'balances.user_id as laravel_through_key',
        ]);
    }

    public function incomingTransactions(): HasManyThrough
    {
        return $this->hasManyThrough(
            Transaction::class,
            Balance::class,
            'user_id',
            'to_balance_id'
        )->select([
            'transactions.*',
            'balances.user_id',
            'balances.user_id as laravel_through_key',
        ]);
    }

    public function transactions(): HasManyThrough
    {
        return $this->outgoingTransactions()
            ->union($this->incomingTransactions());
    }
}
