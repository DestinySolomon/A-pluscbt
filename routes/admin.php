<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TopicController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ResultController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\AttemptController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PassageController;

Route::middleware(['auth', 'admin'])->group(function () {
   
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Subjects
    Route::resource('subjects', SubjectController::class);
    
    // Topics
    Route::resource('topics', TopicController::class);
    Route::post('/topics/import', [TopicController::class, 'import'])->name('topics.import');
    Route::get('/topics/export', [TopicController::class, 'export'])->name('topics.export');
    Route::get('/topics/by-subject/{subjectId}', [TopicController::class, 'bySubject'])->name('topics.by-subject');
    
    // Questions
    Route::resource('questions', QuestionController::class);
    Route::post('/questions/bulk-action', [QuestionController::class, 'bulkAction'])->name('questions.bulk-action');
    Route::post('/questions/bulk-import', [QuestionController::class, 'import'])->name('questions.import');
    Route::get('/questions/bulk-export', [QuestionController::class, 'export'])->name('questions.export');
    Route::get('/questions/get-topics-by-subject/{subjectId}', [QuestionController::class, 'getTopicsBySubject'])->name('questions.get-topics-by-subject');
    Route::post('/questions/upload-image', [QuestionController::class, 'uploadImage'])->name('questions.upload-image');

    
     // Additional Question Routes
     Route::get('/questions/get-passages-by-subject/{subjectId}', [QuestionController::class, 'getPassagesBySubject'])->name('questions.get-passages-by-subject');
     Route::get('/questions/get-next-question-number/{passageId}', [QuestionController::class, 'getNextQuestionNumber'])->name('questions.get-next-question-number');

    // Exams
    Route::resource('exams', ExamController::class);
    Route::post('/exams/{exam}/publish', [ExamController::class, 'publish'])->name('exams.publish');
    Route::post('/exams/{exam}/unpublish', [ExamController::class, 'unpublish'])->name('exams.unpublish');
    Route::get('/exams/{exam}/preview', [ExamController::class, 'preview'])->name('exams.preview');
    Route::get('/exams/{exam}/stats', [ExamController::class, 'stats'])->name('exams.stats');
    Route::get('/exams/{exam}/export', [ExamController::class, 'export'])->name('exams.export');
    Route::post('/exams/{exam}/duplicate', [ExamController::class, 'duplicate'])->name('exams.duplicate');
    Route::post('/exams/bulk-import', [ExamController::class, 'import'])->name('exams.import');
    Route::get('/exams/bulk-export', [ExamController::class, 'exportAll'])->name('exams.export-all');

    // Exam Attempts
    Route::get('/attempts', [AttemptController::class, 'index'])->name('attempts.index');
    Route::get('/attempts/{attempt}', [AttemptController::class, 'show'])->name('attempts.show');
    Route::delete('/attempts/{attempt}', [AttemptController::class, 'destroy'])->name('attempts.destroy');
    Route::post('/attempts/{attempt}/reset', [AttemptController::class, 'reset'])->name('attempts.reset');
    Route::post('/attempts/bulk-delete', [AttemptController::class, 'bulkDelete'])->name('attempts.bulk-delete');
    
    // Users
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('/users/{user}/make-admin', [UserController::class, 'makeAdmin'])->name('users.make-admin');
    Route::post('/users/{user}/remove-admin', [UserController::class, 'removeAdmin'])->name('users.remove-admin');
    Route::post('/users/bulk-action', [UserController::class, 'bulkAction'])->name('users.bulk-action');
    
    // Results
   
    Route::get('/results/analytics/overview', [ResultController::class, 'analyticsOverview'])->name('results.analytics');
    Route::get('/results/analytics/subject-performance', [ResultController::class, 'subjectPerformance'])->name('results.subject-performance');


    Route::get('/results/top-performers', [ResultController::class, 'topPerformers'])->name('results.top-performers');

    Route::get('/results/export', [ResultController::class, 'export'])->name('results.export');
    Route::post('/results/{result}/issue-certificate', [ResultController::class, 'issueCertificate'])->name('results.issue-certificate');
    Route::get('/results/{result}/certificate', [ResultController::class, 'viewCertificate'])->name('results.certificate');
    
     Route::resource('results', ResultController::class);



     // Testimonials Management
Route::resource('testimonials', TestimonialController::class);
Route::post('/testimonials/{testimonial}/approve', [TestimonialController::class, 'approve'])->name('testimonials.approve');
Route::post('/testimonials/{testimonial}/feature', [TestimonialController::class, 'toggleFeature'])->name('testimonials.feature');
Route::post('/testimonials/reorder', [TestimonialController::class, 'reorder'])->name('testimonials.reorder');
Route::post('/testimonials/bulk-action', [TestimonialController::class, 'bulkAction'])->name('testimonials.bulk-action');

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');



    // Profile Settings - Add these routes after your existing settings routes
Route::prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('profile');
    Route::put('/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/image', [ProfileController::class, 'updateProfileImage'])->name('profile.image');
    Route::delete('/image', [ProfileController::class, 'removeProfileImage'])->name('profile.image.remove');
    Route::put('/social', [ProfileController::class, 'updateSocialLinks'])->name('profile.social');
    Route::put('/notifications', [ProfileController::class, 'updateNotifications'])->name('profile.notifications');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
 
});



// Passages Management
Route::prefix('passages')->name('passages.')->group(function () {
    Route::get('/', [PassageController::class, 'index'])->name('index');
    Route::get('/create', [PassageController::class, 'create'])->name('create');
    Route::post('/', [PassageController::class, 'store'])->name('store');
    Route::get('/{passage}', [PassageController::class, 'show'])->name('show');
    Route::get('/{passage}/edit', [PassageController::class, 'edit'])->name('edit');
    Route::put('/{passage}', [PassageController::class, 'update'])->name('update');
    Route::delete('/{passage}', [PassageController::class, 'destroy'])->name('destroy');
    
    // Custom routes
    Route::post('/{passage}/toggle-status', [PassageController::class, 'toggleStatus'])->name('toggle-status');
    Route::post('/{passage}/add-question', [PassageController::class, 'addQuestion'])->name('add-question');
    Route::get('/{passage}/next-question-number', [PassageController::class, 'getNextQuestionNumber'])->name('next-question-number');
    
    // AJAX routes
    Route::get('/get-topics-by-subject/{subjectId}', [PassageController::class, 'getTopicsBySubject'])->name('get-topics-by-subject');
    
    // Bulk actions
    Route::post('/bulk-action', [PassageController::class, 'bulkAction'])->name('bulk-action');
});



// Notifications
Route::prefix('notifications')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::put('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::put('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/clear-read', [NotificationController::class, 'clearRead'])->name('notifications.clear-read');
    Route::get('/statistics', [NotificationController::class, 'statistics'])->name('notifications.statistics');
});
});