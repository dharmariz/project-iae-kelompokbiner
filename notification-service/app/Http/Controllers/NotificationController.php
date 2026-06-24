<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // GET /notifications
    public function index()
    {
        return response()->json(
            Notification::latest()->get()
        );
    }

    // PUT /notifications/{id} (Tandai sudah dibaca)
    public function update(Request $request, int $id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['status' => 'read']);

        return response()->json(['message' => 'Notifikasi ditandai sudah dibaca', 'data' => $notification]);
    }
}
