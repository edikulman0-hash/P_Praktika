<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CountryController;
use App\Http\Controllers\Api\V1\FilmController;
use App\Http\Controllers\Api\V1\GenderController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Открытые эндпоинты
    Route::get('/films', [FilmController::class, 'index']);
    Route::get('/films/{id}', [FilmController::class, 'show']);
    Route::get('/films/{filmId}/reviews', [FilmController::class, 'reviews']);

    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/countries', [CountryController::class, 'index']);
    Route::get('/genders', [GenderController::class, 'index']);

    Route::post('/auth/signup', [AuthController::class, 'signup']);
    Route::post('/auth/signin', [AuthController::class, 'signin']);

    // Закрытые эндпоинты (Этап 2)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/signout', [AuthController::class, 'signout']);
    });
});
