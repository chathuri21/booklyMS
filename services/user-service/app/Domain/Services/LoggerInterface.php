<?php

namespace App\Domain\Services;

interface LoggerInterface
{
    public function info(string $message, array $context = []): void;
}   