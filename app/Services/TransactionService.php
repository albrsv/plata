<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Currency;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Exceptions\InsufficientFundsException;
use App\Jobs\ProcessTransaction;
use App\Models\Balance;
use App\Models\Transaction;
use App\Models\User;
use App\ValueObjects\Amount;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public static function createTransaction(User $user, Currency $currency, TransactionType $type, Amount $amount, ?string $comment = null): void
    {
        $balance = $user
            ->balances()
            ->where('currency', $currency->value)
            ->firstOrFail();

        $transactionData = [
            'type'    => $type->value,
            'status'  => TransactionStatus::PENDING->value,
            'amount'  => $amount,
            'comment' => $comment ?: null,
        ];

        // Create the transaction based on its type
        $transaction = match ($type) {
            TransactionType::WITHDRAW => $balance->outgoingTransactions()->create($transactionData),
            TransactionType::DEPOSIT => $balance->incomingTransactions()->create($transactionData),
            default => throw new \InvalidArgumentException('Invalid transaction type'),
        };

        // Dispatch the job to process the transaction
        ProcessTransaction::dispatch($transaction->id);
    }

    public function processTransaction(int $transactionId): void
    {
        DB::transaction(function () use ($transactionId) {
            // Find and lock the Transaction and Balance for update

            try {
                $transaction = Transaction::where('id', $transactionId)
                    ->where('status', TransactionStatus::PENDING->value)
                    ->lockForUpdate()
                    ->first();

                if (!$transaction) {
                    throw new \RuntimeException('Transaction not found');
                }

                $balance = $transaction
                    ->balanceByTransactionType($transaction)
                    ->lockForUpdate()
                    ->first();

                if (!$balance) {
                    throw new \RuntimeException('Balance not found for transaction');
                }

                // Handle the transaction based on its type
                match ($transaction->type) {
                    TransactionType::WITHDRAW->value => $this->handleWithdraw($balance, $transaction->amount),
                    TransactionType::DEPOSIT->value => $this->handleDeposit($balance, $transaction->amount),
                    default => throw new \InvalidArgumentException('Invalid transaction type'),
                };

                // Save the updated balance
                $balance->save();
                // Update the transaction status to completed
                $transaction->status = TransactionStatus::COMPLETED->value;
                $transaction->save();
            } catch (\Throwable $e) {
                if (isset($transaction)) {
                    $transaction->status = TransactionStatus::FAILED->value;
                    $transaction->save();
                }

                logger()->error('Transaction processing failed', [
                    'transaction_id' => $transactionId,
                    'error'          => $e->getMessage(),
                ]);
            }
        });
    }

    private function handleWithdraw(Balance $balance, Amount $withdrawAmount): void
    {
        if ($balance->amount->cmp($withdrawAmount) < 0) {
            throw new InsufficientFundsException();
        }
        $balance->amount = $balance->amount->sub($withdrawAmount);
    }

    private function handleDeposit(Balance $balance, Amount $depositAmount): void
    {
        $balance->amount = $balance->amount->add($depositAmount);
    }
}
