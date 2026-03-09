<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_detail_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_detail_id')->constrained('transaction_details')->onDelete('cascade');
            $table->foreignId('product_addon_id')->constrained('product_addons')->onDelete('restrict');
            $table->decimal('price', 12, 2); // price snapshot at time of order
            $table->timestamps();

            $table->index('transaction_detail_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_detail_addons');
    }
};
