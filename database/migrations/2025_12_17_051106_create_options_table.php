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
        Schema::create('options', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to question
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            
            // Option letter (A, B, C, D, E)
            $table->char('option_letter', 1); // A, B, C, D, E
            
            // Option text
            $table->text('option_text');
            
            // Optional image for option
            $table->string('image_path')->nullable();
            
            // Is this the correct answer?
            $table->boolean('is_correct')->default(false);
            
            // Order for display (can be randomized later)
            $table->integer('order')->default(0);
            
            // Timestamps
            $table->timestamps();
            
            // Unique constraint: one letter per question
            $table->unique(['question_id', 'option_letter']);
            
            // Indexes
            $table->index('question_id');
            $table->index('is_correct');
            $table->index(['question_id', 'is_correct']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('options');
    }
};