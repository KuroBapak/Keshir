<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('order_type', ['dine_in', 'take_away', 'booking']);
            $table->enum('source', ['pos', 'qr']); // where the order originated
            $table->string('customer_name')->nullable();
            $table->string('phone')->nullable();
            $table->foreignId('table_id')->nullable()->constrained('tables')->onDelete('set null');
            $table->unsignedInteger('people_count')->nullable();
            $table->dateTime('booking_time')->nullable();
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('discount_total', 14, 2)->default(0);
            $table->decimal('tax_total', 14, 2)->default(0);
            $table->decimal('service_total', 14, 2)->default(0);
            $table->decimal('grand_total', 14, 2)->default(0);
            $table->enum('payment_status', ['open', 'paid', 'void'])->default('open');
            $table->string('payment_method')->nullable(); // cash, qris, transfer, etc.
            $table->foreignId('discount_id')->nullable()->constrained('discounts')->onDelete('set null');
            $table->foreignId('cashier_id')->nullable()->constrained('users')->onDelete('set null');
            $table->unsignedBigInteger('cash_drawer_id')->nullable(); // linked to shift
            $table->unsignedInteger('bill_number')->nullable(); // resets per shift
            $table->timestamps();

            $table->index('payment_status');
            $table->index('source');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
