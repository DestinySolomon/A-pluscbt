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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('topic_id')->constrained()->onDelete('cascade');
            
            // Question text (supports HTML for formatting)
            $table->text('question_text');
            
            // Optional image for question
            $table->string('image_path')->nullable();
            
            // Difficulty level (for question randomization)
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            
            // Marks/score for this question
            $table->integer('marks')->default(1);
            
            // Time estimation in seconds (for exam timing)
            $table->integer('time_estimate')->default(60); // 1 minute per question
            
            // Explanation of answer (for learning)
            $table->text('explanation')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            
            // Metadata
            $table->integer('times_answered')->default(0);
            $table->integer('times_correct')->default(0);
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index('subject_id');
            $table->index('topic_id');
            $table->index('difficulty');
            $table->index('is_active');
            $table->index(['subject_id', 'topic_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};