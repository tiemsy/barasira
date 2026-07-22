<?php

use App\Http\Controllers\Admin\ImpersonationController;
use App\Http\Controllers\Front\AuthenticatedSessionController;
use App\Http\Controllers\Front\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\Front\ContactController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\LegalController;
use App\Http\Controllers\Front\MessageController;
use App\Http\Controllers\Front\MissionController;
use App\Http\Controllers\Front\MissionInvitationController;
use App\Http\Controllers\Front\PartnerController;
use App\Http\Controllers\Front\PartnerSponsorshipController;
use App\Http\Controllers\Front\PaymentController;
use App\Http\Controllers\Front\PlatformReviewController;
use App\Http\Controllers\Front\ProfileController;
use App\Http\Controllers\Front\ProfileCredentialController;
use App\Http\Controllers\Front\ProviderDocumentController;
use App\Http\Controllers\Front\Provider\DashboardController as ProviderDashboardController;
use App\Http\Controllers\Front\ServiceController;
use App\Http\Controllers\SeoController;
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
Route::get('/partners', [PartnerController::class, 'index'])->name('front.partners.index');
Route::get('/partners/sponsoring', [PartnerSponsorshipController::class, 'create'])->name('front.partners.sponsorship.create');
Route::post('/partners/sponsoring', [PartnerSponsorshipController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('front.partners.sponsorship.store');
Route::get('/avis', [PlatformReviewController::class, 'index'])->name('front.platform-reviews.index');
Route::post('/avis', [PlatformReviewController::class, 'store'])
    ->middleware(['auth', 'verified', 'throttle:5,1'])
    ->name('front.platform-reviews.store');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('seo.sitemap');
Route::get('/legal/{document}', [LegalController::class, 'show'])
    ->whereIn('document', ['cgu', 'cgv', 'confidentialite', 'cookies', 'moderation', 'kyc'])
    ->name('legal.show');

// Services
Route::controller(ServiceController::class)->group(function () {
    Route::get('/services', 'index')->name('front.services.index');
    Route::get('/services/{service:slug}', 'show')->name('front.services.show');
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
        'seo' => \App\Support\SeoMeta::page(
            request(),
            'Contacter Barasira',
            'Contactez l’équipe Barasira pour toute question sur la recherche de prestataires et les services disponibles au Mali.'
        ),
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
    Route::middleware(['role:prestataire', 'throttle:30,1'])->prefix('/profile')->group(function () {
        Route::post('/educations', [ProfileCredentialController::class, 'storeEducation'])->name('profile.educations.store');
        Route::put('/educations/{education}', [ProfileCredentialController::class, 'updateEducation'])->name('profile.educations.update');
        Route::delete('/educations/{education}', [ProfileCredentialController::class, 'destroyEducation'])->name('profile.educations.destroy');
        Route::post('/experiences', [ProfileCredentialController::class, 'storeExperience'])->name('profile.experiences.store');
        Route::put('/experiences/{experience}', [ProfileCredentialController::class, 'updateExperience'])->name('profile.experiences.update');
        Route::delete('/experiences/{experience}', [ProfileCredentialController::class, 'destroyExperience'])->name('profile.experiences.destroy');
        Route::post('/certifications', [ProfileCredentialController::class, 'storeCertification'])->name('profile.certifications.store');
        Route::put('/certifications/{certification}', [ProfileCredentialController::class, 'updateCertification'])->name('profile.certifications.update');
        Route::delete('/certifications/{certification}', [ProfileCredentialController::class, 'destroyCertification'])->name('profile.certifications.destroy');
        Route::post('/documents', [ProviderDocumentController::class, 'store'])->name('profile.documents.store');
        Route::get('/documents/{document}', [ProviderDocumentController::class, 'download'])->name('profile.documents.download');
        Route::delete('/documents/{document}', [ProviderDocumentController::class, 'destroy'])->name('profile.documents.destroy');
    });

    // Missions (client)
    Route::controller(MissionController::class)->group(function () {
        Route::get('/missions/index', 'userMissions')->name('front.missions.index');
        Route::get('/missions/create', 'create')->name('front.missions.create');
        Route::get('/missions/{mission}/edit', 'edit')->name('front.missions.edit');
        Route::post('/missions/{mission}/images', 'replaceImages')->middleware('throttle:10,1')->name('front.missions.images.replace');
        Route::delete('/missions/{mission}/provider', 'unassignProvider')->middleware(['role:client', 'throttle:10,1'])->name('front.missions.provider.unassign');
        Route::get('/missions/{mission:slug}', 'show')->name('front.missions.show');
    });

    Route::post('/missions/{mission}/invite-provider', [MissionInvitationController::class, 'store'])
        ->middleware(['role:client', 'throttle:10,1'])
        ->name('front.missions.invite-provider');
    Route::get('/mission-invitations/{invitation}', [MissionInvitationController::class, 'show'])
        ->middleware(['role:prestataire', 'signed'])
        ->name('front.mission-invitations.show');
    Route::post('/mission-invitations/{invitation}/accept', [MissionInvitationController::class, 'accept'])
        ->middleware(['role:prestataire', 'throttle:10,1'])
        ->name('front.mission-invitations.accept');

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
            'message' => __('messages.email_already_verified'),
        ], 400);
    }
    $request->user()->sendEmailVerificationNotification();

    return response()->json([
        'message' => __('messages.verification_email_resent'),
    ]);
});

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', __('messages.verification_email_sent'));
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
