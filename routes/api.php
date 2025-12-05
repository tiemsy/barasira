<?php

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

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserSkillController;
use App\Http\Controllers\Api\ServiceCategoryController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\MissionController;
use App\Http\Controllers\Api\PortfolioItemController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\ResumeController;

// USERS
Route::apiResource('users', UserController::class);

// USER SKILLS
Route::apiResource('user-skills', UserSkillController::class);

// SERVICE CATEGORIES
Route::apiResource('service-categories', ServiceCategoryController::class);

// SERVICES
Route::apiResource('services', ServiceController::class);

// MISSIONS
Route::apiResource('missions', MissionController::class);

// REVIEWS
Route::apiResource('reviews', ReviewController::class);

// RESUMES
Route::apiResource('resumes', ResumeController::class);

// PORTFOLIO ITEMS
Route::apiResource('portfolio-items', PortfolioItemController::class);
