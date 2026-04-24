<?php

namespace App\Messaging\Handlers;

use App\Models\UserSnapshot;
use Illuminate\Support\Facades\Log;

class UserCreatedHandler
{
    public function handle(array $eventData): void
    {
        UserSnapshot::updateOrCreate(
            ['user_id' => $eventData['id']],
            [
                'name' => $eventData['name'],
                'email' => $eventData['email'],
                'role' => $eventData['role'],
                'is_active' => $eventData['is_active'] ?? true,
            ]
        );

        Log::info('User snapshot created for user_id: ' . $eventData['id']);
    }
}