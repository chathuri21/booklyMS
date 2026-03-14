<?php

namespace Tests\Integration\Events;

use App\Events\UserCreated;
use App\Infrastructure\EloquentUserMapper;
use App\Infrastructure\Events\LaravelEventDispatcher;
use App\Models\User as EloquentUser;
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

        $eloquentUser = EloquentUser::factory()->create();
        $domainUser = EloquentUserMapper::toDomain($eloquentUser);

        $event = new UserCreated($domainUser);

        $dispatcher->dispatch($event);

        Event::assertDispatched(UserCreated::class);
    }
}