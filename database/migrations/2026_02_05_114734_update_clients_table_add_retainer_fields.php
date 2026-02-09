<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->date('retainer_contract_end')->nullable()->after('category');
            $table->enum('status', ['active', 'inactive', 'pending'])->default('active')->after('retainer_contract_end');
            $table->foreignId('pic_id')->nullable()->constrained('users')->onDelete('set null')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign(['pic_id']);
            $table->dropColumn(['retainer_contract_end', 'status', 'pic_id']);
        });
    }
};
