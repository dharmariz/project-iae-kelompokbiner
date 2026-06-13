# Reservation Service — CRRS (Campus Room Reservation System)

Reservation Service merupakan layanan yang bertanggung jawab untuk mengelola proses reservasi ruangan pada sistem **Campus Room Reservation System (CRRS)**. Service ini menangani pembuatan, perubahan, persetujuan, penolakan, dan pembatalan reservasi. Selain itu, service ini terintegrasi dengan User Service, Room Service, dan Notification Service untuk mendukung proses bisnis secara menyeluruh.

---

## Informasi Service

- **Port:** 8003
- **Database:** `crrs_reservation_db`
- **Framework:** Laravel 11
- **Autentikasi:** Tidak tersedia secara langsung (delegasi ke User Service)

---

## Endpoint API

### Menampilkan Seluruh Reservasi

```http
GET /api/reservations
```

Menampilkan seluruh data reservasi yang tersimpan pada sistem.

### Menampilkan Detail Reservasi

```http
GET /api/reservations/{id}
```

Menampilkan detail reservasi berdasarkan ID.

### Membuat Reservasi Baru

```http
POST /api/reservations
```

Membuat data reservasi baru.

### Mengubah Reservasi

```http
PUT /api/reservations/{id}
```

Mengubah informasi reservasi yang telah dibuat.

### Membatalkan Reservasi

```http
DELETE /api/reservations/{id}
```

Menghapus atau membatalkan reservasi.

### Menyetujui Reservasi

```http
PUT /api/reservations/{id}/approve
```

Digunakan oleh administrator untuk menyetujui reservasi.

### Menolak Reservasi

```http
PUT /api/reservations/{id}/reject
```

Digunakan oleh administrator untuk menolak reservasi.

---

## Setup Lokal (Development)

### 1. Clone Repository

```bash
git clone <repository-url>
cd reservation-service
```

### 2. Install Dependency

```bash
composer install
```

### 3. Konfigurasi Environment

Salin file environment:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

Sesuaikan konfigurasi database pada file `.env`:

```env
DB_DATABASE=crrs_reservation_db
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Jalankan Migration

```bash
php artisan migrate
```

### 5. Menjalankan Service

```bash
php artisan serve --port=8003
```

Service akan berjalan pada alamat:

```text
http://127.0.0.1:8003
```

---

## Menjalankan Menggunakan Docker

### Membuat Docker Network

Langkah ini hanya perlu dilakukan satu kali.

```bash
docker network create crrs_network
```

### Build dan Menjalankan Container

```bash
docker-compose up -d --build
```

### Menjalankan Migration

```bash
docker-compose exec reservation-service php artisan migrate
```

---

## Contoh Request

### Membuat Reservasi Baru

```http
POST /api/reservations
Content-Type: application/json
```

```json
{
    "user_id": 1,
    "room_id": 2,
    "purpose": "Rapat Divisi IT",
    "start_datetime": "2025-08-01 09:00:00",
    "end_datetime": "2025-08-01 11:00:00"
}
```

### Response

```json
{
    "id": 1,
    "user_id": 1,
    "room_id": 2,
    "purpose": "Rapat Divisi IT",
    "start_datetime": "2025-08-01T09:00:00.000000Z",
    "end_datetime": "2025-08-01T11:00:00.000000Z",
    "status": "pending"
}
```

---

## Integrasi Microservices

### User Service

Reservation Service menggunakan endpoint berikut untuk memvalidasi data pengguna sebelum reservasi dibuat.

```http
GET /api/users/{id}
```

### Room Service

Reservation Service menggunakan endpoint berikut untuk memvalidasi data ruangan yang dipilih pengguna.

```http
GET /api/rooms/{id}
```

### Notification Service

Notification Service digunakan untuk mengirimkan notifikasi kepada pengguna ketika terjadi perubahan status reservasi. Komunikasi dilakukan secara asynchronous melalui RabbitMQ.

---

## RabbitMQ Events

### Reservasi Dibuat

Queue:

```text
reservation.created
```

Dikirim ketika reservasi baru berhasil dibuat.

### Reservasi Disetujui

Queue:

```text
reservation.approved
```

Dikirim ketika reservasi disetujui oleh administrator.

### Reservasi Ditolak

Queue:

```text
reservation.rejected
```

Dikirim ketika reservasi ditolak oleh administrator.

### Reservasi Dibatalkan

Queue:

```text
reservation.cancelled
```

Dikirim ketika reservasi dibatalkan oleh pengguna atau administrator.

---

## Konfigurasi Environment

Pastikan file `.env.example` memiliki struktur yang sama dengan file `.env` yang digunakan selama proses pengembangan.

Nilai sensitif seperti berikut dapat dikosongkan:

```env
APP_KEY=
DB_PASSWORD=
```

Hal ini bertujuan agar anggota tim lain dapat menjalankan service dengan konfigurasi yang konsisten tanpa perlu membagikan kredensial yang bersifat rahasia.

---

## Catatan Docker

Saat sistem dijalankan menggunakan Docker Compose, komunikasi antar service tidak menggunakan `localhost`, melainkan menggunakan hostname container yang berada dalam jaringan Docker yang sama.

Contoh hostname yang digunakan:

```Hostname
crrs-user-service
crrs-room-service
crrs-reservation-service
crrs-notification-service
```

Seluruh service harus berada dalam network:crrs_network

agar komunikasi antar microservices dapat berjalan dengan baik.

## Struktur Direktori

reservation-service/
├── app/                # Source code aplikasi
├── bootstrap/          # Bootstrap framework Laravel
├── config/             # Konfigurasi aplikasi
├── database/           # Migration, seeder, dan factory
├── public/             # Public entry point
├── resources/          # Views dan resource aplikasi
├── routes/             # Definisi route API
├── storage/            # Penyimpanan file dan log
├── tests/              # Unit dan feature tests
│
├── .env.example
├── artisan
├── composer.json
├── docker-compose.yml
├── Dockerfile
├── phpunit.xml
└── README.md
