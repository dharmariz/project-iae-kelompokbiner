# User Service - CRRS (Campus Room Reservation System)

Service untuk mengelola autentikasi dan profil pengguna pada sistem Campus Room Reservation System (CRRS).

## Informasi Service

- **Port:** 8001 (host) → 8000 (container)
- **Database:** crrs_user_db
- **Framework:** Laravel 11
- **Autentikasi:** Laravel Sanctum (token-based)

## Endpoint

| Method | Endpoint | Auth | Deskripsi |
|--------|----------|------|-----------|
| POST | /api/register | - | Registrasi user baru |
| POST | /api/login | - | Login, mengembalikan token |
| GET | /api/profile | Bearer Token | Melihat profil user yang login |
| PUT | /api/profile | Bearer Token | Update nama/password |
| POST | /api/logout | Bearer Token | Logout, menghapus token |
| GET | /api/users/{id} | - | **Internal endpoint** untuk dipanggil service lain |

## Setup Lokal (Development)

1. Clone repo dan checkout branch ini:
```bash
git clone https://github.com/dharmariz/project-iae-kelompokbiner.git
cd project-iae-kelompokbiner
git checkout develop/user-service
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

4. Buat database `crrs_user_db` di MySQL lokal (phpMyAdmin/MySQL Workbench/DBeaver).

5. Jalankan migration:
```bash
php artisan migrate
```

6. Jalankan server:
```bash
php artisan serve --port=8001
```

Service berjalan di `http://127.0.0.1:8001`

## Contoh Request

### Register
```http
POST /api/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "johndoe@student.appleacademy.ac.id",
    "password": "password123",
    "role": "mahasiswa"
}
```

### Login
```http
POST /api/login
Content-Type: application/json

{
    "email": "johndoe@student.appleacademy.ac.id",
    "password": "password123"
}
```

Response akan memberikan `token`. Gunakan format `Bearer {id}|{token}` pada header `Authorization` untuk endpoint yang membutuhkan autentikasi.

### Cek Profil
```http
GET /api/profile
Authorization: Bearer 2|xxxxxxxxxxxxx
Accept: application/json
```

### Endpoint untuk Reservation Service (Abel)

Endpoint ini **tidak memerlukan token**, digunakan untuk validasi user saat membuat reservasi:

```http
GET /api/users/{id}
Accept: application/json
```

Response:
```json
{
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "johndoe@student.appleacademy.ac.id",
        "role": "mahasiswa"
    }
}
```

Jika `id` tidak ditemukan, response `404`:
```json
{
    "message": "User tidak ditemukan"
}
```

## Catatan Integrasi Microservices

Saat dijalankan via Docker Compose, hostname yang digunakan service lain untuk memanggil User Service adalah `crrs-user-service` (bukan `localhost`), karena seluruh service berada dalam jaringan Docker `crrs-network`.