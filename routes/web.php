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
| - Authorization policies applied (IdeaPolicy, CommentPolicy)
| - Admin role enforcement on logs route
| - No validation  XSS possible (TODO)
| - Open Redirect vulnerability is intentional
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

    // Edit a comment
    Route::get('/ideas/{idea}/comments/{comment}/edit', [CommentController::class, 'edit'])
        ->name('comments.edit');

    // Update a comment
    Route::put('/ideas/{idea}/comments/{comment}', [CommentController::class, 'update'])
        ->name('comments.update');

    // Delete a comment
    Route::delete('/ideas/{idea}/comments/{comment}', [CommentController::class, 'destroy'])
        ->name('comments.destroy');

    // Logs page — admin only
    Route::get('/logs', [LogController::class, 'index'])
        ->middleware('admin')
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
