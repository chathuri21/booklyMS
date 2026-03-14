<?php

namespace App\Events;

use App\Domain\Entities\User;

class UserCreated
{
    public function __construct(
        public User $user
    ) {}
}