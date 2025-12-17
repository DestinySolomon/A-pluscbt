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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            
            // Exam basic info
            $table->string('name'); // e.g., "JAMB UTME Mock Exam"
            $table->string('code')->unique(); // e.g., "JAMB-MOCK-2024"
            $table->text('description')->nullable();
            
            // Exam type
            $table->enum('type', ['full_jamb', 'subject_test', 'topic_test', 'mixed'])->default('subject_test');
            
            // Duration in minutes (JAMB standard: 120 minutes)
            $table->integer('duration_minutes')->default(120);
            
            // Total questions in this exam
            $table->integer('total_questions')->default(50);
            
            // Passing score (percentage)
            $table->integer('passing_score')->default(50);
            
            // Maximum attempts allowed (0 = unlimited)
            $table->integer('max_attempts')->default(0);
            
            // Shuffle options? Shuffle questions?
            $table->boolean('shuffle_questions')->default(true);
            $table->boolean('shuffle_options')->default(true);
            
            // Show results immediately?
            $table->boolean('show_results_immediately')->default(true);
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_published')->default(false);
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index('code');
            $table->index('type');
            $table->index('is_active');
            $table->index('is_published');
            $table->index(['is_active', 'is_published']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};