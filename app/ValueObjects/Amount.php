<?php

declare(strict_types=1);

namespace App\ValueObjects;

/**
 * Value Object for safe money operations (up to 4 decimal places).
 */
final class Amount
{
    private string $value;

    public function __construct(string|float|int $value)
    {
        $numeric = (string)$value;
        if (bccomp($numeric, '0', 4) < 0 || bccomp($numeric, '999999999', 4) > 0) {
            throw new \InvalidArgumentException('Amount must be between 0 and 999999999');
        }

        $this->value = bcadd((string)$value, '0', 4);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function add(self $other): self
    {
        return new self(bcadd($this->value, $other->value, 4));
    }

    public function sub(self $other): self
    {
        return new self(bcsub($this->value, $other->value, 4));
    }

    public function cmp(self $other): int
    {
        return bccomp($this->value, $other->value, 4);
    }

    public function isZero(): bool
    {
        return $this->cmp(new self('0')) === 0;
    }

    public function toFormattedString(): string
    {
        // Format to 2 decimal places for display
        return substr($this->value, 0, -2);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
