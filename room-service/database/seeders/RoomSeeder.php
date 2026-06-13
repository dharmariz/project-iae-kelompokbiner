<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        Room::insert([
            [
                'room_name' => 'RK A.101',
                'room_type' => 'Classroom',
                'capacity' => 40,
                'description' => 'Ruang kelas A.101',
                'status' => 'available',
            ],
            [
                'room_name' => 'RK A.121',
                'room_type' => 'Classroom',
                'capacity' => 40,
                'description' => 'Ruang kelas A.121',
                'status' => 'available',
            ],
            [
                'room_name' => 'RK A.131',
                'room_type' => 'Classroom',
                'capacity' => 40,
                'description' => 'Ruang kelas A.131',
                'status' => 'available',
            ],
            [
                'room_name' => 'RK B.501',
                'room_type' => 'Classroom',
                'capacity' => 40,
                'description' => 'Ruang kelas B.501',
                'status' => 'available',
            ],
            [
                'room_name' => 'RK B.511',
                'room_type' => 'Classroom',
                'capacity' => 40,
                'description' => 'Ruang kelas B.511',
                'status' => 'available',
            ],
            [
                'room_name' => 'RK B.521',
                'room_type' => 'Classroom',
                'capacity' => 40,
                'description' => 'Ruang kelas B.521',
                'status' => 'available',
            ],
            [
                'room_name' => 'Aula G1',
                'room_type' => 'Hall',
                'capacity' => 100,
                'description' => 'Aula G1',
                'status' => 'available',
            ],
            [
                'room_name' => 'Aula G2',
                'room_type' => 'Hall',
                'capacity' => 100,
                'description' => 'Aula G2',
                'status' => 'available',
            ],
            [
                'room_name' => 'Sekber L2R',
                'room_type' => 'Sekber',
                'capacity' => 30,
                'description' => 'Sekber L2R',
                'status' => 'available',
            ],
            [
                'room_name' => 'Sekber L2L',
                'room_type' => 'Sekber',
                'capacity' => 30,
                'description' => 'Sekber L2L',
                'status' => 'available',
            ],
        ]);
    }
}
