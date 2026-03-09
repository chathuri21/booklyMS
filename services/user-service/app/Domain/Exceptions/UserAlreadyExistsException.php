<?php

namespace App\Domain\Exceptions;

use Exception;

class UserAlreadyExistsException extends Exception
{
    protected $message = 'A user with this email already exists.';
    protected $code = 409; // HTTP status code for Conflict
}