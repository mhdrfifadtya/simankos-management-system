<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('monthly_rent', 12, 2);
            $table->decimal('deposit', 12, 2)->default(0);
            $table->enum('method', ['monthly', 'yearly', 'custom'])->default('monthly');
            $table->enum('status', ['aktif', 'berakhir', 'dibatalkan'])->default('aktif');
            $table->integer('due_date_day')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
