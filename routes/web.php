<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\StaffingRequestController;
use App\Http\Controllers\ProfessionalController;
use App\Http\Controllers\StaffAuthController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\StaffingRequestAdminController;
use App\Http\Controllers\Admin\ProfessionalAdminController;
use App\Http\Controllers\ProfessionalDashboardController;
use App\Http\Controllers\SocialAuthController;

// Social Authentication Routes (Google & Facebook)
Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');

// Public Pages
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/terms', [PublicController::class, 'terms'])->name('terms');
Route::get('/privacy', [PublicController::class, 'privacy'])->name('privacy');

// Public Vetted Staff Catalog & Profiles (app.eventushers.com/crew/Patrick, /hire-staff/Patrick, /mycv.ac/Patrick, and /crew-select/...)
Route::get('/crew', [ProfessionalController::class, 'publicIndex'])->name('crew.index');
Route::get('/crew/{professional}', [ProfessionalController::class, 'publicShow'])->name('crew.show');
Route::get('/mycv.ac/{professional}', [ProfessionalController::class, 'publicShow'])->name('crew.mycv.alias');
Route::get('/mycv/{professional}', [ProfessionalController::class, 'publicShow'])->name('crew.mycv.short');
Route::get('/hire-staff/{professional}', [ProfessionalController::class, 'publicShow'])->name('crew.show.alias');
Route::get('/crew-select/{professional}', [StaffingRequestController::class, 'createForProfessional'])->name('crew.select');
Route::post('/crew/{professional}/review', [ProfessionalController::class, 'storeReview'])->name('crew.review.store');
Route::get('/categories/{category}/skills', [\App\Http\Controllers\Admin\SkillAdminController::class, 'getByCategory'])->name('categories.skills');

use App\Http\Controllers\ClientPortalController;

