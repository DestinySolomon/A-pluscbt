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
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('student_name');
            $table->string('student_course')->nullable();
            $table->integer('score_achieved')->nullable();
            $table->text('testimonial_text');
            $table->integer('rating')->default(5); // 1-5 stars
            $table->string('photo_path')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->integer('display_order')->default(0);
            $table->timestamps();
            
            // Indexes for performance
            $table->index('user_id');
            $table->index('is_approved');
            $table->index('is_featured');
            $table->index('rating');
            $table->index('display_order');
            $table->index(['is_approved', 'is_featured']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};