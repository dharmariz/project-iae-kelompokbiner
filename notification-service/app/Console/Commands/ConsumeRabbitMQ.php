<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use App\Models\Notification;

class ConsumeRabbitMQ extends Command
{
    protected $signature = 'rabbitmq:consume-manual';
    protected $description = 'Consume all reservation queues with 5 seconds delay';

    public function handle()
    {
        // 1. KONEKSI KE RABBITMQ
        $connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        // 2. SETUP QUEUE (Deklarasi semua queue agar pasti ada di RabbitMQ)
        $queues = [
            'reservation.created',
            'reservation.approved',
            'reservation.rejected',
            'reservation.cancelled'
        ];

        foreach ($queues as $queueName) {
            $channel->queue_declare($queueName, false, true, false, false);
        }

        $this->info("Listening to all reservation queues...");

        // 3. CALLBACK FUNCTION (Logika saat pesan diterima)
        $callback = function ($msg) {
            $queueName = $msg->delivery_info['routing_key'];
            $this->info("\n[>] Received message on queue: " . $queueName);
            $this->info("    Payload: " . $msg->body);

            // SIMULASI ASYNC DELAY: Jeda 5 detik
            $this->info("    Processing in 5 seconds...");
            sleep(5);

            $data = json_decode($msg->body, true);

            try {
                $messageText = '';
                switch ($queueName) {
                    case 'reservation.created':
                        $messageText = "Reservasi ruangan dengan ID {$data['reservation_id']} berhasil dibuat, menunggu approval Admin.";
                        break;
                    case 'reservation.approved':
                        $messageText = "Reservasi Anda dengan ID {$data['reservation_id']} telah DISETUJUI oleh Admin.";
                        break;
                    case 'reservation.rejected':
                        $messageText = "Maaf, Reservasi Anda dengan ID {$data['reservation_id']} telah DITOLAK.";
                        break;
                    case 'reservation.cancelled':
                        $messageText = "Reservasi dengan ID {$data['reservation_id']} telah dibatalkan.";
                        break;
                }

                Notification::create([
                    'user_id' => $data['user_id'] ?? null,
                    'message' => $messageText,
                    'status' => 'unread'
                ]);

                $this->info("    [OK] Successfully processed and saved to DB.");
            } catch (\Exception $e) {
                $this->error("    [ERROR] Failed to process: " . $e->getMessage());
            }
        };

        // 4. BIND CALLBACK KE KEEMPAT QUEUE
        foreach ($queues as $queueName) {
            $channel->basic_consume($queueName, '', false, true, false, false, $callback);
        }

        // 5. JALANKAN WORKER
        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}
