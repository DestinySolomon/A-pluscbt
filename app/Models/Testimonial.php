<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_name',
        'student_course',
        'score_achieved',
        'testimonial_text',
        'rating',
        'photo_path',
        'is_approved',
        'is_featured',
        'display_order',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'is_featured' => 'boolean',
        'rating' => 'integer',
        'score_achieved' => 'integer',
        'display_order' => 'integer',
    ];

    /**
     * Get the user this testimonial belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for approved testimonials.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope for pending testimonials.
     */
    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    /**
     * Scope for featured testimonials.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope ordered by display order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('created_at', 'desc');
    }

    /**
     * Scope for high rating testimonials (4-5 stars).
     */
    public function scopeHighRating($query)
    {
        return $query->where('rating', '>=', 4);
    }

    /**
     * Get the photo URL.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo_path) {
            return null;
        }
        
        if (str_starts_with($this->photo_path, 'http')) {
            return $this->photo_path;
        }
        
        return asset('storage/' . $this->photo_path);
    }

    /**
     * Get star rating as HTML.
     */
    public function getStarRatingAttribute(): string
    {
        $stars = '';
        $fullStars = floor($this->rating);
        $hasHalfStar = $this->rating - $fullStars >= 0.5;
        
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $fullStars) {
                $stars .= '<i class="ri-star-fill text-warning"></i>';
            } elseif ($i == $fullStars + 1 && $hasHalfStar) {
                $stars .= '<i class="ri-star-half-line text-warning"></i>';
            } else {
                $stars .= '<i class="ri-star-line text-warning"></i>';
            }
        }
        
        return $stars;
    }

    /**
     * Get short testimonial text (truncated).
     */
    public function getShortTestimonialAttribute(): string
    {
        return \Illuminate\Support\Str::limit($this->testimonial_text, 150);
    }

    /**
     * Check if testimonial has a student photo.
     */
    public function hasPhoto(): bool
    {
        return !empty($this->photo_path);
    }

    /**
     * Get the student initials for avatar.
     */
    public function getInitialsAttribute(): string
    {
        $names = explode(' ', $this->student_name);
        $initials = '';
        
        foreach ($names as $name) {
            if (!empty($name)) {
                $initials .= strtoupper($name[0]);
            }
        }
        
        return substr($initials, 0, 2);
    }
}