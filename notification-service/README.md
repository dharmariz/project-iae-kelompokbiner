# Notification Service - CRRS (Campus Room Reservation System)

Service untuk mengelola notifikasi pada sistem Campus Room Reservation System (CRRS). Service ini bertindak sebagai RabbitMQ Consumer yang menerima event dari Reservation Service dan menyimpan notifikasi ke database.

## Informasi Service

* **Port:** 8004
* **Database:** crrs_notification_db
* **Framework:** Laravel 11
* **Message Broker:** RabbitMQ
* **Role:** Consumer

## Endpoint

| Method | Endpoint           | Deskripsi                     |
| ------ | ------------------ | ----------------------------- |
| GET    | /api/notifications | Menampilkan daftar notifikasi |

## RabbitMQ Events

Notification Service menerima event berikut:

* reservation.created
* reservation.approved
* reservation.rejected
* reservation.cancelled

## Setup Lokal (Development)

1. Clone repository dan checkout branch notification service:

```bash
git clone https://github.com/dharmariz/project-iae-kelompokbiner.git
cd project-iae-kelompokbiner
git checkout develop/notification-service
```

2. Install dependencies:

```bash
composer install
```

3. Copy environment file:

```bash
cp .env.example .env
php artisan key:generate
```

4. Buat database:

```text
crrs_notification_db
```

5. Jalankan migration:

```bash
php artisan migrate
```

6. Jalankan RabbitMQ:

```bash
docker run -d --hostname rabbitmq --name rabbitmq -p 5672:5672 -p 15672:15672 rabbitmq:3-management
```

7. Jalankan queue worker:

```bash
php artisan queue:work
```

8. Jalankan aplikasi:

```bash
php artisan serve --port=8004
```

Service berjalan pada:

```text
http://127.0.0.1:8004
```

## Contoh Request

### Get Notifications

```http
GET /api/notifications
Accept: application/json
```

Response:

```json
[
  {
    "id": 1,
    "user_id": 1,
    "message": "Reservasi berhasil dibuat",
    "status": "unread",
    "created_at": "2026-06-13T10:00:00.000000Z"
  }
]
```

## Arsitektur Integrasi

```text
Reservation Service
        │
        │ RabbitMQ Event
        ▼
Notification Service
        │
        ▼
crrs_notification_db
```

## Catatan Integrasi Microservices

Notification Service tidak dipanggil langsung oleh Reservation Service melalui REST API. Komunikasi dilakukan secara asynchronous menggunakan RabbitMQ.

Notification Service berperan sebagai Consumer, sedangkan Reservation Service berperan sebagai Producer.
