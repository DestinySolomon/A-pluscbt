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
        // For MySQL/MariaDB - modify column directly
        DB::statement('ALTER TABLE questions MODIFY topic_id BIGINT UNSIGNED NULL');
        
        // Remove difficulty column
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('difficulty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add difficulty column
        Schema::table('questions', function (Blueprint $table) {
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
        });
        
        // Make topic_id not nullable again
        DB::statement('ALTER TABLE questions MODIFY topic_id BIGINT UNSIGNED NOT NULL');
    }
};