// Digital Contract Routes
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\PasswordResetController;
Route::get('/forgot-password', [PasswordResetController::class, 'showForgot'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/contracts/{contract}', [ContractController::class, 'show'])->name('contracts.show');
Route::get('/requests/{staffingRequest}/contract', [ContractController::class, 'createOrViewForRequest'])->name('contracts.forRequest');
Route::post('/contracts/{contract}/sign', [ContractController::class, 'sign'])->name('contracts.sign');
Route::post('/requests/{staffingRequest}/proposals', [ProposalController::class, 'store'])->name('proposals.store');
Route::post('/proposals/{proposal}/respond', [ProposalController::class, 'respond'])->name('proposals.respond');

// Public Staff Hiring Form
Route::get('/hire-staff', [StaffingRequestController::class, 'create'])->name('hire.create');
Route::post('/hire-staff', [StaffingRequestController::class, 'store'])->name('hire.store');
Route::get('/hire-staff/success', [StaffingRequestController::class, 'success'])->name('hire.success');

// Client Portal Routes
Route::get('/client/login', [ClientPortalController::class, 'showLogin'])->name('client.login');
Route::post('/client/login', [ClientPortalController::class, 'login'])->name('client.login.submit');
Route::get('/client/register', [ClientPortalController::class, 'showRegister'])->name('client.register');
Route::post('/client/register', [ClientPortalController::class, 'register'])->name('client.register.submit');
Route::post('/client/logout', [ClientPortalController::class, 'logout'])->name('client.logout');
Route::get('/client/dashboard', [ClientPortalController::class, 'dashboard'])->name('client.dashboard');
Route::post('/client/favorites/{professional}', [ClientPortalController::class, 'toggleFavorite'])->name('client.favorites.toggle');
Route::get('/users/message', [\App\Http\Controllers\MessageController::class, 'index'])->name('client.messages');
Route::get('/client/messages', [\App\Http\Controllers\MessageController::class, 'index'])->name('client.messages.alt');

// Public Staff Registration (Join Crew)
Route::get('/join-our-crew', [ProfessionalController::class, 'create'])->name('crew.create');
Route::post('/join-our-crew', [ProfessionalController::class, 'store'])->name('crew.store');

// Staff Portal Login & Authentication
Route::get('/staff/login', [StaffAuthController::class, 'showLogin'])->name('staff.login');
Route::post('/staff/login', [StaffAuthController::class, 'login'])->name('staff.login.submit');
Route::post('/staff/logout', [StaffAuthController::class, 'logout'])->name('staff.logout');
Route::get('/staff/verify-notice', [StaffAuthController::class, 'showVerifyNotice'])->name('staff.verify.notice');
Route::get('/staff/verify-email/{token}', [StaffAuthController::class, 'verifyEmail'])->name('staff.verify.email');

// General Login Redirect
Route::get('/login', function () {
    return redirect()->route('staff.login');
})->name('login');

// Admin Auth Routes
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Protected Admin Panel Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/staffing-requests', [StaffingRequestAdminController::class, 'index'])->name('requests.index');
    Route::get('/staffing-requests/create', [StaffingRequestAdminController::class, 'create'])->name('requests.create');
    Route::post('/staffing-requests', [StaffingRequestAdminController::class, 'store'])->name('requests.store');
    Route::get('/staffing-requests/{staffingRequest}', [StaffingRequestAdminController::class, 'show'])->name('requests.show');
    Route::patch('/staffing-requests/{staffingRequest}/status', [StaffingRequestAdminController::class, 'updateStatus'])->name('requests.status');
    Route::post('/staffing-requests/{staffingRequest}/assign', [StaffingRequestAdminController::class, 'assign'])->name('requests.assign');

    Route::get('/professionals', [ProfessionalAdminController::class, 'index'])->name('professionals.index');
    Route::get('/professionals/create', [ProfessionalAdminController::class, 'create'])->name('professionals.create');
    Route::post('/professionals', [ProfessionalAdminController::class, 'store'])->name('professionals.store');
    Route::get('/professionals/{professional}', [ProfessionalAdminController::class, 'show'])->name('professionals.show');
    Route::get('/professionals/{professional}/edit', [ProfessionalAdminController::class, 'edit'])->name('professionals.edit');
    Route::put('/professionals/{professional}', [ProfessionalAdminController::class, 'update'])->name('professionals.update');
    Route::patch('/professionals/{professional}/status', [ProfessionalAdminController::class, 'updateStatus'])->name('professionals.status');
    Route::delete('/professionals/{professional}', [ProfessionalAdminController::class, 'destroy'])->name('professionals.destroy');

    // Admin Staff Categories Management
    Route::get('/categories', [\App\Http\Controllers\Admin\CategoryAdminController::class, 'index'])->name('categories.index');
    Route::post('/categories', [\App\Http\Controllers\Admin\CategoryAdminController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [\App\Http\Controllers\Admin\CategoryAdminController::class, 'update'])->name('categories.update');
    Route::patch('/categories/{category}/toggle', [\App\Http\Controllers\Admin\CategoryAdminController::class, 'toggleStatus'])->name('categories.toggle');
    Route::delete('/categories/{category}', [\App\Http\Controllers\Admin\CategoryAdminController::class, 'destroy'])->name('categories.destroy');

    // Admin Category Skills Management
    Route::get('/skills', [\App\Http\Controllers\Admin\SkillAdminController::class, 'index'])->name('skills.index');
    Route::post('/skills', [\App\Http\Controllers\Admin\SkillAdminController::class, 'store'])->name('skills.store');
    Route::put('/skills/{skill}', [\App\Http\Controllers\Admin\SkillAdminController::class, 'update'])->name('skills.update');
    Route::patch('/skills/{skill}/toggle', [\App\Http\Controllers\Admin\SkillAdminController::class, 'toggleStatus'])->name('skills.toggle');
    Route::delete('/skills/{skill}', [\App\Http\Controllers\Admin\SkillAdminController::class, 'destroy'])->name('skills.destroy');

    // Admin Clients Directory & History
    Route::get('/clients', [\App\Http\Controllers\Admin\AdminClientController::class, 'index'])->name('clients.index');
    Route::get('/clients/{client}', [\App\Http\Controllers\Admin\AdminClientController::class, 'show'])->name('clients.show');

    // Admin Payment & Escrow Wallet Management
    Route::get('/payments', [\App\Http\Controllers\Admin\AdminPaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/requests/{staffingRequest}/mark-paid', [\App\Http\Controllers\Admin\AdminPaymentController::class, 'markPaid'])->name('payments.markPaid');
    Route::patch('/payments/withdrawals/{withdrawal}', [\App\Http\Controllers\Admin\AdminPaymentController::class, 'updateWithdrawal'])->name('payments.withdrawal');

    // Admin Support & Dispute Desk
    Route::get('/tickets', [\App\Http\Controllers\Admin\AdminTicketController::class, 'index'])->name('tickets.index');
    Route::post('/tickets/{ticket}', [\App\Http\Controllers\Admin\AdminTicketController::class, 'respond'])->name('tickets.respond');

    // Admin System Settings
    Route::get('/settings', [\App\Http\Controllers\Admin\AdminSettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [\App\Http\Controllers\Admin\AdminSettingController::class, 'update'])->name('settings.update');

    // Admin Review Moderation
    Route::get('/reviews', [\App\Http\Controllers\Admin\AdminReviewController::class, 'index'])->name('reviews.index');
    Route::delete('/reviews/{review}', [\App\Http\Controllers\Admin\AdminReviewController::class, 'destroy'])->name('reviews.destroy');

    // Sub-Admin & RBAC Roles
    Route::get('/subadmins', [\App\Http\Controllers\Admin\AdminSubAdminController::class, 'index'])->name('subadmins.index');
    Route::post('/subadmins', [\App\Http\Controllers\Admin\AdminSubAdminController::class, 'store'])->name('subadmins.store');
    Route::delete('/subadmins/{subadmin}', [\App\Http\Controllers\Admin\AdminSubAdminController::class, 'destroy'])->name('subadmins.destroy');

    // Admin Promotional Coupons
    Route::get('/coupons', [\App\Http\Controllers\Admin\AdminCouponController::class, 'index'])->name('coupons.index');
    Route::post('/coupons', [\App\Http\Controllers\Admin\AdminCouponController::class, 'store'])->name('coupons.store');
    Route::delete('/coupons/{coupon}', [\App\Http\Controllers\Admin\AdminCouponController::class, 'destroy'])->name('coupons.destroy');

    // CSV Reports & Export Generator
    Route::get('/reports/requests/export', [\App\Http\Controllers\Admin\AdminReportController::class, 'exportRequests'])->name('reports.requests.export');
    Route::get('/reports/professionals/export', [\App\Http\Controllers\Admin\AdminReportController::class, 'exportProfessionals'])->name('reports.professionals.export');
});

    // Protected Staff Portal Routes
Route::prefix('professional')->name('professional.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('professional.dashboard');
    });
    Route::get('/dashboard', [ProfessionalDashboardController::class, 'index'])->name('dashboard');
    Route::get('/onboarding', [ProfessionalDashboardController::class, 'onboarding'])->name('onboarding');
    Route::match(['get', 'post'], '/onboarding', [ProfessionalDashboardController::class, 'storeOnboarding'])->name('onboarding.store');
    Route::get('/profile', [ProfessionalDashboardController::class, 'profile'])->name('profile');
    Route::match(['post', 'patch'], '/profile', [ProfessionalDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::post('/gallery/delete', [ProfessionalDashboardController::class, 'deleteGalleryPhoto'])->name('gallery.delete');
    Route::get('/shifts', [ProfessionalDashboardController::class, 'shifts'])->name('shifts');
    Route::get('/messages', [\App\Http\Controllers\MessageController::class, 'index'])->name('messages');
    Route::get('/message', [\App\Http\Controllers\MessageController::class, 'index'])->name('message');
    Route::get('/messages/fetch', [\App\Http\Controllers\MessageController::class, 'fetchLatest'])->name('messages.fetch');
    Route::post('/messages', [\App\Http\Controllers\MessageController::class, 'store'])->name('messages.send');
    Route::post('/messages/propose-price', [\App\Http\Controllers\MessageController::class, 'proposePrice'])->name('messages.proposePrice');
    Route::post('/messages/accept-price', [\App\Http\Controllers\MessageController::class, 'acceptPrice'])->name('messages.acceptPrice');
    Route::get('/support', [\App\Http\Controllers\SupportTicketController::class, 'index'])->name('support');
    Route::post('/support', [\App\Http\Controllers\SupportTicketController::class, 'store'])->name('support.store');
    Route::get('/notifications', [ProfessionalDashboardController::class, 'notifications'])->name('notifications');
    Route::patch('/jobs/{job}/status', [ProfessionalDashboardController::class, 'updateJobStatus'])->name('jobs.status');
    Route::get('/wallet', [ProfessionalDashboardController::class, 'wallet'])->name('wallet');
    Route::post('/wallet/withdraw', [ProfessionalDashboardController::class, 'storeWithdrawal'])->name('wallet.withdraw');
    Route::post('/payment-details', [ProfessionalDashboardController::class, 'updatePaymentDetails'])->name('payment-details.update');
    Route::post('/reviews/{review}/toggle-visibility', [ProfessionalDashboardController::class, 'toggleReviewVisibility'])->name('reviews.toggle-visibility');
});

// General In-App Messaging & Price Negotiation Routes
Route::get('/messages', [\App\Http\Controllers\MessageController::class, 'index'])->name('messages');
Route::get('/messages/fetch', [\App\Http\Controllers\MessageController::class, 'fetchLatest'])->name('messages.fetch');
Route::post('/messages', [\App\Http\Controllers\MessageController::class, 'store'])->name('messages.send');
Route::post('/messages/propose-price', [\App\Http\Controllers\MessageController::class, 'proposePrice'])->name('messages.proposePrice');
Route::post('/messages/accept-price', [\App\Http\Controllers\MessageController::class, 'acceptPrice'])->name('messages.acceptPrice');

// Standalone Light-Themed User Creation Form
use App\Http\Controllers\UserController;
Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/users', [UserController::class, 'store'])->name('users.store');

// Live Server Storage Symlink Helper Route
Route::get('/symlink', function () {
    $target = storage_path('app/public');
    $shortcut = public_path('storage');

    if (file_exists($shortcut)) {
        if (is_link($shortcut)) {
            return response()->json([
                'status' => 'exists',
                'message' => 'Storage symlink already exists and points to storage/app/public.',
                'target' => $target,
                'shortcut' => $shortcut
            ]);
        }
        return response()->json([
            'status' => 'warning',
            'message' => 'public/storage exists as a regular folder. Delete public/storage folder on server and visit this link again.',
            'shortcut' => $shortcut
        ]);
    }

    try {
        symlink($target, $shortcut);
        return response()->json([
            'status' => 'success',
            'message' => 'Storage symlink created successfully!'
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to create symlink: ' . $e->getMessage()
        ], 500);
    }
});

