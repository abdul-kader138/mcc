<?php

use App\Http\Controllers\Api\V1\Account\TwoFactorController;
use App\Http\Controllers\Api\V1\AccountController;
use App\Http\Controllers\Api\V1\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\V1\Auth\EmailVerificationController;
use App\Http\Controllers\Api\V1\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Api\V1\Auth\NewPasswordController;
use App\Http\Controllers\Api\V1\Auth\PasswordResetLinkController;
use App\Http\Controllers\Api\V1\Auth\RegisteredUserController;
use App\Http\Controllers\Api\V1\Auth\TwoFactorChallengeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/v1/auth/email/verify/{id}/{hash}', EmailVerificationController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('throttle:login')->name('register');
        Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:login')->name('login');
        Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->middleware('throttle:login')->name('password.email');
        Route::post('/reset-password', [NewPasswordController::class, 'store'])->middleware('throttle:login')->name('password.store');
        Route::post('/login/challenge', [TwoFactorChallengeController::class, 'store'])->middleware('throttle:2fa-challenge')->name('login.challenge');
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
            Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->middleware('throttle:6,1')->name('verification.send');
        });
    });

    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::get('/user', fn (Request $request) => $request->user())->name('user');
        Route::get('/account', [AccountController::class, 'show'])->name('account.show');
        Route::put('/account', [AccountController::class, 'update'])->name('account.update');
        Route::put('/account/password', [AccountController::class, 'updatePassword'])->name('account.password');
        Route::get('/account/export', [AccountController::class, 'export'])->name('account.export');
        Route::delete('/account', [AccountController::class, 'destroy'])->name('account.destroy');
        Route::post('/account/two-factor/setup', [TwoFactorController::class, 'setup'])->name('account.two-factor.setup');
        Route::post('/account/two-factor/confirm', [TwoFactorController::class, 'confirm'])->name('account.two-factor.confirm');
        Route::post('/account/two-factor/recovery-codes', [TwoFactorController::class, 'regenerateRecoveryCodes'])->name('account.two-factor.recovery-codes');
        Route::delete('/account/two-factor', [TwoFactorController::class, 'destroy'])->name('account.two-factor.destroy');
    });
});
