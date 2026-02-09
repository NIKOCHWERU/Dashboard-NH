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
        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('type')->constrained('event_categories')->onDelete('set null');
        });

        // Migrate existing data
        $generalCat = DB::table('event_categories')->where('name', 'Umum')->first();
        $meetingCat = DB::table('event_categories')->where('name', 'Meeting Klien')->first();
        $deadlineCat = DB::table('event_categories')->where('name', 'Deadline Penting')->first();

        if ($generalCat) {
            DB::table('events')->where('type', 'general')->update(['category_id' => $generalCat->id]);
        }
        if ($meetingCat) {
            DB::table('events')->where('type', 'meeting')->update(['category_id' => $meetingCat->id]);
        }
        if ($deadlineCat) {
            DB::table('events')->where('type', 'deadline')->update(['category_id' => $deadlineCat->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
