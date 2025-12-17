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
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            
            // Timing
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->integer('time_remaining')->nullable(); // Seconds remaining
            
            // Status
            $table->enum('status', [
                'in_progress', 
                'completed', 
                'submitted', 
                'time_expired', 
                'abandoned'
            ])->default('in_progress');
            
            // Score information
            $table->integer('total_questions')->default(0);
            $table->integer('questions_answered')->default(0);
            $table->integer('correct_answers')->default(0);
            $table->integer('wrong_answers')->default(0);
            $table->integer('score')->default(0); // Raw score
            $table->decimal('percentage', 5, 2)->default(0); // Percentage
            
            // Grading
            $table->string('grade')->nullable(); // A, B, C, D, F
            $table->boolean('is_passed')->default(false);
            
            // Metadata
            $table->json('questions_order')->nullable(); // Array of question IDs in order presented
            $table->text('notes')->nullable(); // Student notes/comments
            
            // Timestamps
            $table->timestamps();
            
            // Indexes for performance
            $table->index('user_id');
            $table->index('exam_id');
            $table->index('status');
            $table->index('started_at');
            $table->index(['user_id', 'exam_id']);
            $table->index(['user_id', 'status']);
            $table->index(['exam_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_attempts');
    }
};