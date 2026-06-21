<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RoomServiceClient
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('ROOM_SERVICE_URL', 'http://127.0.0.1:8002');
    }

    public function getRoom(int $roomId): ?array
    {
        try {
            $response = Http::timeout(30)->get("{$this->baseUrl}/api/rooms/{$roomId}");
            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            // Lempar error asli agar terlihat di Postman
            throw new \Exception("Gagal connect ke Room Service: " . $e->getMessage());
        }
    }
}
