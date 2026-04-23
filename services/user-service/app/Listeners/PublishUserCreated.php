<?php

namespace App\Listeners;

use App\Domain\Services\LoggerInterface;
use App\Events\UserCreated;
use App\Jobs\PublishUserCreatedJob;

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
        $this->logger->info('PublishUserCreated triggered');
        // Here you would implement the logic to publish the event to your message broker
        // For example, using a RabbitMQ client or Laravel's built-in queue system

        $user = $event->user;
        
        PublishUserCreatedJob::dispatch(json_encode([
            'event' => 'user.created',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'is_active' => $user->isActive
            ]
        ]));
    }
}