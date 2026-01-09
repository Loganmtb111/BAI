<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IdeaController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\CookieConsentController;

/*
|--------------------------------------------------------------------------
| Web Routes — Sandbox Version
|--------------------------------------------------------------------------
|
| SECURITY NOTES:
| - No authorization policies applied yet
| - No admin role enforcement
| - No validation  XSS possible
| - Open Redirect vulnerability is intentional
| - Logs are visible to any authenticated user
|
| Secure all these aspects TODO
|
*/

// ------------- Home page -------------
// Redirect the homepage to the list of ideas
Route::get('/', function () {
    return redirect()->route('ideas.index');
});

// ------------- Protected routes (authentication required) -------------
Route::middleware(['auth'])->group(function () {

    // Full CRUD for ideas
    Route::resource('ideas', IdeaController::class);

    // Create comment on an idea
    Route::post('/ideas/{idea}/comments', [CommentController::class, 'store'])
        ->name('comments.store');

    // Delete a comment (no policy yet → intentional vulnerability)
    Route::delete('/ideas/{idea}/comments/{comment}', [CommentController::class, 'destroy'])
        ->name('comments.destroy');

    // Logs page — currently no admin restriction (intentional)
    Route::get('/logs', [LogController::class, 'index'])
        ->name('logs.index');

    // Cookie consent route
    Route::post('/cookie-consent', [CookieConsentController::class, 'store'])
        ->name('cookie.consent');

    // Cookie toggle route (switch between accept/refuse)
    Route::post('/cookie-toggle', [CookieConsentController::class, 'toggle'])
        ->name('cookie.toggle');
});

// ------------- Intentional Open Redirect Vulnerability -------------
Route::get('/redirect', [RedirectController::class, 'vulnerableRedirect'])
    ->name('redirect.vulnerable');

// ------------- Privacy Policy Page (accessible to all) -------------
Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');

// ------------- Authentication routes from Breeze -------------
require __DIR__.'/auth.php';
