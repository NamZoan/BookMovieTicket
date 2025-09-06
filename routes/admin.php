<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MovieController;
use App\Http\Controllers\Admin\CinemaController;
use App\Http\Controllers\Admin\ScreenController;
use App\Http\Controllers\Admin\SeatController;
use App\Http\Controllers\Admin\ShowtimeController;
use App\Http\Controllers\Admin\PricingController;
use App\Http\Controllers\Admin\FoodItemController;
use App\Http\Controllers\Admin\PromotionController;
// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {

    // Guest routes (chưa đăng nhập)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login']);
    });

    // Authenticated admin routes
    Route::middleware(['admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


        Route::prefix('movies')->name('movies.')->group(function () {
            Route::get('/', [MovieController::class, 'index'])->name('index');
            Route::get('/create', [MovieController::class, 'create'])->name('create');

            Route::post('/', [MovieController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [MovieController::class, 'edit'])->name('edit');
            Route::put('/{id}', [MovieController::class, 'update'])->name('update');
            Route::delete('/{id}', [MovieController::class, 'destroy'])->name('destroy');
        });
        // Thêm các routes admin khác ở đây sau
        Route::resource('cinemas', CinemaController::class);
        Route::resource('screens', ScreenController::class);
        Route::resource('seats', SeatController::class);
        Route::resource('showtimes', ShowtimeController::class);
        
        // Showtime API routes
        Route::get('/showtimes/screen/{screenId}', [ShowtimeController::class, 'getScreenInfo'])->name('showtimes.screen-info');
        Route::get('/showtimes/movie/{movieId}', [ShowtimeController::class, 'getMovieInfo'])->name('showtimes.movie-info');
        Route::post('/showtimes/check-conflict', [ShowtimeController::class, 'checkConflict'])->name('showtimes.check-conflict');
        Route::get('/showtimes/by-screen-date', [ShowtimeController::class, 'getShowtimesByScreenAndDate'])->name('showtimes.by-screen-date');
        
        Route::resource('pricing', PricingController::class);
        Route::resource('food-items', FoodItemController::class);
        Route::resource('promotions', PromotionController::class);

        Route::post('/promotions/{promotion}/toggle-status', [PromotionController::class, 'toggleStatus'])->name('promotions.toggle-status');
        // Additional food item routes
        Route::post('/food-items/{food_item}/toggle-status', [FoodItemController::class, 'toggleStatus'])->name('food-items.toggle-status');
    });
});

