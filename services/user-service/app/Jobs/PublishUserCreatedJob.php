<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Queue;

class PublishUserCreatedJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $payload)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Queue::connection('rabbitmq')->pushRaw($this->payload, 'user.created');
    }
}
