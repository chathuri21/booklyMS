<?php

namespace App\Events;

use App\Models\User;
use App\Domain\Services\LoggerInterface;

class UserCreated
{
    public function __construct(
        public User $user,
        private LoggerInterface $logger
    ) 
    {
        $this->logger->info('UserCreated event triggered');
    }
}