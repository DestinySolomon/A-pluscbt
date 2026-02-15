<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserTestimonialController extends Controller
{
    /**
     * Display a listing of approved testimonials
     */
    public function index()
    {
        // Get approved testimonials, ordered by featured first, then display order, then date
        $testimonials = Testimonial::where('is_approved', true)
            ->orderBy('is_featured', 'desc')
            ->orderBy('display_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        // Get featured testimonials for the top
        $featuredTestimonials = Testimonial::where('is_approved', true)
            ->where('is_featured', true)
            ->orderBy('display_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
        
        // Get unique subjects/courses for filtering
        $subjects = Testimonial::where('is_approved', true)
            ->whereNotNull('student_course')
            ->where('student_course', '!=', '')
            ->distinct()
            ->pluck('student_course')
            ->sort()
            ->values();
        
        // Calculate average rating
        $averageRating = Testimonial::where('is_approved', true)->avg('rating') ?? 0;
        $totalTestimonials = Testimonial::where('is_approved', true)->count();
        
        // Get user's own testimonials (for the "My Testimonials" section)
        $myTestimonials = Testimonial::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('user.testimonials.index', compact(
            'testimonials',
            'featuredTestimonials',
            'subjects',
            'averageRating',
            'totalTestimonials',
            'myTestimonials'
        ));
    }
    
    /**
     * Show the form for creating a new testimonial
     */
    public function create()
    {
        // Check if user has already submitted a testimonial today (optional limit)
        $todayTestimonials = Testimonial::where('user_id', Auth::id())
            ->whereDate('created_at', today())
            ->count();
        
        // Optional: Limit to one testimonial per day per user
        // if ($todayTestimonials >= 1) {
        //     return redirect()->route('user.testimonials.index')
        //         ->with('error', 'You can only submit one testimonial per day.');
        // }
        
        return view('user.testimonials.create');
    }
    
    /**
     * Store a newly created testimonial
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_name' => 'required|string|max:255',
            'student_course' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'testimonial_text' => 'required|string|min:50|max:1000',
            'score_achieved' => 'nullable|integer|min:120|max:400',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('testimonials', 'public');
        }
        
        // Create testimonial
        $testimonial = Testimonial::create([
            'user_id' => Auth::id(),
            'student_name' => $validated['student_name'],
            'student_course' => $validated['student_course'],
            'rating' => $validated['rating'],
            'testimonial_text' => $validated['testimonial_text'],
            'score_achieved' => $validated['score_achieved'],
            'photo_path' => $photoPath,
            'is_approved' => false, // Wait for admin approval
            'is_featured' => false,
            'display_order' => 0,
        ]);
        
        // Redirect to the newly created testimonial's show page
        return redirect()->route('user.testimonials.show', $testimonial)
            ->with('success', 'Thank you for your testimonial! It will be reviewed by our team before appearing publicly on the site.');
    }
    
    /**
     * Display the specified testimonial
     */
    public function show(Testimonial $testimonial)
    {
        // Check if user can view this testimonial
        if (!$testimonial->is_approved && $testimonial->user_id !== Auth::id()) {
            abort(404);
        }
        
        // Get related testimonials (same subject)
        $relatedTestimonials = Testimonial::where('is_approved', true)
            ->where('id', '!=', $testimonial->id)
            ->where(function($query) use ($testimonial) {
                $query->where('student_course', $testimonial->student_course)
                      ->orWhere('user_id', Auth::id()); // Include user's own testimonials
            })
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();
        
        return view('user.testimonials.show', compact('testimonial', 'relatedTestimonials'));
    }
    
    /**
     * Show the form for editing the user's own testimonial
     */
    public function edit(Testimonial $testimonial)
    {
        // User can only edit their own testimonials
        if ($testimonial->user_id !== Auth::id()) {
            abort(403, 'You can only edit your own testimonials.');
        }
        
        // Show warning if trying to edit approved testimonial
        if ($testimonial->is_approved) {
            return redirect()->route('user.testimonials.show', $testimonial)
                ->with('warning', 'Approved testimonials can be edited, but will require re-approval.');
        }
        
        return view('user.testimonials.edit', compact('testimonial'));
    }
    
    /**
     * Update the user's own testimonial
     */
    public function update(Request $request, Testimonial $testimonial)
    {
        // User can only update their own testimonials
        if ($testimonial->user_id !== Auth::id()) {
            abort(403, 'You can only update your own testimonials.');
        }
        
        $validated = $request->validate([
            'student_name' => 'required|string|max:255',
            'student_course' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'testimonial_text' => 'required|string|min:50|max:1000',
            'score_achieved' => 'nullable|integer|min:120|max:400',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_photo' => 'nullable|boolean',
        ]);
        
        // Handle photo update
        $photoPath = $testimonial->photo_path;
        
        if ($request->has('remove_photo') && $request->remove_photo) {
            // Remove existing photo
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = null;
        } elseif ($request->hasFile('photo')) {
            // Upload new photo
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('photo')->store('testimonials', 'public');
        }
        
        // Determine if approval status needs reset
        $requiresReapproval = $testimonial->is_approved && (
            $validated['student_name'] !== $testimonial->student_name ||
            $validated['student_course'] !== $testimonial->student_course ||
            $validated['rating'] != $testimonial->rating ||
            $validated['testimonial_text'] !== $testimonial->testimonial_text ||
            $validated['score_achieved'] != $testimonial->score_achieved ||
            $photoPath !== $testimonial->photo_path
        );
        
        // Update testimonial
        $testimonial->update([
            'student_name' => $validated['student_name'],
            'student_course' => $validated['student_course'],
            'rating' => $validated['rating'],
            'testimonial_text' => $validated['testimonial_text'],
            'score_achieved' => $validated['score_achieved'],
            'photo_path' => $photoPath,
            'is_approved' => $requiresReapproval ? false : $testimonial->is_approved,
        ]);
        
        $message = $requiresReapproval 
            ? 'Testimonial updated successfully! It will be reviewed again by our team.'
            : 'Testimonial updated successfully!';
        
        return redirect()->route('user.testimonials.show', $testimonial)
            ->with('success', $message);
    }
    
    /**
     * Remove the user's own testimonial
     */
    public function destroy(Testimonial $testimonial)
    {
        // User can only delete their own testimonials
        if ($testimonial->user_id !== Auth::id()) {
            abort(403, 'You can only delete your own testimonials.');
        }
        
        // Delete photo if exists
        if ($testimonial->photo_path && Storage::disk('public')->exists($testimonial->photo_path)) {
            Storage::disk('public')->delete($testimonial->photo_path);
        }
        
        $testimonial->delete();
        
        return redirect()->route('user.testimonials.index')
            ->with('success', 'Testimonial deleted successfully.');
    }
    
    /**
     * Filter testimonials by subject/course
     */
    public function filterBySubject(Request $request, $subject)
    {
        $testimonials = Testimonial::where('is_approved', true)
            ->where('student_course', $subject)
            ->orderBy('is_featured', 'desc')
            ->orderBy('display_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        $featuredTestimonials = Testimonial::where('is_approved', true)
            ->where('is_featured', true)
            ->where('student_course', $subject)
            ->orderBy('display_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
        
        // Get unique subjects/courses for filtering
        $subjects = Testimonial::where('is_approved', true)
            ->whereNotNull('student_course')
            ->where('student_course', '!=', '')
            ->distinct()
            ->pluck('student_course')
            ->sort()
            ->values();
        
        // Calculate average rating for this subject
        $averageRating = Testimonial::where('is_approved', true)
            ->where('student_course', $subject)
            ->avg('rating') ?? 0;
        
        $totalTestimonials = $testimonials->total();
        
        // Get user's own testimonials for this subject
        $myTestimonials = Testimonial::where('user_id', Auth::id())
            ->where('student_course', $subject)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('user.testimonials.index', compact(
            'testimonials',
            'featuredTestimonials',
            'subjects',
            'averageRating',
            'totalTestimonials',
            'myTestimonials'
        ))->with('selectedSubject', $subject);
    }
    
    /**
     * Display user's own testimonials
     */
    public function myTestimonials()
    {
        $testimonials = Testimonial::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('user.testimonials.my-testimonials', compact('testimonials'));
    }
}