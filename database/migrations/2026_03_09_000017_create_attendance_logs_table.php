<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('date');
            $table->dateTime('check_in')->nullable();
            $table->dateTime('check_out')->nullable();
            $table->enum('source', ['web', 'iot'])->default('web');
            $table->timestamps();

            $table->index('user_id');
            $table->index('date');
            $table->unique(['user_id', 'date']); // one attendance record per user per day
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
