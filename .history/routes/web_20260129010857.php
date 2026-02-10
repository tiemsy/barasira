<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\MissionController;
use App\Http\Controllers\Front\ServiceController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Front\AuthenticatedSessionController;
use App\Http\Controllers\Front\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\Front\Provider\DashboardController as ProviderDashboardController;

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
    Route::get('/login', fn() => Inertia::render('Auth/Login'))->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'login'])->middleware(['verified'])->name('login.store');

    Route::get('/register', fn() => Inertia::render('Auth/Register'))->name('register');
    Route::post('/register', [AuthenticatedSessionController::class, 'register'])->name('register.store');
});

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
    Route::get('/profile', function () {
        return Inertia::render('Profile/Show');
    })->name('profile.show');

    // Profil
    Route::get('/messages/create', function () {
        return Inertia::render('Messages/Create');
    })->name('messages.create');

    Route::get('/profile/edit', function () {
        return Inertia::render('Profile/Edit');
    })->name('profile.edit');

    // Mes missions (client)
    // Route::get('/my-missions', function () {
    //     return Inertia::render('Missions/MyIndex');
    // })->name('missions.mine');

    // Missions (client)
    Route::controller(MissionController::class)->group(function () {
        Route::get('/missions/index', 'userMissions')->name('front.missions.index');
        Route::get('/missions/create', 'create')->name('front.missions.create');
        Route::get('/missions/show/{mission}', 'show')->name('front.missions.show');
    });

    // Paiement
    Route::get('/payments/{mission}', function ($mission) {
        return Inertia::render('Payments/SelectMethod', [
            'missionId' => $mission,
        ]);
    })->name('payments.select');

    Route::get('/payments/{mission}/confirm', function ($mission) {
        return Inertia::render('Payments/Confirm', [
            'missionId' => $mission,
        ]);
    })->name('payments.confirm');

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
        'admin' => '/admin/dashboard',
        'prestataire' => '/provider/dashboard',
        'client' => '/dashboard',
    };

    return redirect(config('app.url') . $redirect);
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::middleware('auth')->post('/email/resend', function (Request $request) {
    if ($request::user()->hasVerifiedEmail()) {
        return response()->json([
            'message' => 'Email déjà vérifié.'
        ], 400);
    }
    $request::user()->sendEmailVerificationNotification();
    return response()->json([
        'message' => 'Email de vérification renvoyé. Vérifiez également votre SPAM si vous ne voyez pas le message dans votre boite de reception'
    ]);
});

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Email de vérification envoyé. Vérifiez aussi votre SPAM');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
