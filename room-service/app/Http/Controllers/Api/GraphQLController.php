<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;

class GraphQLController extends Controller
{
    public function handle(Request $request)
    {
        $query = $request->input('query', '');

        if (strpos($query, 'rooms') !== false) {

            $rooms = Room::all();

            preg_match('/rooms\s*\{([^}]*)\}/', $query, $matches);

            $requestedFields = [];
            if (isset($matches[1])) {
                $requestedFields = array_filter(explode(' ', trim($matches[1])));
            }

            $result = $rooms->map(function ($room) use ($requestedFields) {
                if (empty($requestedFields)) {
                    return $room;
                }

                $filtered = [];
                foreach ($requestedFields as $field) {
                    if (isset($room->$field)) {
                        $filtered[$field] = $room->$field;
                    }
                }
                return $filtered;
            });

            return response()->json([
                'data' => [
                    'rooms' => $result
                ]
            ]);
        }

        return response()->json([
            'errors' => [
                ['message' => 'Query not supported. Only "rooms" is available.']
            ]
        ], 400);
    }
}
