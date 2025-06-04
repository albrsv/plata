<?php

namespace App\Exceptions;

class InsufficientFundsException extends \RuntimeException
{
    protected $message = 'Insufficient funds';
}
