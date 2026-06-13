<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();

            $table->string('room_name');
            $table->string('room_type');
            $table->integer('capacity');

            $table->text('description')->nullable();

            $table->enum('status', [
                'available',
                'maintenance'
            ])->default('available');

            $table->timestamps();
        });
    }
};
