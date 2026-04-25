<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

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
        $connection = new AMQPStreamConnection(
            config('queue.connections.rabbitmq.hosts.0.host'),
            config('queue.connections.rabbitmq.hosts.0.port'),
            config('queue.connections.rabbitmq.hosts.0.user'),
            config('queue.connections.rabbitmq.hosts.0.password'),
            config('queue.connections.rabbitmq.hosts.0.vhost')
        );

        $channel = $connection->channel();

        $exchange = config('queue.connections.rabbitmq.options.exchange.user_events', 'user.events');

        $channel->exchange_declare($exchange, 'topic', false, true, false);

        $msg = new AMQPMessage($this->payload, [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            'content_type' => 'application/json'
        ]);

        $channel->basic_publish($msg, $exchange, 'user.created');

        $channel->close();
        $connection->close();
        
        // Queue::connection('rabbitmq')->pushRaw($this->payload, 'user.created', [
        //     'delivery_mode' => 2, // Make message persistent
        // ]);
    }
}
