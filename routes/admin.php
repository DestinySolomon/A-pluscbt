<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\DashboardController;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Subjects (we'll build this first)
    // Route::resource('subjects', Admin\SubjectController::class);
    
    // Topics (comment out for now)
    // Route::resource('topics', Admin\TopicController::class);
    
    // Questions (comment out for now)
    // Route::resource('questions', Admin\QuestionController::class);
    
    // Exams (comment out for now)
    // Route::resource('exams', Admin\ExamController::class);
    
    // Users (comment out for now)
    // Route::resource('users', Admin\UserController::class);
    
    // Results (comment out for now)
    // Route::resource('results', Admin\ResultController::class);
    
    // Settings (comment out for now)
    // Route::get('/settings', [Admin\SettingController::class, 'index'])->name('settings');
    // Route::put('/settings', [Admin\SettingController::class, 'update'])->name('settings.update');
    





Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard - FIXED: Using fully qualified controller
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Simple test route to verify middleware works
    Route::get('/test', function() {
        return "Admin middleware is working!";
    })->name('test');
});

    // Temporary routes for testing
    Route::get('/subjects/temp', function() {
        return "Subjects page coming soon";
    })->name('subjects.temp');
    
    Route::get('/topics/temp', function() {
        return "Topics page coming soon";
    })->name('topics.temp');
});





