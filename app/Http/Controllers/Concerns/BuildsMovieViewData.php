<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Movie;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait BuildsMovieViewData
{
    /**
     * Prepare a lightweight array representation of a movie for list/card UIs.
     */
    protected function mapMovieForCard(Movie $movie, array $overrides = []): array
    {
        $base = [
            'id' => $movie->movie_id,
            'title' => $movie->title,
            'original_title' => $movie->original_title,
            'summary' => Str::limit(strip_tags((string) $movie->description), 140),
            'duration' => $this->formatDuration($movie->duration),
            'release_date' => optional($movie->release_date)->format('d/m/Y'),
            'genres' => $this->extractGenres($movie->genre),
            'language' => $movie->language,
            'country' => $movie->country,
            'rating' => $this->formatRating($this->resolveMovieRating($movie)),
            'age_rating' => $movie->age_rating,
            'status' => $movie->status,
            'poster_url' => $this->resolvePosterUrl($movie->poster_url),
            'trailer_url' => $this->resolveTrailerUrl($movie->trailer_url),
            'details_url' => route('movies.showtimes', $movie->movie_id),
            'book_url' => route('movies.showtimes', $movie->movie_id),
            'badges' => $this->buildBadgeMeta($movie),
        ];

        return array_replace_recursive($base, $overrides);
    }

    /**
     * Produce the payload expected by hero/slider components.
     */
    protected function mapMovieForHero(Movie $movie): array
    {
        return $this->mapMovieForCard($movie, [
            'summary' => Str::limit(strip_tags((string) $movie->description), 220),
            'background_image' => $this->resolvePosterUrl($movie->poster_url),
            'cta' => [
                'label' => __('Đặt vé ngay'),
                'url' => route('movies.showtimes', $movie->movie_id),
            ],
        ]);
    }

    /**
     * Convert a showtime model into a compact array for quick booking widgets.
     */
    protected function mapShowtime($showtime): array
    {
        $cinema = optional(optional($showtime->screen)->cinema);

        return [
            'id' => $showtime->showtime_id,
            'movie_title' => optional($showtime->movie)->title,
            'movie_id' => optional($showtime->movie)->movie_id,
            'poster_url' => $showtime->movie
                ? $this->resolvePosterUrl($showtime->movie->poster_url)
                : $this->defaultPosterUrl(),
            'start_time' => optional($showtime->show_time)->format('H:i'),
            'date' => optional($showtime->show_date)->format('d/m/Y'),
            'available_seats' => $showtime->available_seats,
            'screen' => optional($showtime->screen)->screen_name,
            'cinema' => [
                'name' => $cinema->name,
                'address' => $cinema->address,
                'city' => $cinema->city,
            ],
            'booking_url' => route('booking.seatSelection', ['showtime' => $showtime->showtime_id]),
        ];
    }

    /**
     * Normalize a collection of movies into card arrays.
     */
    protected function normalizeMovies(Collection $movies): Collection
    {
        return $movies->map(fn (Movie $movie) => $this->mapMovieForCard($movie));
    }

    /**
     * @return array<string>
     */
    protected function extractGenres(?string $genres): array
    {
        return collect(explode(',', (string) $genres))
            ->map(static fn ($genre) => trim($genre))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    protected function formatDuration(?int $minutes): ?string
    {
        if (!$minutes || $minutes <= 0) {
            return null;
        }

        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        return $hours > 0
            ? sprintf('%dh %02d', $hours, $mins)
            : sprintf('%d phút', $mins);
    }

    protected function formatRating($rating): ?string
    {
        if (blank($rating)) {
            return null;
        }

        return number_format((float) $rating, 1);
    }

    protected function resolvePosterUrl(?string $posterUrl): string
    {
        if (blank($posterUrl)) {
            return $this->defaultPosterUrl();
        }

        if (Str::startsWith($posterUrl, ['http://', 'https://'])) {
            return $posterUrl;
        }

        if (Str::startsWith($posterUrl, ['storage/', 'assets/'])) {
            return asset($posterUrl);
        }

        return asset('storage/' . ltrim($posterUrl, '/'));
    }

    protected function resolveTrailerUrl(?string $trailerUrl): string
    {
        if (blank($trailerUrl)) {
            return $this->defaultTrailerUrl();
        }

        return $trailerUrl;
    }

    protected function defaultPosterUrl(): string
    {
        return asset('assets/img/default/cinema.jpg');
    }

    protected function defaultTrailerUrl(): string
    {
        return 'https://www.youtube.com/embed/tgbNymZ7vqY';
    }

    protected function buildBadgeMeta(Movie $movie): array
    {
        $rating = $this->resolveMovieRating($movie);

        return [
            'status' => [
                'label' => $movie->status,
                'variant' => match ($movie->status) {
                    'Now Showing' => 'danger',
                    'Coming Soon' => 'warning',
                    default => 'secondary',
                },
            ],
            'rating' => $this->formatRating($rating),
        ];
    }

    protected function resolveMovieRating(Movie $movie): ?float
    {
        return $movie->computed_rating ?? $movie->rating;
    }
}
