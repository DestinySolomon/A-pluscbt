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
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('exam_attempt_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->foreignId('selected_option_id')->constrained('options')->onDelete('cascade');
            
            // Student's selected option letter (A, B, C, D, E)
            $table->char('selected_option', 1);
            
            // Is this answer correct?
            $table->boolean('is_correct')->default(false);
            
            // Timing information
            $table->timestamp('answered_at')->useCurrent();
            $table->integer('time_spent_seconds')->default(0); // How long on this question
            
            // Question was marked for review?
            $table->boolean('marked_for_review')->default(false);
            
            // Question was skipped?
            $table->boolean('skipped')->default(false);
            
            // Timestamps
            $table->timestamps();
            
            // Unique constraint: one answer per question per attempt
            $table->unique(['exam_attempt_id', 'question_id']);
            
            // Indexes for performance
            $table->index('exam_attempt_id');
            $table->index('question_id');
            $table->index('selected_option_id');
            $table->index('is_correct');
            $table->index('answered_at');
            $table->index(['exam_attempt_id', 'is_correct']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};