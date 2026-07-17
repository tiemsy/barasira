<?php

use App\Http\Controllers\Api\Auth\GoogleAuthController;
use App\Http\Controllers\Api\Auth\LoginController as ApiLoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController as ApiRegisterController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\MissionAiController;
use App\Http\Controllers\Api\MissionController;
use App\Http\Controllers\Api\PaymentController as ApiPaymentController;
use App\Http\Controllers\Api\PortfolioItemController;
use App\Http\Controllers\Api\ResumeController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\ServiceCategoryController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\TranslationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserSkillController;
use App\Http\Controllers\Front\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// GOOGLE SSO
Route::middleware('web')->group(function () {
    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect']);
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);
});

// USER SKILLS
Route::apiResource('user-skills', UserSkillController::class);

// SERVICE CATEGORIES
Route::apiResource('service-categories', ServiceCategoryController::class);

// SERVICES
Route::apiResource('services', ServiceController::class)->only(['index', 'show']);
Route::get('services-search', [ServiceController::class, 'search'])->name('services.search');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::post('/services', [ServiceController::class, 'store']);
    Route::match(['put', 'patch'], '/services/{service}', [ServiceController::class, 'update']);
    Route::delete('/services/{service}', [ServiceController::class, 'destroy']);
    Route::match(['post', 'put', 'patch'], '/users/{user}', [UserController::class, 'update']);
    Route::post('/missions/{mission}/claim', [MissionController::class, 'claim']);
    Route::apiResource('missions', MissionController::class);
    Route::get('/messages', [MessageController::class, 'index']);
    Route::get('/messages/conversation/{user}', [MessageController::class, 'conversation']);
    Route::post('/messages', [MessageController::class, 'store']);
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::post('/missions/{mission}/payments', [ApiPaymentController::class, 'store'])->middleware('throttle:10,1');
    Route::get('/payments/{payment}', [ApiPaymentController::class, 'show']);
    Route::patch('/reviews/{review}', [ReviewController::class, 'update']);

    Route::middleware('throttle:ai')->group(function () {
        Route::post('/missions/generate-with-ai', MissionAiController::class);
    });

    Route::post('/ai/translate', TranslationController::class);
});

Route::middleware(['auth:sanctum', 'verified', 'role:admin,superadmin'])->group(function () {
    Route::apiResource('users', UserController::class)->only(['index', 'show']);
});

// RESUMES
Route::apiResource('resumes', ResumeController::class);

// PORTFOLIO ITEMS
Route::apiResource('portfolio-items', PortfolioItemController::class);

// Auth
Route::post('/login', [ApiLoginController::class, 'store'])->middleware(['web'])->name('api.login');
Route::post('/register', [ApiRegisterController::class, 'store'])->middleware('web')->name('api.register');

Route::middleware([
    'auth:sanctum',
    'verified',
])->group(function () {
    Route::get('/me', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [LogoutController::class, 'logout'])->name('api.logout');
});

Route::get('/debug-auth', function (Request $request) {
    return response()->json([
        'user' => $request->user(),
        'auth_id' => auth()->id(),
        'has_session' => $request->hasSession(),
        'session_id' => $request->hasSession() ? $request->session()->getId() : null,
        'cookies' => $request->cookies->all(),
    ]);
});

Route::match(['get', 'post'], '/payments/webhooks/cinetpay', [PaymentController::class, 'cinetPay'])
    ->middleware('throttle:120,1')->name('payments.webhooks.cinetpay');
Route::get('/payments/mobile/return/{payment}', [ApiPaymentController::class, 'returned'])->name('api.payments.mobile.return');
Route::get('/payments/mobile/cancel/{payment}', [ApiPaymentController::class, 'cancelled'])->name('api.payments.mobile.cancel');
