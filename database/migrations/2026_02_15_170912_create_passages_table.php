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
        Schema::create('passages', function (Blueprint $table) {
            $table->id();
            
            // Basic passage info
            $table->string('title')->nullable();
            $table->text('content'); // The passage text
            $table->string('image_path')->nullable();
            $table->string('source')->nullable(); // e.g., "USE OF ENGLISH 1979"
            $table->text('instruction')->nullable(); // e.g., "Read each passage and answer the questions that follow"
            
            // Foreign keys
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('topic_id')->nullable()->constrained()->onDelete('set null');
            
            // For ordering multiple passages in an exam
            $table->integer('order')->default(0);
            
            // Status
            $table->boolean('is_active')->default(true);
            
            // Metadata for flexibility (store passage type, etc.)
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index('subject_id');
            $table->index('topic_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('passages');
    }
};