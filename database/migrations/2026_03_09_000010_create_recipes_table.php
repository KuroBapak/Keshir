<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->timestamps();

            $table->index('product_id');
        });

        Schema::create('recipe_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained('recipes')->onDelete('cascade');
            $table->foreignId('ingredient_id')->constrained('ingredients')->onDelete('restrict');
            $table->decimal('quantity', 12, 2); // amount of ingredient needed per 1 product
            $table->timestamps();

            $table->index('recipe_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipe_details');
        Schema::dropIfExists('recipes');
    }
};
