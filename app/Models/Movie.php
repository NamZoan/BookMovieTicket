<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Movie extends Model
{
    use HasFactory;

    protected $table = 'movies';

    protected $primaryKey = 'movie_id';

    protected $fillable = [
        'title',
        'original_title',
        'description',
        'duration',
        'release_date',
        'director',
        'cast',
        'genre',
        'language',
        'country',
        'rating',
        'age_rating',
        'poster_url',
        'trailer_url',
        'status',
    ];

    public $timestamps = true;

    protected $casts = [
        'release_date' => 'date',
        'rating' => 'float',
        'duration' => 'integer',
    ];

    public function showtimes()
    {
        return $this->hasMany(Showtime::class, 'movie_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'movie_id', 'movie_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Query scopes
    |--------------------------------------------------------------------------
    */
    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeNowShowing(Builder $query): Builder
    {
        return $query->status('Now Showing');
    }

    public function scopeComingSoon(Builder $query): Builder
    {
        return $query->status('Coming Soon')->orderBy('release_date');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->whereIn('status', ['Now Showing', 'Coming Soon'])
            ->where('rating', '>=', 7.5)
            ->orderByDesc('rating')
            ->orderByDesc('release_date');
    }

    public function scopeTopRated(Builder $query): Builder
    {
        return $query->whereNotNull('rating')
            ->orderByDesc('rating')
            ->orderByDesc('release_date');
    }

    public function scopeTrending(Builder $query): Builder
    {
        return $query->nowShowing()
            ->orderByDesc('updated_at')
            ->orderByDesc('rating');
    }

    public function scopeGenre(Builder $query, ?string $genre): Builder
    {
        if (blank($genre)) {
            return $query;
        }

        return $query->where('genre', 'LIKE', '%' . $genre . '%');
    }

    public function scopeMinimumRating(Builder $query, ?float $rating): Builder
    {
        if (blank($rating)) {
            return $query;
        }

        return $query->where('rating', '>=', $rating);
    }

    public function scopeLanguage(Builder $query, ?string $language): Builder
    {
        if (blank($language)) {
            return $query;
        }

        return $query->where('language', $language);
    }

    public function scopeAgeRating(Builder $query, ?string $ageRating): Builder
    {
        if (blank($ageRating)) {
            return $query;
        }

        return $query->where('age_rating', $ageRating);
    }

    public function scopeReleased(Builder $query): Builder
    {
        return $query->whereNotNull('release_date')
            ->where('release_date', '<=', now());
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */
    public function getGenreListAttribute(): array
    {
        return collect(explode(',', (string) $this->genre))
            ->map(static fn ($value) => trim($value))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Include aggregated review data on the query.
     */
    public function scopeWithRatingStats(Builder $query): Builder
    {
        return $query->withAvg('reviews as reviews_avg_rating', 'rating')
            ->withCount('reviews');
    }

    /**
     * Resolve the rating that should be shown to end users.
     */
    public function getComputedRatingAttribute(): ?float
    {
        if (!is_null($this->reviews_avg_rating ?? null)) {
            return round(((float) $this->reviews_avg_rating) * 2, 1);
        }

        return $this->rating;
    }
}
