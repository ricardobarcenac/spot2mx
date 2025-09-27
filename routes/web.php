<?php

use App\Http\Controllers\Web\UrlShortenerController;
use App\Http\Controllers\Api\UrlShortenerController as ApiUrlShortenerController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

// Ruta para obtener CSRF token para APIs REST
Route::get('/csrf-token', function () {
    return response()->json([
        'token' => csrf_token(),
        'expires_at' => now()->addMinutes(config('session.lifetime'))->toISOString()
    ]);
})->middleware('auth')->name('csrf.token');

# Web Routes - Vistas
Route::get('short_urls', [UrlShortenerController::class, 'index'])->name('short_urls.index');
Route::get('short_urls/create', [UrlShortenerController::class, 'create'])->name('short_urls.create');
Route::get('short_urls/edit/{urlShortener}', [UrlShortenerController::class, 'edit'])->name('short_urls.edit');
Route::get('short_urls/shortcut/{shortUrl}', [UrlShortenerController::class, 'redirect'])->name('short_urls.redirect');

# APIs - CRUD
Route::prefix('api')->middleware(['auth', 'verified'])->group(function () {
    Route::get('shortcuts', [ApiUrlShortenerController::class, 'index'])->name('api.shortcuts.index');
    Route::post('shortcuts', [ApiUrlShortenerController::class, 'store'])->name('api.shortcuts.store');
    Route::put('shortcuts/{urlShortener}', [ApiUrlShortenerController::class, 'update'])->name('api.shortcuts.update');
    Route::delete('shortcuts/{urlShortener}', [ApiUrlShortenerController::class, 'destroy'])->name('api.shortcuts.destroy');
});

# API - Redirección fuera del middleware
Route::get('api/redirect/{shortUrl}', [ApiUrlShortenerController::class, 'redirect'])->name('api.redirect');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';