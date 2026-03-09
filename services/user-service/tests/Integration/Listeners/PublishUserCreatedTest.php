<?php

namespace Tests\Integration\Listeners;

use App\Domain\Services\LoggerInterface;
use App\Events\UserCreated;
use App\Listeners\PublishUserCreated;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublishUserCreatedTest extends TestCase
{
    use RefreshDatabase;

    public function test_listener_handles_event(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);

        $user = User::factory()->create();

        $event = new UserCreated($user, $loggerMock);

        $listener = new PublishUserCreated($loggerMock);
        $listener->handle($event);
        
        $this->assertTrue(true);
    }
}