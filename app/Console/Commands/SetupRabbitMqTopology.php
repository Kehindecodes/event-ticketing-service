<?php

namespace App\Console\Commands;

use App\Services\DeadLetterPublisher;
use Illuminate\Console\Command;

class SetupRabbitMqTopology extends Command
{
    protected $signature = 'rabbitmq:setup';

    protected $description = 'Declare the notification dead letter exchange, queue and binding';

    public function handle(DeadLetterPublisher $publisher): int
    {
        $connection = $publisher->connect();
        $channel = $connection->channel();

        $channel->exchange_declare($publisher->exchange(), 'direct', false, true, false);
        $channel->queue_declare($publisher->queue(), false, true, false, false);
        $channel->queue_bind($publisher->queue(), $publisher->exchange(), $publisher->routingKey());

        $channel->close();
        $connection->close();

        $this->info("Declared {$publisher->exchange()} -> {$publisher->queue()} ({$publisher->routingKey()})");

        return self::SUCCESS;
    }
}
