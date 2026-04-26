<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite doesn't support ALTER COLUMN / MODIFY COLUMN for enum changes
        // We need to recreate the table with the new enum values
        
        // Convert any existing 'approved' to 'confirmed', 'rejected' to 'cancelled'
        DB::table('bookings')->where('status', 'approved')->update(['status' => 'confirmed']);
        DB::table('bookings')->where('status', 'rejected')->update(['status' => 'cancelled']);

        // For SQLite: drop the check constraint by recreating the column
        // Since SQLite stores enum as text, we just need to ensure our app uses the right values
        // The actual constraint is enforced at application level in Laravel
        // No schema change needed for SQLite - it stores enum as string/text
    }

    public function down(): void
    {
        DB::table('bookings')->where('status', 'confirmed')->update(['status' => 'approved']);
        DB::table('bookings')->where('status', 'cancelled')->update(['status' => 'rejected']);
    }
};
