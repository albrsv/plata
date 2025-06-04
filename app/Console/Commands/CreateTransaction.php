<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\Currency;
use App\Enums\TransactionType;
use App\Models\User;
use App\Services\TransactionService;
use App\ValueObjects\Amount;
use App\ValueObjects\Email;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-transaction {email?} {type?} {currency?} {amount?} {comment?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transaction creation command';

    private ?User $user;
    private Currency $currency;
    private TransactionType $type;
    private Amount $amount;
    private ?string $comment = null;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->promptArguments();

        TransactionService::createTransaction(
            $this->user,
            $this->currency,
            $this->type,
            $this->amount,
            $this->comment,
        );

        $this->info('Transaction created and sent to the queue successfully');
        return self::SUCCESS;
    }

    /**
     * Prompt for arguments and validate them.
     */
    private function promptArguments(): void
    {
        // Prompt for email
        $this->askAndValidate('email', true, function ($value) {
            try {
                $email = new Email($value);
            } catch (\InvalidArgumentException $e) {
                $this->error($e->getMessage());
                return false;
            }

            $this->user = User::where('email', $email->value)->first();
            if (!$this->user) {
                $this->error('User with this email does not exist');
                return false;
            }
            $this->input->setArgument('email', $email->value);
            return true;
        });

        // Prompt for type
        $this->askAndValidate('type', true, function ($value) {
            $type = TransactionType::tryFrom(strtolower($value));
            if (!$type) {
                $this->error('Invalid type. Supported types: ' . implode(', ', TransactionType::values()));
                return false;
            }
            $this->type = $type;
            return true;
        });

        // Prompt for currency
        $this->askAndValidate('currency', true, function ($value) {
            $currency = Currency::tryFrom(strtoupper($value));
            if (!$currency) {
                $this->error('Invalid currency. Supported currencies: ' . implode(', ', Currency::values()));
                return false;
            }
            $this->currency = $currency;
            return true;
        });

        // Prompt for amount
        $this->askAndValidate('amount', true, function ($value) {
            try {
                $this->amount = new Amount($value);
                return true;
            } catch (\InvalidArgumentException $e) {
                $this->error($e->getMessage());
                return false;
            }
        });

        // Prompt for comment
        $this->askAndValidate('comment', false, function ($value) {
            $this->comment = $value;
            if (!empty($value) && Str::length($value) > 255) {
                $this->error('Comment must not exceed 255 characters');
                return false;
            }
            return true;
        });
    }

    /**
     * Ask for an argument and validate it using a callback.
     */
    private function askAndValidate(string $argument, bool $required, callable $validator): void
    {
        do {
            $value = $this->ask("Enter {$argument}" . ($required ? ' (required)' : ' (optional)'), $this->argument($argument));
        } while (!$validator($value));

        $this->input->setArgument($argument, $value);
    }
}
