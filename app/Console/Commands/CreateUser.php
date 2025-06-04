<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use App\ValueObjects\Email;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-user {name?} {email?} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user with name, email, and password';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->promptArguments();

        $user = User::create([
            'name' => $this->argument('name'),
            'email' => $this->argument('email'),
            'password' => Hash::make($this->argument('password')),
        ]);

        $this->info("User {$user->email} created successfully.");
        return self::SUCCESS;
    }

    /**
     * Prompt for arguments and validate them.
     */
    private function promptArguments(): void
    {
        // Prompt for name
        $this->askAndValidate('name', function ($value) {
            if (empty($value)) {
                $this->error('Name is required.');
                return false;
            }
            return true;
        });

        // Prompt for email
        $this->askAndValidate('email', function ($value) {
            try {
                $email = new Email($value);
            } catch (\InvalidArgumentException $e) {
                $this->error($e->getMessage());
                return false;
            }
            if (User::where('email', $email->value)->exists()) {
                $this->error('User with this email already exists');
                return false;
            }
            $this->input->setArgument('email', $email->value);
            return true;
        });

        // Prompt for password
        $this->askAndValidate('password', function ($value) {
            if (empty($value) || strlen($value) < 6) {
                $this->error('Password must be at least 6 characters.');
                return false;
            }
            return true;
        });
    }

    /**
     * Ask for an argument and validate it using a callback.
     */
    private function askAndValidate(string $argument, callable $validator): void
    {
        do {
            if ($argument === 'password') {
                $value = $this->secret('Enter password (required)');
            } else {
                $value = $this->ask("Enter {$argument} (required)", $this->argument($argument));
            }
        } while (!$validator($value));

        $this->input->setArgument($argument, $value);
    }
}
