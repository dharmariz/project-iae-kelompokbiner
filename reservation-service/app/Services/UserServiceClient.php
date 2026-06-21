<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class UserServiceClient
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('USER_SERVICE_URL', 'http://127.0.0.1:8001');
    }

    public function getUser(int $userId): ?array
    {
        try {
            $response = Http::timeout(30)->get("{$this->baseUrl}/api/users/{$userId}");
            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            // Lempar error asli agar terlihat di Postman
            throw new \Exception("Gagal connect ke User Service: " . $e->getMessage());
        }
    }
}
