<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\ClientApiController;
use App\Http\Controllers\Api\Auth\CrewApiController;
use App\Http\Controllers\Api\ClientMobileController;
use App\Http\Controllers\Api\CrewMobileController;
use App\Http\Controllers\Api\CategorySkillApiController;
use App\Http\Controllers\MpesaController;

/*
|--------------------------------------------------------------------------
| Mobile Application API Routes (V1)
| Complete Suite for Client and Crew Mobile Apps
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ==========================================
    // 1. CLIENT MOBILE APP SUITE
    // ==========================================
    Route::prefix('client')->group(function () {
        // Authentication & Account
        Route::post('/register', [ClientApiController::class, 'register']);
        Route::post('/login', [ClientApiController::class, 'login']);
        Route::get('/profile', [ClientApiController::class, 'profile']);
        Route::match(['put', 'post'], '/profile', [ClientMobileController::class, 'updateProfile']);
        Route::post('/logout', [ClientApiController::class, 'logout']);
        Route::post('/forgot-password', [ClientMobileController::class, 'forgotPassword']);
        Route::post('/reset-password', [ClientMobileController::class, 'resetPassword']);

        // Crew Discovery & Public Profiles
        Route::get('/crew', [ClientMobileController::class, 'browseCrew']);
        Route::get('/crew/{idOrUsername}', [ClientMobileController::class, 'getCrewDetail']);
        Route::get('/favorites', [ClientMobileController::class, 'getFavorites']);
        Route::post('/favorites/{professionalId}/toggle', [ClientMobileController::class, 'toggleFavorite']);

        // Gigs & Bookings Management
        Route::post('/bookings', [ClientMobileController::class, 'createBooking']);
        Route::get('/bookings', [ClientMobileController::class, 'getBookings']);
        Route::get('/bookings/{id}', [ClientMobileController::class, 'getBookingDetail']);
        Route::post('/bookings/{id}/cancel', [ClientMobileController::class, 'cancelBooking']);

        // Digital Contracts
        Route::post('/contracts/{id}/sign', [ClientMobileController::class, 'signContract']);

        // Reviews & Ratings
        Route::post('/crew/{id}/reviews', [ClientMobileController::class, 'submitReview']);

        // Direct Messaging
        Route::get('/messages', [ClientMobileController::class, 'getConversations']);
        Route::post('/messages', [ClientMobileController::class, 'sendMessage']);
    });

    // ==========================================
    // 2. CREW / PROFESSIONAL MOBILE APP SUITE
    // ==========================================
    Route::prefix('crew')->group(function () {
        // Authentication & Status
        Route::post('/register', [CrewApiController::class, 'register']);
        Route::post('/login', [CrewApiController::class, 'login']);
        Route::get('/profile', [CrewApiController::class, 'profile']);
        Route::post('/logout', [CrewApiController::class, 'logout']);
        Route::post('/forgot-password', [CrewMobileController::class, 'forgotPassword']);
        Route::post('/reset-password', [CrewMobileController::class, 'resetPassword']);
        Route::get('/status', [CrewMobileController::class, 'checkStatus']);

        // Profile Builder, Rates & Bio
        Route::post('/profile/update', [CrewMobileController::class, 'updateProfile']);
        Route::post('/profile/expertises', [CrewMobileController::class, 'updateExpertises']);
        Route::post('/profile/gallery', [CrewMobileController::class, 'uploadGalleryPhoto']);
        Route::delete('/profile/gallery', [CrewMobileController::class, 'deleteGalleryPhoto']);
        Route::post('/profile/education', [CrewMobileController::class, 'updateEducation']);
        Route::post('/profile/experience', [CrewMobileController::class, 'updateExperience']);
        Route::post('/profile/referees', [CrewMobileController::class, 'updateReferees']);
        Route::post('/profile/referees/{index}/resend', [CrewMobileController::class, 'resendRefereeVerification']);
        Route::post('/profile/upload-documents', [CrewMobileController::class, 'uploadDocuments']);
        Route::post('/skills/suggest', [CrewMobileController::class, 'suggestSkill']);

        // Shifts & Gig Management
        Route::get('/shifts', [CrewMobileController::class, 'getShifts']);
        Route::get('/shifts/{id}', [CrewMobileController::class, 'getShiftDetail']);
        Route::post('/shifts/{id}/respond', [CrewMobileController::class, 'respondShift']);
        Route::post('/shifts/{id}/check-in', [CrewMobileController::class, 'checkInShift']);
        Route::post('/shifts/{id}/complete', [CrewMobileController::class, 'completeShift']);
        Route::post('/contracts/{id}/sign', [CrewMobileController::class, 'signContract']);

        // Wallet, Escrow & Payouts
        Route::get('/wallet', [CrewMobileController::class, 'getWallet']);
        Route::post('/wallet/payout-method', [CrewMobileController::class, 'savePayoutMethod']);
        Route::post('/wallet/withdraw', [CrewMobileController::class, 'requestWithdrawal']);
        Route::get('/wallet/transactions', [CrewMobileController::class, 'getTransactions']);

        // Reviews
        Route::get('/reviews', [CrewMobileController::class, 'getReviews']);
        Route::post('/reviews/{id}/toggle-visibility', [CrewMobileController::class, 'toggleReviewVisibility']);

        // In-App Chat & Negotiations
        Route::get('/messages', [CrewMobileController::class, 'getConversations']);
        Route::post('/messages', [CrewMobileController::class, 'sendMessage']);
        Route::post('/messages/propose-rate', [CrewMobileController::class, 'proposeRate']);

        // Support & Help Desk
        Route::get('/support/tickets', [CrewMobileController::class, 'getSupportTickets']);
        Route::post('/support/tickets', [CrewMobileController::class, 'createSupportTicket']);
    });

    // ==========================================
    // 3. COMMON CATALOG & SYSTEM LOOKUPS
    // ==========================================
    Route::get('/categories-with-skills', [CategorySkillApiController::class, 'categoriesWithSkills']);
    Route::get('/categories/{category}/skills', [CategorySkillApiController::class, 'skillsByCategory']);
    Route::post('/admin/skills', [CategorySkillApiController::class, 'storeSkill']);
});

/*
|--------------------------------------------------------------------------
| Safaricom M-Pesa Daraja Payment Gateway Routes
|--------------------------------------------------------------------------
*/
Route::prefix('mpesa')->group(function () {
    // Initiate STK Push (Lipa na M-Pesa Online)
    Route::post('/stkpush', [MpesaController::class, 'stkPush'])->name('api.mpesa.stkpush');

    // Safaricom Webhook Callbacks
    Route::post('/stk-callback', [MpesaController::class, 'stkCallback'])->name('api.mpesa.callback');
    Route::post('/callback', [MpesaController::class, 'stkCallback']); // Alias

    // Transaction Status Check (for front-end polling)
    Route::get('/status/{checkoutRequestId}', [MpesaController::class, 'checkStatus'])->name('api.mpesa.status');

    // Sandbox Simulation (for local testing when webhooks are blocked)
    Route::post('/simulate-success', [MpesaController::class, 'simulateSuccess'])->name('api.mpesa.simulate');

    // Direct C2B Paybill Hooks
    Route::post('/c2b/validation', [MpesaController::class, 'c2bValidation']);
    Route::post('/c2b/confirmation', [MpesaController::class, 'c2bConfirmation']);
});
