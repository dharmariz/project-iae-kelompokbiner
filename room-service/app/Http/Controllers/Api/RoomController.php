<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Room::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. PROTEKSI UTAMA: Cek apakah format JSON rusak/cacat (seperti kelebihan koma atau kurang tanda petik)
        if ($request->isJson() && is_null(json_decode($request->getContent()))) {
            return response()->json([
                'message' => 'Format JSON kamu rusak/invalid! Cek lagi kelebihan koma atau kurang tanda petik "" di Postman.',
                'error_code' => 'INVALID_JSON_FORMAT'
            ], 400);
        }

        // 2. VALIDASI KETAT DATA BARU
        $validated = $request->validate([
            'room_name'   => 'required|string|max:255|unique:rooms,room_name',
            'room_type'   => 'required|string|max:255',
            'capacity'    => 'required|integer|min:1', // Wajib angka dan minimal 1
            'description' => 'nullable|string',
            'status'      => 'required|string|in:Available,Maintenance,Occupied',
        ], [
            'room_name.unique' => 'The room name has already been taken. Silakan gunakan nama ruangan lain.',
            'capacity.integer' => 'Kolom capacity WAJIB berupa angka bulat (integer)! Gak boleh pakai kata teks.',
            'capacity.min'     => 'Kapasitas ruangan minimal harus 1!',
        ]);

        $room = Room::create($validated);

        return response()->json([
            'message' => 'Room created successfully',
            'data' => $room
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json([
                'message' => 'Room not found'
            ], 404);
        }

        return response()->json($room, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json([
                'message' => 'Room not found'
            ], 404);
        }

        // 1. PROTEKSI UTAMA: Cek jika format JSON yang dikirim via PUT rusak (misal kelebihan koma gantung)
        if ($request->isJson() && is_null(json_decode($request->getContent()))) {
            return response()->json([
                'message' => 'Format JSON kamu rusak/invalid! Cek lagi kelebihan koma atau kurang tanda petik "" di Postman.',
                'error_code' => 'INVALID_JSON_FORMAT'
            ], 400);
        }

        // Ambil input data secara aman
        $inputData = $request->isJson() ? $request->json()->all() : $request->all();

        // 2. VALIDASI KETAT KHUSUS UPDATE PARSIAL
        $validated = Validator::make($inputData, [
            'room_name'   => 'sometimes|required|string|max:255|unique:rooms,room_name,'.$id,
            'room_type'   => 'sometimes|required|string|max:255',
            'capacity'    => 'sometimes|required|integer|min:1', // Kadang dikirim, tapi kalau dikirim wajib integer dan minimal 1
            'description' => 'nullable|string',
            'status'      => 'sometimes|required|string|in:Available,Maintenance,Occupied',
        ], [
            'room_name.unique' => 'The room name has already been taken. Silakan gunakan nama ruangan lain.',
            'capacity.integer' => 'Kolom capacity WAJIB berupa angka bulat (integer)! Gak boleh pakai kata teks.',
            'capacity.min'     => 'Kapasitas ruangan minimal harus 1!',
        ])->validate();

        $room->update($validated);

        return response()->json([
            'message' => 'Room updated successfully',
            'data' => $room
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json([
                'message' => 'Room not found'
            ], 404);
        }

        $room->delete();

        return response()->json([
            'message' => 'Room deleted successfully'
        ], 200);
    }
}
