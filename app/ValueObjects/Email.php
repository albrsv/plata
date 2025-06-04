<?php

declare(strict_types=1);

namespace App\ValueObjects;

final class Email
{
    public readonly string $value;

    public function __construct(string $value)
    {
        $email = trim($value);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email address.');
        }

        $this->value = strtolower($email);
    }

    public function equals(Email $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
