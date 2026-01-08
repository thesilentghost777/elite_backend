<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CorrespondenceController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\ReferralController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\PaymentController;
use Illuminate\Support\Facades\Route;




// Routes de paiement
Route::prefix('payment')->group(function () {
    // Routes nécessitant l'authentification
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/initiate-deposit', [PaymentController::class, 'initiateDeposit']);
        Route::post('/check-status', [PaymentController::class, 'checkPaymentStatus']);
    });
    
    // Webhook MoneyFusion (sans authentification)
    Route::post('/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');
    
    // URL de retour après paiement (sans authentification)
    Route::get('/return', [PaymentController::class, 'returnUrl'])->name('payment.return');
});


// ============================================
// ROUTES PUBLIQUES
// ============================================

Route::get('/test', function() {
    return response()->json(['message' => 'Ghost API is working!']);
});

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/check-referral-code', [AuthController::class, 'checkReferralCode']);
});

Route::get('/profiles', [ProfileController::class, 'index']);
Route::get('/profiles/secteurs', [ProfileController::class, 'secteurs']);
Route::get('/profiles/{id}', [ProfileController::class, 'show']);

// ⚠️ ORDRE IMPORTANT : Routes spécifiques AVANT les routes avec paramètres
Route::get('/categories', [CourseController::class, 'categories']);
Route::get('/packs', [CourseController::class, 'packs']);
Route::get('/packs/{id}', [CourseController::class, 'packDetails'])->where('id', '[0-9]+');

Route::get('/faq', [FaqController::class, 'index']);
Route::get('/faq/search', [FaqController::class, 'search']);
Route::get('/faq/popular', [FaqController::class, 'popular']);
Route::get('/faq/{id}', [FaqController::class, 'show']);

// ============================================
// ROUTES AUTHENTIFIÉES
// ============================================
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/profile', [AuthController::class, 'profile']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);

    // Correspondance
    Route::get('/correspondence/questions', [CorrespondenceController::class, 'questions']);
    Route::post('/correspondence/submit', [CorrespondenceController::class, 'submit']);
    Route::get('/correspondence/results', [CorrespondenceController::class, 'results']);
    Route::post('/correspondence/choose-profile', [CorrespondenceController::class, 'chooseProfile']);
    Route::post('/correspondence/choose-path', [CorrespondenceController::class, 'choosePath']);

    // Profils & Roadmaps
    Route::get('/profiles/{id}/roadmap', [ProfileController::class, 'roadmap']);
    Route::get('/my-roadmap', [ProfileController::class, 'myRoadmap']);

    // Cours - ⚠️ Routes spécifiques AVANT les routes avec {id}
    Route::get('/packs/recommended', [CourseController::class, 'recommendedPacks']);
    Route::get('/packs/{id}/modules', [CourseController::class, 'packModules'])->where('id', '[0-9]+');
    
    Route::get('/modules/{id}/chapters', [CourseController::class, 'moduleChapters'])->where('id', '[0-9]+');
    
    Route::get('/chapters/{id}/lessons', [CourseController::class, 'chapterLessons'])->where('id', '[0-9]+');
    Route::get('/chapters/{id}/quiz', [CourseController::class, 'chapterQuiz'])->where('id', '[0-9]+');
    Route::post('/chapters/{id}/unlock-by-referral', [CourseController::class, 'unlockByReferral'])->where('id', '[0-9]+');
    
    Route::get('/lessons/{id}', [CourseController::class, 'lesson'])->where('id', '[0-9]+');
    Route::post('/lessons/{id}/complete', [CourseController::class, 'completeLesson'])->where('id', '[0-9]+');
    
    Route::post('/quiz/{id}/submit', [CourseController::class, 'submitQuiz'])->where('id', '[0-9]+');

    Route::get('chapters/{id}/quiz-info', [CourseController::class, 'getChapterQuizInfo']);
    // Wallet & Paiements
    Route::get('/wallet/balance', [WalletController::class, 'balance']);
    Route::post('/wallet/deposit', [WalletController::class, 'deposit']);
    Route::post('/wallet/use-cash-code', [WalletController::class, 'useCashCode']);
    Route::post('/wallet/find-user', [WalletController::class, 'findUser']);
    Route::post('/wallet/transfer', [WalletController::class, 'transfer']);
    Route::get('/wallet/transactions', [WalletController::class, 'transactions']);
    Route::post('/packs/{id}/purchase', [WalletController::class, 'purchasePack'])->where('id', '[0-9]+');
    Route::get('/user/packs', [WalletController::class, 'myPacks']);

    // Parrainage
    Route::get('/referral/my-code', [ReferralController::class, 'myCode']);
    Route::get('/referral/stats', [ReferralController::class, 'stats']);
    Route::get('/referral/history', [ReferralController::class, 'history']);
    Route::get('/referral/my-parrain', [ReferralController::class, 'myParrain']);
});