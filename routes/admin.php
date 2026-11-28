<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CategoryFilmController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\FilmController;
use App\Http\Controllers\Admin\RatingFilmController;
use App\Http\Controllers\Admin\ReviewFilmController;
use App\Http\Controllers\Admin\UserController;
use App\Models\Film;
use App\Models\Review as ReviewModel;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'index'])->name('admin.login');
Route::post('/login', [AuthController::class, 'login'])->name('admin.login.post');

Route::middleware('auth:admin')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard', [
            'filmsCount' => Film::count(),
            'usersCount' => User::count(),
            'reviewsCount' => ReviewModel::count(),
        ]);
    })->name('admin.dashboard');

    Route::resource('countries', CountryController::class)
        ->except(['show'])
        ->names('admin.countries');

    Route::resource('categories', CategoryController::class)
        ->except(['show'])
        ->names('admin.categories');

    Route::resource('films', FilmController::class)
        ->except(['show'])
        ->names('admin.films');

    Route::resource('films.categories', CategoryFilmController::class)
        ->only(['index', 'create', 'store', 'destroy'])
        ->names('admin.films.categories');

    Route::get('users', [UserController::class, 'index'])->name('admin.users.index');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    Route::patch('users/{id}/restore', [UserController::class, 'restore'])->name('admin.users.restore');

    Route::get('reviews', [ReviewFilmController::class, 'index'])->name('admin.reviews.index');
    Route::patch('reviews/{review}/approved', [ReviewFilmController::class, 'approved'])->name('admin.reviews.approved');
    Route::delete('reviews/{review}', [ReviewFilmController::class, 'destroy'])->name('admin.reviews.destroy');

    Route::get('ratings', [RatingFilmController::class, 'index'])->name('admin.ratings.index');
    Route::delete('ratings/{rating}', [RatingFilmController::class, 'destroy'])->name('admin.ratings.destroy');

    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
});
