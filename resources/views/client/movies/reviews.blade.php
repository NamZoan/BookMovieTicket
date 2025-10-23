@php
    // $movie: App\Models\Movie
    // $reviews: LengthAwarePaginator or Collection (approved + current user's)
    // $averageRating: float
@endphp

<div id="movie-reviews-root">
    @include('client.movies.partials.reviews', ['movie' => $movie])
</div>