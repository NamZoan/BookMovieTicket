<?php

use Illuminate\Support\Str;

if (!function_exists('movie_poster_url')) {
    function movie_poster_url(?string $url): string {
        if (blank($url)) {
            return asset('assets/img/default/cinema.jpg');
        }

        if (Str::startsWith($url, ['http://', 'https://'])) {
            return $url;
        }

        if (Str::startsWith($url, ['storage/', 'assets/'])) {
            return asset($url);
        }

        return asset('storage/' . ltrim($url, '/'));
    }
}
