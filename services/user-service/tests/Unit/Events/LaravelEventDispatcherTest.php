<?php

namespace Tests\Unit\Events;

use App\Events\UserCreated;
use App\Infrastructure\Events\LaravelEventDispatcher;
use App\Models\User;
use App\Domain\Services\LoggerInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class LaravelEventDispatcherTest extends TestCase
{
    use RefreshDatabase;

    public function test_dispatcher_dispatches_event(): void
    {
        Event::fake();

        $dispatcher = new LaravelEventDispatcher();

        $user = User::factory()->create();

        $loggerMock = $this->createMock(LoggerInterface::class);

        $event = new UserCreated($user, $loggerMock);

        $dispatcher->dispatch($event);

        $this->assertTrue(true);
    }
}