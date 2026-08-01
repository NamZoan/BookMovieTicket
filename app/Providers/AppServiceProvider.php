<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (env('APP_ENV') !== 'local') {
            \Illuminate\Support\Facades\URL::forceScheme('https'); // Thêm dấu \ ở đầu
        }
    }
}

if (!function_exists('movie_poster_url')) {
    function movie_poster_url(?string $url): string {
        if (blank($url)) {
            return asset('assets/img/default/cinema.jpg');
        }

        if (\Illuminate\Support\Str::startsWith($url, ['http://', 'https://'])) {
            return $url;
        }

        if (\Illuminate\Support\Str::startsWith($url, ['storage/', 'assets/'])) {
            return asset($url);
        }

        return asset('storage/' . ltrim($url, '/'));
    }
}
