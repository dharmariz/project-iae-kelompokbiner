<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Services\UserServiceClient;
use App\Services\RoomServiceClient;
use App\Services\RabbitMQPublisher;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function __construct(
        private UserServiceClient $userClient,
        private RoomServiceClient $roomClient,
        private RabbitMQPublisher $publisher
    ) {}

    // GET /reservations
    public function index()
    {
        return response()->json(Reservation::all());
    }

    // GET /reservations/{id}
    public function show(int $id)
    {
        $reservation = Reservation::findOrFail($id);
        return response()->json($reservation);
    }

    // POST /reservations
    public function store(Request $request)
    {
        $request->validate([
            'user_id'        => 'required|integer',
            'room_id'        => 'required|integer',
            'purpose'        => 'required|string',
            'start_datetime' => 'required|date',
            'end_datetime'   => 'required|date|after:start_datetime',
        ]);

        // Validasi user exist di user-service
        $user = $this->userClient->getUser($request->user_id);
        if (!$user) return response()->json(['message' => 'User tidak ditemukan'], 404);

        // Validasi room exist di room-service
        $room = $this->roomClient->getRoom($request->room_id);
        if (!$room) return response()->json(['message' => 'Ruangan tidak ditemukan'], 404);

        // Cek jadwal bentrok
        $conflict = Reservation::where('room_id', $request->room_id)
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->where(function ($q) use ($request) {
                $q->whereBetween('start_datetime', [$request->start_datetime, $request->end_datetime])
                    ->orWhereBetween('end_datetime', [$request->start_datetime, $request->end_datetime])
                    ->orWhere(function ($q2) use ($request) {
                        $q2->where('start_datetime', '<=', $request->start_datetime)
                            ->where('end_datetime', '>=', $request->end_datetime);
                    });
            })->exists();

        if ($conflict) return response()->json(['message' => 'Jadwal ruangan bentrok'], 422);

        $reservation = Reservation::create([
            'user_id'        => $request->user_id,
            'room_id'        => $request->room_id,
            'purpose'        => $request->purpose,
            'start_datetime' => $request->start_datetime,
            'end_datetime'   => $request->end_datetime,
            'status'         => 'pending',
        ]);

        // Publish ke RabbitMQ
        $this->publisher->publish('reservation.created', [
            'reservation_id' => $reservation->id,
            'user_id'        => $reservation->user_id,
            'room_id'        => $reservation->room_id,
        ]);

        return response()->json($reservation, 201);
    }

    // PUT /reservations/{id}
    public function update(Request $request, int $id)
    {
        $reservation = Reservation::findOrFail($id);
        if ($reservation->status !== 'pending') {
            return response()->json(['message' => 'Hanya reservasi pending yang bisa diedit'], 422);
        }
        $reservation->update($request->only(['purpose', 'start_datetime', 'end_datetime']));
        return response()->json($reservation);
    }

    // DELETE /reservations/{id}
    public function destroy(int $id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->update(['status' => 'cancelled']);

        $this->publisher->publish('reservation.cancelled', [
            'reservation_id' => $reservation->id,
            'user_id'        => $reservation->user_id,
        ]);

        return response()->json(['message' => 'Reservasi dibatalkan']);
    }

    // PUT /reservations/{id}/approve
    public function approve(int $id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->update(['status' => 'approved']);

        $this->publisher->publish('reservation.approved', [
            'reservation_id' => $reservation->id,
            'user_id'        => $reservation->user_id,
        ]);

        return response()->json(['message' => 'Reservasi disetujui', 'data' => $reservation]);
    }

    // PUT /reservations/{id}/reject
    public function reject(int $id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->update(['status' => 'rejected']);

        $this->publisher->publish('reservation.rejected', [
            'reservation_id' => $reservation->id,
            'user_id'        => $reservation->user_id,
        ]);

        return response()->json(['message' => 'Reservasi ditolak', 'data' => $reservation]);
    }
}
