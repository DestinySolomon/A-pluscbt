<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::approved()
            ->orderBy('display_order')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $stats = [
            'total' => Testimonial::approved()->count(),
            'average_rating' => Testimonial::approved()->avg('rating') ?? 0,
            'featured_count' => Testimonial::approved()->featured()->count(),
        ];

        return view('testimonials', compact('testimonials', 'stats'));
    }

    // Remove or comment out other methods for now
    // public function show($id) { ... }
    // public function submitForm() { ... }
    // public function store(Request $request) { ... }
}