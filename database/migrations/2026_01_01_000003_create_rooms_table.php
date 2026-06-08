<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number');
            $table->enum('type', ['standard', 'deluxe', 'vip'])->default('standard');
            $table->decimal('price', 12, 2);
            $table->decimal('area', 8, 2)->nullable();
            $table->integer('floor')->nullable();
            $table->enum('status', ['tersedia', 'terisi', 'maintenance'])->default('tersedia');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
