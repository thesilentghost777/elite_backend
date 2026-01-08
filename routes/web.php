<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PackController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\ChapterController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\QuizController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\CorrespondenceController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Admin Panel
|--------------------------------------------------------------------------
*/

Route::get('project_elite_tfs237', function () {
    return view('welcome');
});

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Users Management
    Route::resource('users', UserController::class)->except(['create', 'store']);
    Route::post('users/{user}/add-points', [UserController::class, 'addPoints'])->name('users.add-points');

    Route::resource('categories', CategoryController::class);

    // Packs & Courses Management
    Route::resource('packs', PackController::class);
    Route::post('packs/{pack}/toggle-active', [PackController::class, 'toggleActive'])->name('packs.toggle-active');

    // Modules
    Route::get('packs/{pack}/modules/create', [ModuleController::class, 'create'])->name('modules.create');
    Route::post('packs/{pack}/modules', [ModuleController::class, 'store'])->name('modules.store');
    Route::get('modules/{module}/edit', [ModuleController::class, 'edit'])->name('modules.edit');
    Route::put('modules/{module}', [ModuleController::class, 'update'])->name('modules.update');
    Route::delete('modules/{module}', [ModuleController::class, 'destroy'])->name('modules.destroy');

    // Chapters
    Route::get('modules/{module}/chapters/create', [ChapterController::class, 'create'])->name('chapters.create');
    Route::post('modules/{module}/chapters', [ChapterController::class, 'store'])->name('chapters.store');
    Route::get('chapters/{chapter}', [ChapterController::class, 'show'])->name('chapters.show');
    Route::get('chapters/{chapter}/edit', [ChapterController::class, 'edit'])->name('chapters.edit');
    Route::put('chapters/{chapter}', [ChapterController::class, 'update'])->name('chapters.update');
    Route::delete('chapters/{chapter}', [ChapterController::class, 'destroy'])->name('chapters.destroy');

    // Lessons
    Route::get('chapters/{chapter}/lessons/create', [LessonController::class, 'create'])->name('lessons.create');
    Route::post('chapters/{chapter}/lessons', [LessonController::class, 'store'])->name('lessons.store');
    Route::get('lessons/{lesson}/edit', [LessonController::class, 'edit'])->name('lessons.edit');
    Route::put('lessons/{lesson}', [LessonController::class, 'update'])->name('lessons.update');
    Route::delete('lessons/{lesson}', [LessonController::class, 'destroy'])->name('lessons.destroy');

    // Quizzes
    Route::get('chapters/{chapter}/quiz/create', [QuizController::class, 'create'])->name('quizzes.create');
    Route::post('chapters/{chapter}/quiz', [QuizController::class, 'store'])->name('quizzes.store');
    Route::get('quizzes/{quiz}', [QuizController::class, 'show'])->name('quizzes.show');
    Route::get('quizzes/{quiz}/edit', [QuizController::class, 'edit'])->name('quizzes.edit');
    Route::put('quizzes/{quiz}', [QuizController::class, 'update'])->name('quizzes.update');
    Route::delete('quizzes/{quiz}', [QuizController::class, 'destroy'])->name('quizzes.destroy');
    Route::post('quizzes/{quiz}/questions', [QuizController::class, 'addQuestion'])->name('quizzes.add-question');
    Route::delete('questions/{question}', [QuizController::class, 'deleteQuestion'])->name('questions.destroy');

    // Career Profiles
    Route::resource('profiles', ProfileController::class);

    // Correspondence
    Route::get('correspondence', [CorrespondenceController::class, 'index'])->name('correspondence.index');
    Route::get('correspondence/categories/create', [CorrespondenceController::class, 'createCategory'])->name('correspondence.create-category');
    Route::post('correspondence/categories', [CorrespondenceController::class, 'storeCategory'])->name('correspondence.store-category');
    Route::get('correspondence/categories/{category}/questions/create', [CorrespondenceController::class, 'createQuestion'])->name('correspondence.create-question');
    Route::post('correspondence/categories/{category}/questions', [CorrespondenceController::class, 'storeQuestion'])->name('correspondence.store-question');
    Route::get('correspondence/questions/{question}/edit', [CorrespondenceController::class, 'editQuestion'])->name('correspondence.edit-question');
    Route::put('correspondence/questions/{question}', [CorrespondenceController::class, 'updateQuestion'])->name('correspondence.update-question');
    Route::delete('correspondence/questions/{question}', [CorrespondenceController::class, 'deleteQuestion'])->name('correspondence.delete-question');

    // Transactions & Payments
    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('transactions/settings', [TransactionController::class, 'settings'])->name('transactions.settings');
    Route::put('transactions/settings/update', [TransactionController::class, 'updateSettings'])->name('transactions.update-settings');
    Route::get('cash-codes', [TransactionController::class, 'cashCodes'])->name('cash-codes.index');
    Route::get('cash-codes/create', [TransactionController::class, 'createCashCode'])->name('cash-codes.create');
    Route::post('cash-codes', [TransactionController::class, 'storeCashCode'])->name('cash-codes.store');
    Route::delete('cash-codes/{cashCode}', [TransactionController::class, 'deleteCashCode'])->name('cash-codes.destroy');
    Route::get('settings/payments', [TransactionController::class, 'settings'])->name('settings.payments');
    Route::put('settings/payments', [TransactionController::class, 'updateSettings'])->name('settings.payments.update');
});

// Auth routes (use Laravel Breeze or Fortify)
require __DIR__.'/auth.php';
