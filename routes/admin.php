<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Subjects (we'll build this first)
    Route::resource('subjects', App\Http\Controllers\Admin\SubjectController::class);
    
    // Topics (comment out for now)
    Route::resource('topics', App\Http\Controllers\Admin\TopicController::class);
    
    // Questions (comment out for now)
    Route::resource('questions', App\Http\Controllers\Admin\QuestionController::class);
    
    // Exams (comment out for now)
    Route::resource('exams', App\Http\Controllers\Admin\ExamController::class);
    
    // Users (comment out for now)
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    
    // Results (comment out for now)
    Route::resource('results', App\Http\Controllers\Admin\ResultController::class);
    
    // Settings (comment out for now)
    Route::get('/settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings');
    Route::put('/settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');

    // Simple test route to verify middleware works
    Route::get('/test', function() {
        return "Admin middleware is working!";
    })->name('test');
    
    // Temporary routes for testing
    Route::get('/subjects/temp', function() {
        return "Subjects page coming soon";
    })->name('subjects.temp');
    
    Route::get('/topics/temp', function() {
        return "Topics page coming soon";
    })->name('topics.temp');
});





