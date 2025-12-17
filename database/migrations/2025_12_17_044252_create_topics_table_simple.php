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
        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to subjects
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            
            // Topic details - SIMPLE
            $table->string('name'); // e.g., "Algebra", "Comprehension"
            $table->text('description')->nullable();
            
            // JAMB syllabus reference (optional)
            $table->string('syllabus_ref')->nullable();
            $table->integer('syllabus_order')->default(0);
            
            // Status
            $table->boolean('is_active')->default(true);
            
            // Timestamps
            $table->timestamps();
            
            // Simple indexes
            $table->index('subject_id');
            $table->index('is_active');
            $table->index('syllabus_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topics');
    }
};