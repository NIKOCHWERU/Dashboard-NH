<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $create) {
            $create->id();
            $create->foreignId('user_id')->constrained()->onDelete('cascade');
            $create->string('title');
            $create->text('description')->nullable();
            $create->enum('priority', ['Q1', 'Q2', 'Q3'])->default('Q2');
            $create->enum('status', ['pending', 'completed'])->default('pending');
            $create->date('due_date')->nullable();
            $create->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
