<?php

use App\Http\Controllers\Api\BlockController;
use App\Http\Controllers\Api\InquiryController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Public reads.
Route::get('/profiles', [ProfileController::class, 'index']);
Route::get('/profiles/{profile}', [ProfileController::class, 'show']);

// Everything that writes belongs to a signed-in owner.
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/profiles/{profile}/inquiries', [InquiryController::class, 'index']);

    Route::patch('/profiles/{profile}', [ProfileController::class, 'update']);
    Route::post('/profiles/{profile}/presets', [ProfileController::class, 'applyPreset']);

    Route::post('/profiles/{profile}/blocks', [BlockController::class, 'store']);
    Route::patch('/profiles/{profile}/blocks', [ProfileController::class, 'syncBlocks']);
    Route::patch('/profiles/{profile}/blocks/{block}', [BlockController::class, 'update']);
    Route::delete('/profiles/{profile}/blocks/{block}', [BlockController::class, 'destroy']);
});
