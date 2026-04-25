<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('default_shift_id')->nullable()->constrained('shifts')->nullOnDelete();
            $table->boolean('allow_double_shift')->default(false);
        });

        Schema::table('attendance_logs', function (Blueprint $table) {
            // Drop the old unique constraint
            $table->dropUnique(['user_id', 'date']);
            
            $table->foreignId('shift_id')->nullable()->constrained('shifts')->nullOnDelete();
            $table->string('status_in')->default('on_time'); // on_time, late
            $table->string('status_out')->default('normal'); // normal, auto_checkout
        });
    }

    public function down(): void
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropForeign(['shift_id']);
            $table->dropColumn(['shift_id', 'status_in', 'status_out']);
            $table->unique(['user_id', 'date']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['default_shift_id']);
            $table->dropColumn(['default_shift_id', 'allow_double_shift']);
        });
    }
};
