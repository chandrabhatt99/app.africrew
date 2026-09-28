<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\ClientApiController;
use App\Http\Controllers\Api\Auth\CrewApiController;

/*
|--------------------------------------------------------------------------
| Mobile Application API Routes (V1)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Client Mobile App Auth Routes
    Route::prefix('client')->group(function () {
        Route::post('/register', [ClientApiController::class, 'register']);
        Route::post('/login', [ClientApiController::class, 'login']);
        Route::get('/profile', [ClientApiController::class, 'profile']);
        Route::post('/logout', [ClientApiController::class, 'logout']);
    });

    // Crew / Professional Mobile App Auth Routes
    Route::prefix('crew')->group(function () {
        Route::post('/register', [CrewApiController::class, 'register']);
        Route::post('/login', [CrewApiController::class, 'login']);
        Route::get('/profile', [CrewApiController::class, 'profile']);
        Route::post('/logout', [CrewApiController::class, 'logout']);
    });

    // Categories & Category-wise Skills API Endpoints
    Route::get('/categories-with-skills', [\App\Http\Controllers\Api\CategorySkillApiController::class, 'categoriesWithSkills']);
    Route::get('/categories/{category}/skills', [\App\Http\Controllers\Api\CategorySkillApiController::class, 'skillsByCategory']);
    Route::post('/admin/skills', [\App\Http\Controllers\Api\CategorySkillApiController::class, 'storeSkill']);

});
