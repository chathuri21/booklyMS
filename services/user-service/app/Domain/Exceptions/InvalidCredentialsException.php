<?php

namespace App\Domain\Exceptions;

use Exception;

class InvalidCredentialsException extends Exception
{
    protected $message = 'Invalid credentials provided';
    protected $code = 401;
}