<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\PortalLoginController;
use App\Http\Controllers\Auth\SSOController;
use App\Http\Controllers\Auth\UserLoginController;
use App\Http\Controllers\User;
use App\Http\Controllers\Api\ChatPollController;
use App\Http\Controllers\Api\NotificationController;
use Illuminate\Support\Facades\Route;

// Public root
Route::get('/', fn() => redirect()->route('user.login'));

// Direct user login (authenticates against dashboard_db.users)
Route::get('/login',  [UserLoginController::class, 'showLogin'])->name('user.login');
Route::post('/login', [UserLoginController::class, 'login'])->name('user.login.post');
Route::post('/logout', [UserLoginController::class, 'logout'])->name('user.logout');

// Portal staff login
Route::get('/portal/login',  [PortalLoginController::class, 'showLogin'])->name('portal.login');
Route::post('/portal/login', [PortalLoginController::class, 'login'])->name('portal.login.post');
Route::post('/portal/logout', [PortalLoginController::class, 'logout'])->name('portal.logout');

// SSO — called by dashboardv2 redirect
Route::get('/auth/sso', [SSOController::class, 'handle'])->name('sso.handle');
Route::post('/auth/sso/logout', [SSOController::class, 'logout'])->name('sso.logout');

// Portal routes — permission-based, all authenticated portal users
Route::middleware('portal.auth')->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Profile (current user)
    Route::get('/profile/password',  [Admin\ProfileController::class, 'editPassword'])->name('profile.password');
    Route::post('/profile/password', [Admin\ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Requests — fine-grained auth handled in controller
    Route::get('/requests',                                [Admin\RequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/{customizationRequest}',         [Admin\RequestController::class, 'show'])->name('requests.show');
    Route::get('/requests/{customizationRequest}/edit',    [Admin\RequestController::class, 'edit'])->name('requests.edit');
    Route::put('/requests/{customizationRequest}',         [Admin\RequestController::class, 'update'])->name('requests.update');
    Route::post('/requests/{customizationRequest}/assign', [Admin\RequestController::class, 'assign'])->name('requests.assign');
    Route::post('/requests/{customizationRequest}/status', [Admin\RequestController::class, 'updateStatus'])->name('requests.status');
    Route::get('/requests/{customizationRequest}/logs',    [Admin\RequestController::class, 'logs'])->name('requests.logs');

    // Chat
    Route::get('/requests/{customizationRequest}/chat',  [Admin\ChatController::class, 'show'])->name('requests.chat');
    Route::post('/requests/{customizationRequest}/chat', [Admin\ChatController::class, 'store'])->name('requests.chat.store');

    // Bug reports
    Route::get('/bug-reports',              [Admin\BugReportController::class, 'index'])->name('bug-reports.index');
    Route::get('/bug-reports/{bugReport}',  [Admin\BugReportController::class, 'show'])->name('bug-reports.show');

    // User management — requires manage_portal_users permission
    Route::middleware('permission:manage_portal_users')->group(function () {
        Route::get('/users',                    [Admin\UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/create',             [Admin\UserManagementController::class, 'create'])->name('users.create');
        Route::post('/users',                   [Admin\UserManagementController::class, 'store'])->name('users.store');
        Route::get('/users/{portalUser}/edit',  [Admin\UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/users/{portalUser}',       [Admin\UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/users/{portalUser}',    [Admin\UserManagementController::class, 'destroy'])->name('users.destroy');
    });
});

// User routes (SSO authenticated)
Route::middleware('sso.auth')->prefix('request')->name('user.')->group(function () {

    Route::get('/dashboard',                     [User\RequestController::class, 'dashboard'])->name('dashboard');
    Route::get('/create',                        [User\RequestController::class, 'create'])->name('request.create');
    Route::post('/store',                        [User\RequestController::class, 'store'])->name('request.store');

    // Bug reports — must be above the {customizationRequest} wildcard
    Route::get('/bug-reports',      [User\BugReportController::class, 'index'])->name('bug-report.index');
    Route::get('/bug-report',       [User\BugReportController::class, 'create'])->name('bug-report.create');
    Route::post('/bug-report',      [User\BugReportController::class, 'store'])->name('bug-report.store');

    Route::get('/{customizationRequest}/edit',   [User\RequestController::class, 'edit'])->name('request.edit');
    Route::put('/{customizationRequest}',        [User\RequestController::class, 'update'])->name('request.update');
    Route::get('/{customizationRequest}',        [User\RequestController::class, 'show'])->name('request.show');
    Route::get('/{customizationRequest}/chat',   [User\ChatController::class, 'show'])->name('chat.show');
    Route::post('/{customizationRequest}/chat',  [User\ChatController::class, 'store'])->name('chat.store');
});

// Chat polling API
Route::get('/api/chat/{requestId}/poll', [ChatPollController::class, 'poll'])->name('api.chat.poll');

// Notification API
Route::get('/api/notifications',              [App\Http\Controllers\Api\NotificationController::class, 'index'])->name('api.notifications');
Route::post('/api/notifications/{notification}/dismiss', [App\Http\Controllers\Api\NotificationController::class, 'dismiss'])->name('api.notifications.dismiss');
Route::post('/api/notifications/clear',       [App\Http\Controllers\Api\NotificationController::class, 'clearAll'])->name('api.notifications.clear');
