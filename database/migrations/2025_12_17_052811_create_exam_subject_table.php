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
        Schema::create('exam_subject', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            
            // How many questions from this subject
            $table->integer('question_count')->default(0);
            
            // Specific topics (if any)
            $table->json('topic_ids')->nullable(); // Array of topic IDs
            
            // Difficulty distribution
            $table->json('difficulty_distribution')->nullable(); // e.g., {"easy": 30, "medium": 50, "hard": 20}
            
            // Timestamps
            $table->timestamps();
            
            // Unique constraint
            $table->unique(['exam_id', 'subject_id']);
            
            // Indexes
            $table->index('exam_id');
            $table->index('subject_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_subject');
    }
};