<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Laravel\Facades\Image;

class TestimonialController extends Controller
{
    public function index(Request $request)
    {
        $query = Testimonial::query();
        
        // Apply filters
        if ($request->filled('status')) {
            if ($request->status == 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status == 'pending') {
                $query->where('is_approved', false);
            } elseif ($request->status == 'featured') {
                $query->where('is_featured', true);
            }
        }
        
        if ($request->filled('rating')) {
            $query->where('rating', '>=', $request->rating);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                  ->orWhere('student_course', 'like', "%{$search}%")
                  ->orWhere('testimonial_text', 'like', "%{$search}%");
            });
        }
        
        // Apply sorting
        $sortBy = $request->get('sort_by', 'newest');
        switch ($sortBy) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'rating_high':
                $query->orderBy('rating', 'desc');
                break;
            case 'rating_low':
                $query->orderBy('rating', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('student_name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('student_name', 'desc');
                break;
            case 'order':
                $query->orderBy('display_order')->orderBy('created_at', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }
        
        $testimonials = $query->paginate(20)->withQueryString();
        
        // Get statistics
        $stats = [
            'total' => Testimonial::count(),
            'approved' => Testimonial::where('is_approved', true)->count(),
            'pending' => Testimonial::where('is_approved', false)->count(),
            'featured' => Testimonial::where('is_featured', true)->count(),
            'average_rating' => Testimonial::where('is_approved', true)->avg('rating') ?? 0,
        ];
        
        return view('admin.testimonials.index', compact('testimonials', 'stats'));
    }

    public function create()
    {
        $users = User::where('role', 'student')->orderBy('name')->get();
        return view('admin.testimonials.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'student_name' => 'required|string|max:255',
            'student_course' => 'nullable|string|max:255',
            'score_achieved' => 'nullable|integer|min:0|max:100',
            'testimonial_text' => 'required|string|min:10|max:1000',
            'rating' => 'required|integer|min:1|max:5',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_approved' => 'boolean',
            'is_featured' => 'boolean',
        ]);
        
        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photoPath = $this->uploadPhoto($request->file('photo'));
            $validated['photo_path'] = $photoPath;
        }
        
        // Get next display order
        $validated['display_order'] = Testimonial::max('display_order') + 1;
        
        Testimonial::create($validated);
        
        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimonial created successfully.');
    }

    public function show(Testimonial $testimonial)
    {
        return view('admin.testimonials.show', compact('testimonial'));
    }

    public function edit(Testimonial $testimonial)
    {
        $users = User::where('role', 'student')->orderBy('name')->get();
        return view('admin.testimonials.edit', compact('testimonial', 'users'));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'student_name' => 'required|string|max:255',
            'student_course' => 'nullable|string|max:255',
            'score_achieved' => 'nullable|integer|min:0|max:100',
            'testimonial_text' => 'required|string|min:10|max:1000',
            'rating' => 'required|integer|min:1|max:5',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_approved' => 'boolean',
            'is_featured' => 'boolean',
            'display_order' => 'integer',
        ]);
        
        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($testimonial->photo_path && Storage::exists($testimonial->photo_path)) {
                Storage::delete($testimonial->photo_path);
            }
            
            $photoPath = $this->uploadPhoto($request->file('photo'));
            $validated['photo_path'] = $photoPath;
        }
        
        // Remove photo if requested
        if ($request->has('remove_photo') && $testimonial->photo_path) {
            if (Storage::exists($testimonial->photo_path)) {
                Storage::delete($testimonial->photo_path);
            }
            $validated['photo_path'] = null;
        }
        
        $testimonial->update($validated);
        
        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimonial updated successfully.');
    }

    public function destroy(Testimonial $testimonial)
    {
        // Delete photo if exists
        if ($testimonial->photo_path && Storage::exists($testimonial->photo_path)) {
            Storage::delete($testimonial->photo_path);
        }
        
        $testimonial->delete();
        
        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimonial deleted successfully.');
    }

    public function approve(Testimonial $testimonial)
    {
        $testimonial->update(['is_approved' => true]);
        
        return redirect()->back()->with('success', 'Testimonial approved successfully.');
    }

    public function toggleFeature(Testimonial $testimonial)
    {
        $testimonial->update(['is_featured' => !$testimonial->is_featured]);
        
        $status = $testimonial->is_featured ? 'featured' : 'unfeatured';
        return redirect()->back()->with('success', "Testimonial {$status} successfully.");
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:testimonials,id',
        ]);
        
        foreach ($request->order as $index => $id) {
            Testimonial::where('id', $id)->update(['display_order' => $index + 1]);
        }
        
        return response()->json(['success' => true]);
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,delete,feature,unfeature',
            'testimonials' => 'required|array',
            'testimonials.*' => 'exists:testimonials,id',
        ]);
        
        $testimonials = Testimonial::whereIn('id', $request->testimonials);
        
        switch ($request->action) {
            case 'approve':
                $testimonials->update(['is_approved' => true]);
                $message = 'Selected testimonials approved successfully.';
                break;
                
            case 'delete':
                // Delete photos first
                $photos = $testimonials->pluck('photo_path')->filter();
                foreach ($photos as $photo) {
                    if ($photo && Storage::exists($photo)) {
                        Storage::delete($photo);
                    }
                }
                $testimonials->delete();
                $message = 'Selected testimonials deleted successfully.';
                break;
                
            case 'feature':
                $testimonials->update(['is_featured' => true]);
                $message = 'Selected testimonials marked as featured.';
                break;
                
            case 'unfeature':
                $testimonials->update(['is_featured' => false]);
                $message = 'Selected testimonials unfeatured successfully.';
                break;
        }
        
        return redirect()->back()->with('success', $message);
    }
    
    /**
     * Upload and process testimonial photo.
     */
    private function uploadPhoto($photo)
    {
        $path = $photo->store('testimonials', 'public');
        
        // Resize image for optimization
        $image = Image::make(storage_path('app/public/' . $path));
        $image->fit(400, 400, function ($constraint) {
            $constraint->upsize();
        });
        $image->save();
        
        return $path;
    }
}