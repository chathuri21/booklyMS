<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SyncUser
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserCreated $event): void
    {
        User::updateOrCreate(
            ['id' => $event->id],
            [
                'name' => $event->name,
                'email' => $event->email,
                'role' => $event->role
            ]
        );
    }
}
