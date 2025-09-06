<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\CinemaController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

require __DIR__.'/admin.php';

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home', [HomeController::class, 'index'])->name('client.home');

// AJAX Routes for Home
Route::get('/movies-by-genre', [HomeController::class, 'getMoviesByGenre'])->name('movies.by-genre');
Route::get('/search-movies', [HomeController::class, 'searchMovies'])->name('movies.search');
Route::get('/movie-showtimes/{movie}', [HomeController::class, 'getMovieShowtimes'])->name('movies.showtimes');
Route::get('/cinema-locations', [HomeController::class, 'getCinemaLocations'])->name('cinemas.locations');
Route::post('/newsletter-subscribe', [HomeController::class, 'subscribeNewsletter'])->name('newsletter.subscribe');

// Movie Routes - Fixed order to prevent conflicts
Route::prefix('movies')->name('movies.')->group(function () {
    // Static routes first
    Route::get('/', [MovieController::class, 'index'])->name('index');
    Route::get('/now-showing', [MovieController::class, 'nowShowing'])->name('now-showing');
    Route::get('/coming-soon', [MovieController::class, 'comingSoon'])->name('coming-soon');
    Route::get('/ended', [MovieController::class, 'ended'])->name('ended');
    Route::get('/search', [MovieController::class, 'search'])->name('search');

    // Dynamic routes with specific patterns
    Route::get('/genre/{genre}', [MovieController::class, 'byGenre'])->name('genre');

    // AJAX routes with specific prefix to avoid conflicts
    Route::get('/{movie_id}/showtimes-ajax', [MovieController::class, 'showtimesAjax'])
        ->name('showtimes.ajax')
        ->where('movie_id', '[0-9]+');

    // Movie detail routes (should come last)
    Route::get('/{movie}', [MovieController::class, 'show'])->name('show');
    Route::get('/{movie_id}/showtimes', [MovieController::class, 'showtimes'])
        ->name('showtimes')
        ->where('movie_id', '[0-9]+');
    Route::get('/{movie}/reviews', [MovieController::class, 'reviews'])->name('reviews');
});

/*
|--------------------------------------------------------------------------
| API Routes for AJAX calls
|--------------------------------------------------------------------------
*/
Route::prefix('api/movies')->name('api.movies.')->group(function () {
    Route::get('/', [MovieController::class, 'apiIndex'])->name('index');
    Route::get('/{movie}', [MovieController::class, 'apiShow'])->name('show');
    Route::get('/{movie_id}/showtimes', [MovieController::class, 'apiShowtimes'])
        ->name('showtimes')
        ->where('movie_id', '[0-9]+');
});

// Cinema Routes
Route::prefix('cinemas')->name('cinemas.')->group(function () {
    Route::get('/', [CinemaController::class, 'index'])->name('index');
    Route::get('/{cinema}', [CinemaController::class, 'show'])->name('show');
    Route::get('/{cinema}/showtimes', [CinemaController::class, 'showtimes'])->name('showtimes');
    Route::get('/city/{city}', [CinemaController::class, 'byCity'])->name('city');
});

// Authentication Routes
Route::prefix('auth')->name('auth.')->group(function () {
    // Guest routes
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.post');
        Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->name('register.post');
        Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('forgot-password');
        Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('forgot-password.post');
        Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('reset-password');
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset-password.post');
    });

    // Authenticated routes
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });
});

// Booking Routes (Protected)
Route::middleware('auth')->prefix('booking')->name('booking.')->group(function () {
    Route::get('/create/{showtime}', [BookingController::class, 'create'])->name('create');
    Route::post('/store', [BookingController::class, 'store'])->name('store');
    Route::get('/seat-selection/{showtime}', [BookingController::class, 'seatSelection'])->name('seatSelection');
    Route::post('/select-seats', [BookingController::class, 'selectSeats'])->name('select-seats');
    Route::get('/payment/{booking}', [BookingController::class, 'payment'])->name('payment');
    Route::post('/process-payment', [BookingController::class, 'processPayment'])->name('process-payment');
    Route::get('/confirmation/{booking}', [BookingController::class, 'confirmation'])->name('confirmation');
    Route::get('/ticket/{booking}', [BookingController::class, 'ticket'])->name('ticket');
    Route::post('/cancel/{booking}', [BookingController::class, 'cancel'])->name('cancel');

    // AJAX Routes for booking
    Route::get('/available-seats/{showtime}', [BookingController::class, 'getAvailableSeats'])->name('available-seats');
    Route::post('/hold-seats', [BookingController::class, 'holdSeats'])->name('hold-seats');
    Route::post('/release-seats', [BookingController::class, 'releaseSeats'])->name('release-seats');
    Route::get('/pricing/{showtime}', [BookingController::class, 'getPricing'])->name('pricing');
});

// User Account Routes (Protected)
Route::middleware('auth')->prefix('account')->name('account.')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::get('/bookings', [UserController::class, 'bookings'])->name('bookings');
    Route::get('/bookings/{booking}', [UserController::class, 'bookingDetails'])->name('booking-details');
    Route::get('/favorites', [UserController::class, 'favorites'])->name('favorites');
    Route::post('/favorites/toggle', [UserController::class, 'toggleFavorite'])->name('favorites.toggle');
    Route::get('/loyalty-points', [UserController::class, 'loyaltyPoints'])->name('loyalty-points');
    Route::get('/change-password', [UserController::class, 'showChangePasswordForm'])->name('change-password');
    Route::post('/change-password', [UserController::class, 'changePassword'])->name('change-password.post');
    Route::get('/notifications', [UserController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/mark-read', [UserController::class, 'markNotificationsRead'])->name('notifications.mark-read');
});

// Public Pages
Route::get('/about', function () {
    return view('client.pages.about');
})->name('about');

Route::get('/contact', function () {
    return view('client.pages.contact');
})->name('contact');

Route::post('/contact', [HomeController::class, 'submitContact'])->name('contact.submit');

Route::get('/faq', function () {
    return view('client.pages.faq');
})->name('faq');

Route::get('/terms', function () {
    return view('client.pages.terms');
})->name('terms');

Route::get('/privacy', function () {
    return view('client.pages.privacy');
})->name('privacy');
