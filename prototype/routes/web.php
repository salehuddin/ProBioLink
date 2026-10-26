<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

Route::get('/robots.txt', [SitemapController::class, 'robots']);
Route::get('/sitemap.xml', [SitemapController::class, 'index']);

// Server-rendered so shared links are indexable and unfurl correctly.
Route::get('/u/{username}', [PublicProfileController::class, 'show'])->name('profile.show');

// Everything else is the SPA.
Route::get('/{any?}', fn () => view('app'))->where('any', '.*');
