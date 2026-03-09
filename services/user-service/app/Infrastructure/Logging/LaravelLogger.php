<?php

namespace App\Infrastructure\Logging;

use Illuminate\Support\Facades\Log;

class LaravelLogger implements \App\Domain\Services\LoggerInterface
{
    public function info(string $message, array $context = []): void
    {
        Log::info($message, $context);
    }
}