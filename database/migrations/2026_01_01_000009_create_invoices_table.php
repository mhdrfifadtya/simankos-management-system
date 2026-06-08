<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('rental_id')->constrained('rentals')->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->integer('year');
            $table->integer('month');
            $table->decimal('rent_amount', 12, 2);
            $table->decimal('extra_charges', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->enum('status', ['draft', 'sent', 'pending', 'partial', 'paid', 'overdue'])->default('draft');
            $table->string('snap_token')->nullable();
            $table->string('midtrans_order_id')->nullable();
            $table->string('midtrans_transaction_id')->nullable();
            $table->string('payment_gateway')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
