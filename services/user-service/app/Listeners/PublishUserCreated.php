<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Domain\Services\LoggerInterface;
use Illuminate\Support\Facades\Queue;

class PublishUserCreated
{
    public function __construct(private LoggerInterface $logger)
    {
        $this->logger->info('PublishUserCreated listener instantiated');
    }
    // This listener will publish the UserCreated event to the message broker (e.g., RabbitMQ, Kafka)
    // so that other services can consume it and react accordingly (e.g., send welcome email, update search index, etc.)
    public function handle(UserCreated $event): void
    {
        $this->logger->info('UserCreated publish triggered');
        // Here you would implement the logic to publish the event to your message broker
        // For example, using a RabbitMQ client or Laravel's built-in queue system

        $user = $event->user;

        $this->logger->info('UserCreated published payload', ['payload' => $user]);

        $payload = json_encode([
            'event' => 'user.created',
            'data' => [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'is_active' => $user->is_active,
            ]
        ]);
        
        $this->logger->info('UserCreated published to message broker', ['payload' => $payload]);
        Queue::connection('rabbitmq')->pushRaw($payload, 'user.created');
    }
}