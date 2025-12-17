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
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_attempt_id')->constrained()->onDelete('cascade');
            
            // Basic score information
            $table->integer('total_questions');
            $table->integer('questions_answered');
            $table->integer('correct_answers');
            $table->integer('wrong_answers');
            $table->integer('score'); // Raw score
            $table->decimal('percentage', 5, 2); // Percentage score
            
            // Grading
            $table->string('grade'); // A, B, C, D, F
            $table->boolean('is_passed');
            
            // Timing
            $table->integer('time_spent_seconds'); // Total time taken
            $table->decimal('average_time_per_question', 5, 2); // Avg seconds per question
            
            // Performance by subject (JSON for flexibility)
            $table->json('subject_breakdown')->nullable(); // { "MAT": { "correct": 15, "total": 20, "percentage": 75 } }
            
            // Performance by topic (JSON)
            $table->json('topic_breakdown')->nullable();
            
            // Performance by difficulty (JSON)
            $table->json('difficulty_breakdown')->nullable(); // { "easy": { "correct": 8, "total": 10 }, "medium": {...}, "hard": {...} }
            
            // Ranking information
            $table->integer('rank')->nullable(); // Rank in this exam
            $table->integer('total_participants')->nullable(); // How many took this exam
            
            // Exam session details
            $table->timestamp('exam_date');
            $table->enum('completion_status', ['completed', 'time_expired', 'submitted'])->default('completed');
            
            // Student feedback/notes
            $table->text('student_notes')->nullable();
            
            // Certificate/Report data
            $table->string('certificate_number')->nullable()->unique();
            $table->timestamp('certificate_issued_at')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Indexes for performance
            $table->index('user_id');
            $table->index('exam_id');
            $table->index('exam_attempt_id');
            $table->index('grade');
            $table->index('is_passed');
            $table->index('percentage');
            $table->index('exam_date');
            $table->index('certificate_number');
            $table->index(['user_id', 'exam_date']);
            $table->index(['exam_id', 'percentage']);
            $table->index(['user_id', 'is_passed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};