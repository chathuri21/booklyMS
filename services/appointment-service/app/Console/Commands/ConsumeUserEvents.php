<?php

namespace App\Console\Commands;

use App\Messaging\Handlers\UserCreatedHandler;
use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ConsumeUserEvents extends Command
{
    protected $signature = 'consume:user-events';
    protected $description = 'Consume user.* events from the message broker and update user_snapshots';

    public function handle(UserCreatedHandler $userCreatedHandler): int
    {        
        $this->info('Starting to consume user events...');   

        $connection = new AMQPStreamConnection(
            config('queue.connections.rabbitmq.0.host'),
            config('queue.connections.rabbitmq.0.port'),
            config('queue.connections.rabbitmq.0.user'),
            config('queue.connections.rabbitmq.0.password'),
            config('queue.connections.rabbitmq.0.vhost')
        );

        // Exchange and queue names from config
        $exchange = config('queue.connections.rabbitmq.options.exchange.user_events', 'user.events');
        $queue = config('queue.connections.rabbitmq.options.queue.appointment_user_events', 'appointment.user_events');
        
        $channel = $connection->channel();

        $channel->exchange_declare($exchange, 'topic', false, true, false);
        $channel->queue_declare($queue, false, true, false, false);

        // Bind the queue to the exchange with a routing key that matches user created event
        $channel->queue_bind($queue, $exchange, 'user.created'); 

        $this->info("Listening on [{$queue}] via exchange [{$exchange}]...");

        $callback = function (AMQPMessage $msg) use ($userCreatedHandler): void {
            $routingKey = $msg->getRoutingKey();
            $this->line("Received message with routing key: [{$routingKey}]");

            try {
                $eventData = json_decode($msg->getBody(), true, 512, JSON_THROW_ON_ERROR);

                if ($routingKey === 'user.created') {
                    $userCreatedHandler->handle($eventData['data']);
                    $this->info('Processed user.created event for user_id: ' . $eventData['data']['id']);
                } else {
                    $this->warn("No handler for [{$routingKey}]");
                }

                // Acknowledge the message after processing
                $msg->ack();
                $this->info("[{$routingKey}] processed successfully and acknowledged.");

            } catch (\Exception $e) {
                $this->error("[{$routingKey}] Failed: " . $e->getMessage());
                // nack without requeueing - send to dead-letter queue if configured
                $msg->nack(true);
            }
        };

        $channel->basic_qos(null, 1, null); // Process one message at a time
        $channel->basic_consume($queue, '', false, false, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();

        return self::SUCCESS;
    }
}