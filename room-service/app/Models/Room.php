<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'rooms';

    // Field yang diizinkan untuk diisi secara massal
    protected $fillable = [
        'room_name',
        'room_type',
        'capacity',
        'description',
        'status'
    ];

    // Mengonversi tipe data secara otomatis saat keluar dari Eloquent
    protected $casts = [
        'capacity' => 'integer',
    ];
}
