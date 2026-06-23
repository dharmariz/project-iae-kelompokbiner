#!/bin/bash

set -e

BASE_DIR="$(pwd)"

echo "🚀 Starting CRRS Microservices Via Docker..."

# 1. Pastikan network sudah ada
if ! docker network ls | grep -q "crrs_network"; then
    echo "🌐 Creating CRRS_Network..."
    docker network create crrs_network
else
    echo "✅ CRRS_Network already exists"
fi

# =========================
# NOTIFICATION SERVICE (RabbitMQ ada di sini) — DIJALANKAN DULUAN
# =========================
echo ""
echo "▶ Starting Notification Service + RabbitMQ..."
cd "$BASE_DIR/notification-service"
docker-compose up -d
echo "⏳ Waiting 10s for RabbitMQ to be ready..."
sleep 10

# =========================
# USER SERVICE
# =========================
echo ""
echo "▶ Starting User Service..."
cd "$BASE_DIR/user-service"
docker-compose up -d

# =========================
# ROOM SERVICE
# =========================
echo ""
echo "▶ Starting Room Service..."
cd "$BASE_DIR/room-service"
docker-compose up -d

# =========================
# RESERVATION SERVICE
# =========================
echo ""
echo "▶ Starting Reservation Service..."
cd "$BASE_DIR/reservation-service"
docker-compose up -d

# =========================
# MIGRATE SEMUA SERVICE
# =========================
echo ""
echo "🗄️  Running migrations..."

cd "$BASE_DIR/notification-service"
docker-compose exec -T notification-service php artisan migrate --force

cd "$BASE_DIR/user-service"
docker-compose exec -T user-service php artisan migrate --force

cd "$BASE_DIR/room-service"
docker-compose exec -T room-service php artisan migrate --force

cd "$BASE_DIR/reservation-service"
docker-compose exec -T reservation-service php artisan migrate --force

# =========================
# SEEDER KHUSUS ROOM SERVICE (10 ruangan)
# =========================
echo ""
echo "🌱 Seeding 10 ruangan di Room Service..."
cd "$BASE_DIR/room-service"
docker-compose exec -T room-service php artisan db:seed --force

echo ""
echo "✅ All services are running!"
echo ""
echo "📌 Service URLs (gunakan 127.0.0.1, JANGAN localhost):"
echo "   User Service         : http://127.0.0.1:8001"
echo "   Room Service         : http://127.0.0.1:8002"
echo "   Reservation Service  : http://127.0.0.1:8003"
echo "   Notification Service : http://127.0.0.1:8004"
echo "   RabbitMQ Dashboard   : http://127.0.0.1:15672"
echo "   (RabbitMQ login: guest / guest)"
echo ""
echo "📨 Untuk mulai mendengarkan event RabbitMQ, buka terminal BARU dan jalankan:"
echo "   cd notification-service"
echo "   docker-compose exec notification-service php artisan rabbitmq:consume-manual"