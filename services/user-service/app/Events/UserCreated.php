<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserCreated
{
    public function __construct(public User $user) 
    {
        Log::info('UserCreated event triggered');
    }
}