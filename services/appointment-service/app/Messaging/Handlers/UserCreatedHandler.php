<?php

namespace App\Messaging\Handlers;

use App\Models\UserSnapshot;
use Illuminate\Support\Facades\Log;

class UserCreatedHandler
{
    /**
     * Handle the user.created event.
     *
     * Expected payload shape:
     * {
     *   "event":      "user.created",
     *   "data": {
     *     "id":    1,
     *     "name":  "Jane Doe",
     *     "email": "jane@example.com",
     *     "phone": "+49123456789",
     *     "role": "customer",
     *   "is_active": true
     *   }
     * }
     */
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