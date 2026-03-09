<?php

namespace App\Domain\Exceptions;

use Exception;

class InactiveAccountException extends Exception
{
    protected $message = 'User account is inactive';
    protected $code = 403;
}