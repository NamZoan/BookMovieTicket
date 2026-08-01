<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

if (!function_exists('movie_poster_url')) {
    function movie_poster_url(?string $url): string {
        if (empty($url) || trim($url) === '') {
            return asset('assets/img/default/cinema.jpg');
        }

        $url = trim($url);

        if (Str::startsWith($url, ['http://', 'https://'])) {
            return $url;
        }

        $supabaseUrl = env('SUPABASE_STORAGE_URL') ?: env('AWS_URL');
        if ($supabaseUrl && !Str::startsWith($url, ['storage/', 'assets/'])) {
            return rtrim($supabaseUrl, '/') . '/' . ltrim($url, '/');
        }

        $disk = config('filesystems.default', env('FILESYSTEM_DISK', 'local'));
        if ($disk === 's3') {
            return Storage::disk('s3')->url($url);
        }

        if (Str::startsWith($url, ['storage/', 'assets/'])) {
            return asset($url);
        }

        return asset('storage/' . ltrim($url, '/'));
    }
}
