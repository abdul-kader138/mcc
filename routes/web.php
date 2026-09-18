<?php

use App\Http\Controllers\Auth\CustomerGoogleAuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\StaffVerifyEmailController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemLikeController;
use App\Http\Controllers\ModelConfigurationController;
use App\Http\Controllers\QuoteRequestController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::redirect('/models', '/', 301);
Route::get('/models/{item:slug}', [ItemController::class, 'show'])->name('items.show');
Route::get('/models/{item:slug}/download', [ItemController::class, 'download'])->name('items.download');
Route::post('/models/{item:slug}/like', [ItemLikeController::class, 'toggle'])->middleware('throttle:30,1')->name('items.like');
Route::post('/models/{item:slug}/configurations', [ModelConfigurationController::class, 'store'])->middleware('throttle:20,1')->name('items.configurations.store');
Route::get('/models/{item:slug}/configurations/{configuration:token}', [ModelConfigurationController::class, 'show'])->name('items.configurations.show');
Route::post('/models/{item:slug}/quote-requests', [QuoteRequestController::class, 'store'])->middleware('throttle:5,1')->name('items.quote-requests.store');

// Staff (panel) email verification. No auth middleware on purpose — the
// `signed` middleware secures it — so the link works when opened on a
// device the user isn't logged in on. Customers use `verification.verify`
// in routes/api.php instead. See App\Notifications\Auth\VerifyEmail.
Route::get('/admin/email/verify/{id}/{hash}', StaffVerifyEmailController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('staff.verification.verify');

Route::post('/locale', [LocaleController::class, 'update'])->name('locale.update');

Route::prefix('admin/auth/google')->name('auth.google.')->middleware('throttle:30,1')->group(function () {
    Route::get('/redirect', [GoogleAuthController::class, 'redirect'])->name('redirect');
    Route::get('/callback', [GoogleAuthController::class, 'callback'])->name('callback');
});

// Customer-facing "Sign in with Google" — a full browser redirect flow
// (OAuth can't be an XHR call), separate from the admin one above so a
// customer login never assigns the admin's 'panel_user' role. See
// App\Http\Controllers\Auth\CustomerGoogleAuthController.
Route::prefix('auth/google')->name('customer.auth.google.')->middleware('throttle:30,1')->group(function () {
    Route::get('/redirect', [CustomerGoogleAuthController::class, 'redirect'])->name('redirect');
    Route::get('/callback', [CustomerGoogleAuthController::class, 'callback'])->name('callback');
});
