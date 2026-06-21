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
        // Jika koneksi sudah ada (belum di-null kan sebelumnya), gunakan koneksi tersebut
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

        // Deklarasi queue (pastikan queue ada di RabbitMQ)
        $this->channel->queue_declare($queue, false, true, false, false);

        // Buat pesan dalam format JSON
        $msg = new AMQPMessage(
            json_encode($payload),
            ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );

        // Kirim pesan ke queue
        $this->channel->basic_publish($msg, '', $queue);

        // =========================================================
        // PAKSA TUTUP CHANNEL & KONEKSI AGAR PESAN TER-FLUSH KE JARINGAN
        // Ini memastikan pesan benar-benar sampai ke RabbitMQ saat itu juga.
        // =========================================================
        $this->channel->close();
        $this->connection->close();

        // Reset menjadi null agar下次 publish() memanggil connect() lagi
        $this->channel = null;
        $this->connection = null;
    }

    public function __destruct()
    {
        // Fallback jika ada koneksi yang tertinggal saat objek dihancurkan
        if ($this->channel) $this->channel->close();
        if ($this->connection) $this->connection->close();
    }
}
