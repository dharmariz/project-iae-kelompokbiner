# Room Service - CRRS (Campus Room Reservation System)

Service untuk mengelola data ruangan pada sistem Campus Room Reservation System (CRRS).

## Informasi Service

* **Port:** 8002 (host) → 8000 (container)
* **Database:** crrs_room_db
* **Framework:** Laravel 13
* **Fungsi:** Manajemen data ruangan (CRUD)

## Endpoint

| Method | Endpoint        | Deskripsi                             |
| ------ | --------------- | ------------------------------------- |
| GET    | /api/rooms      | Melihat seluruh daftar ruangan        |
| GET    | /api/rooms/{id} | Melihat detail ruangan berdasarkan ID |
| POST   | /api/rooms      | Menambahkan ruangan baru              |
| PUT    | /api/rooms/{id} | Mengubah data ruangan                 |
| DELETE | /api/rooms/{id} | Menghapus data ruangan                |

## Setup Lokal (Development)

1. Clone repository dan checkout branch ini:

```bash
git clone https://github.com/dharmariz/project-iae-kelompokbiner.git
cd project-iae-kelompokbiner
git checkout develop/room-service
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

4. Buat database `crrs_room_db` di MySQL lokal (phpMyAdmin/MySQL Workbench/DBeaver).

5. Jalankan migration dan seeder:

```bash
php artisan migrate --seed
```

6. Jalankan server:

```bash
php artisan serve --port=8002
```

Service berjalan di:

```text
http://127.0.0.1:8002
```

## Contoh Request

### Get All Rooms

```http
GET /api/rooms
Accept: application/json
```

### Get Room By ID

```http
GET /api/rooms/1
Accept: application/json
```

### Create Room

```http
POST /api/rooms
Content-Type: application/json

{
    "room_name": "RK C.999",
    "room_type": "Classroom",
    "capacity": 50,
    "description": "Test Room",
    "status": "available"
}
```

### Update Room

```http
PUT /api/rooms/11
Content-Type: application/json

{
    "room_name": "RK C.999",
    "room_type": "Classroom",
    "capacity": 60,
    "description": "Updated Room",
    "status": "maintenance"
}
```

### Delete Room

```http
DELETE /api/rooms/11
Accept: application/json
```

## Data Seeder

Saat menjalankan:

```bash
php artisan migrate --seed
```

akan otomatis dibuat data ruangan berikut:

* RK A.101
* RK A.121
* RK A.131
* RK B.501
* RK B.511
* RK B.521
* Aula G1
* Aula G2
* Sekber L2R
* Sekber L2L

## Endpoint untuk Reservation Service

Endpoint berikut dapat digunakan oleh Reservation Service untuk memvalidasi ketersediaan dan informasi ruangan:

```http
GET /api/rooms/{id}
Accept: application/json
```

Contoh Response:

```json
{
    "id": 1,
    "room_name": "RK A.101",
    "room_type": "Classroom",
    "capacity": 40,
    "description": "Ruang kelas lantai 1",
    "status": "available"
}
```

Jika ID tidak ditemukan:

```json
{
    "message": "Room not found"
}
```

## Catatan Integrasi Microservices

Saat dijalankan melalui Docker Compose, hostname yang digunakan service lain untuk mengakses Room Service adalah:

```text
crrs-room-service
```

bukan:

```text
localhost
```

karena seluruh service berada dalam jaringan Docker yang sama.
