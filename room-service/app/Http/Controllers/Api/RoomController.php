<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Room::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $room = Room::create([
            'room_name' => $request->room_name,
            'room_type' => $request->room_type,
            'capacity' => $request->capacity,
            'description' => $request->description,
            'status' => $request->status,
        ]);

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

        return response()->json($room);
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

        $room->update([
            'room_name' => $request->room_name,
            'room_type' => $request->room_type,
            'capacity' => $request->capacity,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Room updated successfully',
            'data' => $room
        ]);
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
        ]);
    }
}
