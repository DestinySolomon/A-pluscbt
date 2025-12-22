<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TestimonialController;


Route::get('/', [HomeController::class, 'index'])->name('home');



Route::get('/home', [HomeController::class, 'index']);
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';



Route::get('/test-subjects', function() {
    return \App\Models\Subject::all();



    
});

// Testimonials Routes
Route::get('/testimonials', [TestimonialController::class, 'index'])->name('testimonials.all');
Route::get('/testimonials/{id}', [TestimonialController::class, 'show'])->name('testimonials.show');
Route::get('/testimonials/submit', [TestimonialController::class, 'submitForm'])->name('testimonials.submit');
Route::post('/testimonials', [TestimonialController::class, 'store'])->name('testimonials.store');