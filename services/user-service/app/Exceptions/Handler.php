<?php

namespace App\Exceptions;

use App\Domain\Exceptions\InactiveAccountException;
use App\Domain\Exceptions\InvalidCredentialsException;
use App\Domain\Exceptions\UserAlreadyExistsException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e)
    {
        if ($e instanceof UserAlreadyExistsException ) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 409);
        }

        if ($e instanceof InvalidCredentialsException ) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 401);
        }

        if ($e instanceof InactiveAccountException ) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 403);
        }

        return parent::render($request, $e);
    }
}
