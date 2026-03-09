<?php

namespace App\Exceptions;

use App\Domain\Exceptions\UserAlreadyExistsException;
use Exception;
use Throwable;

class Handler extends Exception
{
    public function render($request, Throwable $e)
    {
        if ($e instanceof UserAlreadyExistsException) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 409);
        }
        
        return response()->json([
            'message' => $this->getMessage(),
        ], 500);
    }
}
