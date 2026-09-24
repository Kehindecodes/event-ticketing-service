<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class DeadLetterPublisher
{
    public function publish(array $payload): void
    {
        $connection = $this->connect();
        $channel = $connection->channel();

        try {
            $channel->basic_publish(
                new AMQPMessage(json_encode($payload), [
                    'content_type' => 'application/json',
                    'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                ]),
                $this->exchange(),
                $this->routingKey(),
            );
        } finally {
            $channel->close();
            $connection->close();
        }
    }

    public function connect(): AMQPStreamConnection
    {
        $config = config('queue.connections.rabbitmq.hosts.0');

        return new AMQPStreamConnection(
            $config['host'],
            $config['port'],
            $config['user'],
            $config['password'],
            $config['vhost'],
        );
    }

    public function exchange(): string
    {
        return config('queue.connections.rabbitmq.options.queue.arguments.x-dead-letter-exchange');
    }

    public function routingKey(): string
    {
        return config('queue.connections.rabbitmq.options.queue.arguments.x-dead-letter-routing-key');
    }

    public function queue(): string
    {
        return config('queue.connections.rabbitmq.dlq');
    }
}
