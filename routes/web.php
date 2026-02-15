<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\UserExamController;
use App\Http\Controllers\User\UserResultController;
use App\Http\Controllers\User\UserTestimonialController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home', [HomeController::class, 'index']);

// Testimonials Routes
Route::get('/testimonials', [TestimonialController::class, 'index'])->name('testimonials.all');
Route::get('/testimonials/{id}', [TestimonialController::class, 'show'])->name('testimonials.show');
Route::get('/testimonials/submit', [TestimonialController::class, 'submitForm'])->name('testimonials.submit');
Route::post('/testimonials', [TestimonialController::class, 'store'])->name('testimonials.store');

// Test route (remove after testing)
Route::get('/test-subjects', function() {
    return \App\Models\Subject::all();
});

// User Dashboard Routes (Protected by auth)
Route::middleware(['auth', 'verified'])->group(function () {
    // Main dashboard (using the default Breeze route)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');


// Add this route in the user group (around line 60-70 of your web.php)
Route::get('/study-materials', function () {
    return view('user.materials.index');
})->name('study.materials');

    // Instructions
    Route::get('/instructions', function () {
        return view('user.instructions');
    })->name('instructions');
    
    // User Dashboard Routes
    Route::prefix('user')->name('user.')->group(function () {
        // Dashboard stats (AJAX)
        Route::get('/dashboard/stats', [UserDashboardController::class, 'getStats'])->name('dashboard.stats');
        Route::get('/exams/recent', [UserDashboardController::class, 'getRecentExams'])->name('exams.recent');
        Route::get('/exams/recommended', [UserDashboardController::class, 'getRecommendedExams'])->name('exams.recommended');
        
        // Exams
        Route::get('/exams', [UserExamController::class, 'index'])->name('exams.index');
        Route::get('/exams/{exam}', [UserExamController::class, 'show'])->name('exams.show');
        Route::post('/exams/{exam}/start', [UserExamController::class, 'start'])->name('exams.start');
        Route::get('/exams/{exam}/instructions', [UserExamController::class, 'instructions'])->name('exams.instructions');
        Route::get('/exams/{exam}/take', [UserExamController::class, 'take'])->name('exams.take');
        Route::post('/exams/{exam}/submit', [UserExamController::class, 'submit'])->name('exams.submit');
        Route::post('/exams/{exam}/save-answer', [UserExamController::class, 'saveAnswer'])->name('exams.save-answer');
        Route::post('/exams/{exam}/save-time', [UserExamController::class, 'saveTime'])->name('exams.save-time');
        Route::get('/exams/{exam}/get-question', [UserExamController::class, 'getQuestion'])->name('exams.get-question');
        
        // Testimonials Routes
        Route::prefix('testimonials')->name('testimonials.')->group(function () {
            // List routes (no parameters)
            Route::get('/', [UserTestimonialController::class, 'index'])->name('index');
            
            // Creation routes
            Route::get('/create', [UserTestimonialController::class, 'create'])->name('create');
            Route::post('/', [UserTestimonialController::class, 'store'])->name('store');
            
            // Specific named routes (MUST come before dynamic parameter routes)
            Route::get('/my-testimonials', [UserTestimonialController::class, 'myTestimonials'])->name('my-testimonials');
            Route::get('/filter/subject/{subject}', [UserTestimonialController::class, 'filterBySubject'])->name('filter.subject');
            
            // Dynamic parameter routes (MUST come last)
            Route::get('/{testimonial}', [UserTestimonialController::class, 'show'])->name('show');
            Route::get('/{testimonial}/edit', [UserTestimonialController::class, 'edit'])->name('edit');
            Route::put('/{testimonial}', [UserTestimonialController::class, 'update'])->name('update');
            Route::delete('/{testimonial}', [UserTestimonialController::class, 'destroy'])->name('destroy');
        });

        // Results
        Route::get('/results', [UserResultController::class, 'index'])->name('results.index');
        Route::get('/results/{attempt}', [UserResultController::class, 'show'])->name('results.show');
        Route::get('/results/{attempt}/review', [UserResultController::class, 'review'])->name('results.review');
    });
    
   // Profile Routes (from Breeze - keep these)
Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto'])->name('profile.photo.delete');
});

require __DIR__.'/auth.php';