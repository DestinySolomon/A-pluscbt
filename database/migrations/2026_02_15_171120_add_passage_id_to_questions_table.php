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
        Schema::table('questions', function (Blueprint $table) {
            // Add passage_id (nullable - NULL means standalone question)
            $table->foreignId('passage_id')
                  ->nullable()
                  ->after('id')
                  ->constrained()
                  ->onDelete('cascade');
            
            // Add question number for ordering within passage
            $table->integer('question_number')
                  ->nullable()
                  ->after('question_text');
            
            // Add index for better performance
            $table->index('passage_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['passage_id']);
            $table->dropColumn(['passage_id', 'question_number']);
        });
    }
};