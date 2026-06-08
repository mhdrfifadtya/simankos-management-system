<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('extra_payments');
        Schema::dropIfExists('reminder_histories');
        Schema::dropIfExists('scheduler_job_logs');
        Schema::dropIfExists('scheduler_jobs');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('suggestion_boxes');
        Schema::dropIfExists('room_facilities');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('facilities');
    }

    public function down(): void
    {
        // Tidak bisa di-restore tanpa membuat ulang struktur lengkap
    }
};
