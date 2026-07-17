<?php

use App\Http\Controllers\Admin\ImpersonationController;
use App\Http\Controllers\Front\AuthenticatedSessionController;
use App\Http\Controllers\Front\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\Front\ContactController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\MessageController;
use App\Http\Controllers\Front\MissionController;
use App\Http\Controllers\Front\PaymentController;
use App\Http\Controllers\Front\ProfileController;
use App\Http\Controllers\Front\Provider\DashboardController as ProviderDashboardController;
use App\Http\Controllers\Front\ServiceController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home
Route::controller(HomeController::class)->group(function () {
    Route::get('/', 'index')->name('home');
});

// Services
Route::controller(ServiceController::class)->group(function () {
    Route::get('/services', 'index')->name('front.services.index');
    Route::get('/services/{service}', 'show')->name('front.services.show');
});

/*
|--------------------------------------------------------------------------
| Auth (Invités)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => Inertia::render('Auth/Login'))->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'login'])->middleware(['verified'])->name('login.store');

    Route::get('/register', fn () => Inertia::render('Auth/Register', [
        'googleProfile' => session()->get('google_registration'),
    ]))->name('register');
    Route::post('/register', [AuthenticatedSessionController::class, 'register'])->name('register.store');
});

Route::get('/contact-us', function () {
    return Inertia::render('ContactUs', [
        'contactEmail' => config('mail.contact_address', 'contact@barasira.com'),
        'contactPhone' => '+223 00 00 00 00',
    ]);
})->name('contact.index');

Route::post('/contact-us', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');

Route::middleware('auth')->post('/impersonation/stop', [ImpersonationController::class, 'stop'])
    ->name('impersonation.stop');

/*
|--------------------------------------------------------------------------
| Authentifiés
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard client
    Route::get('/dashboard', [ClientDashboardController::class, 'index'])->middleware('role:client')->name('client.dashboard');

    // Dashboard prestataire
    Route::get('/provider/dashboard', [ProviderDashboardController::class, 'index'])->middleware('role:prestataire')->name('provider.dashboard');

    // Profil
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');

    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');

    // Missions (client)
    Route::controller(MissionController::class)->group(function () {
        Route::get('/missions/index', 'userMissions')->name('front.missions.index');
        Route::get('/missions/create', 'create')->name('front.missions.create');
        Route::get('/missions/{mission}/edit', 'edit')->name('front.missions.edit');
        Route::get('/missions/{mission}', 'show')->name('front.missions.show');
    });

    // Paiement
    Route::get('/payments/{mission}', [PaymentController::class, 'show'])->name('payments.select');
    Route::post('/payments/{mission}', [PaymentController::class, 'store'])->middleware('throttle:10,1')->name('payments.store');
    Route::get('/payments/result/{payment}', [PaymentController::class, 'returned'])->name('payments.return');
    Route::get('/payments/paypal/{payment}/capture', [PaymentController::class, 'capture'])->name('payments.paypal.capture');

    // Déconnexion
    Route::post('/logout', [AuthenticatedSessionController::class, 'logout'])->name('logout');
});

// Page notice (SPA)
Route::get('/email/verify', function () {
    return inertia('Auth/VerifyEmail');
})->middleware(['auth'])->name('verification.notice');

// Lien cliqué dans l’email
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // Qui appelle markEmailAsVerified()

    $user = $request->user();

    // Mise à jour automatique Laravel
    $user->markEmailAsVerified();

    // Mise à jour de ton champ custom
    $user->verified = true;
    $user->save();

    $redirect = match ($user->role) {
        'admin', 'superadmin' => '/admin/dashboard',
        'prestataire' => '/provider/dashboard',
        'client' => '/dashboard',
    };

    return redirect(config('app.url').$redirect);
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::middleware('auth')->post('/email/resend', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return response()->json([
            'message' => 'Email déjà vérifié.',
        ], 400);
    }
    $request->user()->sendEmailVerificationNotification();

    return response()->json([
        'message' => 'Email de vérification renvoyé. Vérifiez également votre SPAM si vous ne voyez pas le message dans votre boite de reception',
    ]);
});

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Email de vérification envoyé. Vérifiez aussi votre SPAM');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
