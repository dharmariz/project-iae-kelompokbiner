#!/bin/bash

set -e

BASE_DIR="$(pwd)"

echo "🛑 Stopping CRRS Microservices & Hasura..."

# =========================
# STOP HASURA (Jika ada)
# =========================
echo ""
echo "▶ Stopping Hasura..."
if [ -f "$BASE_DIR/docker-compose-hasura.yml" ]; then
    docker-compose -f docker-compose-hasura.yml down
else
    echo "ℹ️ Hasura compose file not found, skipping..."
fi

# =========================
# STOP RESERVATION SERVICE
# =========================
echo ""
echo "▶ Stopping Reservation Service..."
cd "$BASE_DIR/reservation-service"
docker-compose down

# =========================
# STOP ROOM SERVICE
# =========================
echo ""
echo "▶ Stopping Room Service..."
cd "$BASE_DIR/room-service"
docker-compose down

# =========================
# STOP USER SERVICE
# =========================
echo ""
echo "▶ Stopping User Service..."
cd "$BASE_DIR/user-service"
docker-compose down

# =========================
# STOP NOTIFICATION SERVICE & RABBITMQ
# =========================
echo ""
echo "▶ Stopping Notification Service & RabbitMQ..."
cd "$BASE_DIR/notification-service"
docker-compose down

echo ""
echo "✅ All services have been stopped successfully!"
echo "💡 Data is still safe in Docker volumes."