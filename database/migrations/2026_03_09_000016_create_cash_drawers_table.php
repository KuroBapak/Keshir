<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_drawers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->dateTime('opened_at');
            $table->dateTime('closed_at')->nullable();
            $table->decimal('starting_cash', 14, 2);
            $table->decimal('ending_cash', 14, 2)->nullable(); // physical cash counted
            $table->decimal('expected_ending_cash', 14, 2)->nullable(); // system-calculated
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
        });

        Schema::create('cash_drawer_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_drawer_id')->constrained('cash_drawers')->onDelete('cascade');
            $table->enum('type', ['in', 'out']); // cash in (sale) or cash out (refund/petty)
            $table->decimal('amount', 14, 2);
            $table->string('description')->nullable();
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->onDelete('set null');
            $table->timestamps();

            $table->index('cash_drawer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_drawer_logs');
        Schema::dropIfExists('cash_drawers');
    }
};
