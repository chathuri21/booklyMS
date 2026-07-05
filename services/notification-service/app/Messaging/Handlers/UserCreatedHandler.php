<?php

namespace App\Messaging\Handlers;

use App\Models\UserSnapshot;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
     *     "is_active": true
     *   }
     * }
     */
    public function handle(array $eventData): void
    {
        // Keep a local copy of the user so future notifications (e.g. appointment
        // reminders) can resolve contact details without calling user-service
        UserSnapshot::updateOrCreate(
            ['user_id' => $eventData['id']],
            [
                'name' => $eventData['name'],
                'email' => $eventData['email'],
                'phone' => $eventData['phone'] ?? null,
                'role' => $eventData['role'],
                'is_active' => $eventData['is_active'] ?? true,
            ]
        );

        Log::info('User snapshot stored for user_id: ' . $eventData['id']);

        Mail::raw(
            "Hi {$eventData['name']},\n\nWelcome to Bookly! Your account has been created successfully.",
            function ($message) use ($eventData) {
                $message->to($eventData['email'], $eventData['name'])
                    ->subject('Welcome to Bookly');
            }
        );

        Log::info('Welcome email sent for user_id: ' . $eventData['id']);
    }
}
