<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class GraphQLController extends Controller
{
    public function handle(Request $request)
    {
        $query = $request->input('query', '');

        if (strpos($query, 'users') !== false) {

            $users = User::all();

            preg_match('/users\s*\{([^}]*)\}/', $query, $matches);

            $requestedFields = [];
            if (isset($matches[1])) {
                $requestedFields = array_filter(explode(' ', trim($matches[1])));
            }

            $result = $users->map(function ($user) use ($requestedFields) {
                if (empty($requestedFields)) {
                    return $user;
                }

                $filtered = [];
                foreach ($requestedFields as $field) {
                    if (isset($user->$field)) {
                        $filtered[$field] = $user->$field;
                    }
                }
                return $filtered;
            });

            return response()->json([
                'data' => [
                    'users' => $result
                ]
            ]);
        }

        return response()->json([
            'errors' => [
                ['message' => 'Query not supported. Only "users" is available.']
            ]
        ], 400);
    }
}
