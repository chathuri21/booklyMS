<?php

namespace App\Infrastructure\Logging;

use \App\Domain\Services\LoggerInterface;
use Illuminate\Support\Facades\Log;

class LaravelLogger implements LoggerInterface
{
    public function info(string $message, array $context = []): void
    {
        Log::info($message, $context);
    }
}