<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQPublisher
{
    private ?AMQPStreamConnection $connection = null;
    private ?AMQPChannel $channel = null;

    private function connect(): void
    {
        if ($this->connection) return;

        $this->connection = new AMQPStreamConnection(
            env('RABBITMQ_HOST', 'rabbitmq'),
            env('RABBITMQ_PORT', 5672),
            env('RABBITMQ_USER', 'guest'),
            env('RABBITMQ_PASSWORD', 'guest')
        );
        $this->channel = $this->connection->channel();
    }

    public function publish(string $queue, array $payload): void
    {
        $this->connect();

        $this->channel->queue_declare($queue, false, true, false, false);

        $msg = new AMQPMessage(
            json_encode($payload),
            ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );

        $this->channel->basic_publish($msg, '', $queue);
    }

    public function __destruct()
    {
        if ($this->channel) $this->channel->close();
        if ($this->connection) $this->connection->close();
    }
}
