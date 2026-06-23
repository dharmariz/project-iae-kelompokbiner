<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;

class GraphQLController extends Controller
{
    public function handle(Request $request)
    {
        // 1. Ambil string query dari body Postman
        $query = $request->input('query', '');

        // 2. Cek apakah query meminta data "reservations"
        if (strpos($query, 'reservations') !== false) {

            // Ambil semua data dari DB
            $reservations = Reservation::all();

            // 3. Ekstrak field yang diminta di dalam kurung kurawal { ... }
            // Contoh: { reservations { id status start_datetime } }
            preg_match('/reservations\s*\{([^}]*)\}/', $query, $matches);

            $requestedFields = [];
            if (isset($matches[1])) {
                // Pecah string berdasarkan spasi jadi array field
                $requestedFields = array_filter(explode(' ', trim($matches[1])));
            }

            // 4. Filter data: hanya kembalikan field yang diminta client
            $result = $reservations->map(function ($reservation) use ($requestedFields) {
                if (empty($requestedFields)) {
                    return $reservation; // Kembalikan semua jika tidak specify field
                }

                $filtered = [];
                foreach ($requestedFields as $field) {
                    // Hanya ambil field yang ada di tabel database
                    if (isset($reservation->$field)) {
                        $filtered[$field] = $reservation->$field;
                    }
                }
                return $filtered;
            });

            // 5. Return sesuai standar format GraphQL
            return response()->json([
                'data' => [
                    'reservations' => $result
                ]
            ]);
        }

        // Jika query tidak dikenali
        return response()->json([
            'errors' => [
                ['message' => 'Query not supported. Only "reservations" is available.']
            ]
        ], 400);
    }
}
