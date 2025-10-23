<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Review;
use App\Models\Movie;
use App\Models\Booking;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReviewPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create a new review for a movie.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Movie  $movie
     * @return bool
     */
    public function create(User $user, Movie $movie)
    {
        // 1. Check if the user has already reviewed this movie.
        $hasReviewed = Review::where('user_id', $user->user_id)
                             ->where('movie_id', $movie->movie_id)
                             ->exists();

        if ($hasReviewed) {
            return false;
        }

        // 2. Check if the user has a 'Confirmed' or 'Used' booking for this movie.
        $hasBooking = Booking::where('user_id', $user->user_id)
            ->whereIn('booking_status', ['Confirmed', 'Used'])
            ->whereHas('showtime', function ($query) use ($movie) {
                $query->where('movie_id', $movie->movie_id);
            })
            ->exists();

        return $hasBooking;
    }

    /**
     * Determine whether the user can update the review.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Review  $review
     * @return bool
     */
    public function update(User $user, Review $review)
    {
        return $user->user_id === $review->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the review.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Review  $review
     * @return bool
     */
    public function delete(User $user, Review $review)
    {
        return $user->user_id === $review->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can approve the review.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function approve(User $user)
    {
        return $user->isAdmin();
    }
}
