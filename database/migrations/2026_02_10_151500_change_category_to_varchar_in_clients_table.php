<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change category from ENUM to VARCHAR to support longer category names
        DB::statement("ALTER TABLE clients MODIFY COLUMN category VARCHAR(100) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to ENUM (note: this will fail if there are values other than the enum values)
        DB::statement("ALTER TABLE clients MODIFY COLUMN category ENUM('retainer', 'perorangan') NOT NULL");
    }
};
