<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('total_stock', 12, 2)->default(0);
            $table->string('unit'); // base unit: gram, ml, pcs
            $table->decimal('content_per_pack', 12, 2)->nullable(); // if bought in packs, how many base units per pack
            $table->decimal('minimum_stock', 12, 2)->default(0);
            $table->timestamps();

            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
