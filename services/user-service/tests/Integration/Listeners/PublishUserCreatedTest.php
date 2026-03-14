<?php

namespace Tests\Integration\Listeners;

use App\Domain\Services\LoggerInterface;
use App\Events\UserCreated;
use App\Infrastructure\EloquentUserMapper;
use App\Jobs\PublishUserCreatedJob;
use App\Listeners\PublishUserCreated;
use App\Models\User as EloquentUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PublishUserCreatedTest extends TestCase
{
    use RefreshDatabase;

    public function test_listener_handles_event(): void
    {
        Queue::fake();

        $loggerMock = $this->createMock(LoggerInterface::class);

        $eloquentUser = EloquentUser::factory()->create();
        $domainUser = EloquentUserMapper::toDomain($eloquentUser);

        $event = new UserCreated($domainUser, $loggerMock);

        $listener = new PublishUserCreated($loggerMock);
        $listener->handle($event);

        Queue::assertPushed(PublishUserCreatedJob::class, function ($job) use ($domainUser) {
            return str_contains($job->payload, 'user.created') && str_contains($job->payload, (string) $domainUser->id);
        });

    }
}