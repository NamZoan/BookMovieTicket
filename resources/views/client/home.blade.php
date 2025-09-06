@extends('client.layouts.app')

@section('title', 'Home - Movie Booking System')

@section('content')
<!-- Hero Slider Section -->
<section class="w3l-main-slider position-relative" id="home">
    <div class="companies20-content">
        <div class="owl-one owl-carousel owl-theme">
            @forelse($featuredMovies as $index => $movie)
            <div class="item">
                <li>
                    <div class="slider-info banner-view bg bg{{ $index + 1 }}"
                         style="background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('{{ $movie->poster_url ?? asset('assets/images/default-movie.jpg') }}')">
                        <div class="banner-info">
                            <h3>{{ $movie->title }}</h3>
                            <p>{{ Str::limit($movie->description, 120) }}</p>
                            <div class="movie-meta">
                                <span class="rating">
                                    <i class="fa fa-star"></i> {{ $movie->rating ?? 'N/A' }}
                                </span>
                                <span class="duration">
                                    <i class="fa fa-clock-o"></i> {{ floor($movie->duration / 60) }}h {{ $movie->duration % 60 }}m
                                </span>
                                <span class="genre">{{ $movie->genre }}</span>
                            </div>
                            @if($movie->trailer_url)
                            <a href="#trailer-modal-{{ $movie->movie_id }}" class="popup-with-zoom-anim play-view1">
                                <span class="video-play-icon">
                                    <span class="fa fa-play"></span>
                                </span>
                                <h6>Watch Trailer</h6>
                            </a>
                            <div id="trailer-modal-{{ $movie->movie_id }}" class="zoom-anim-dialog mfp-hide">
                                <iframe src="{{ $movie->trailer_url }}" allow="autoplay; fullscreen" allowfullscreen=""></iframe>
                            </div>
                            @endif
                            <a href="{{ route('movies.show', $movie->movie_id) }}" class="btn btn-primary mt-3">
                                <i class="fa fa-ticket"></i> Book Now
                            </a>
                        </div>
                    </div>
                </li>
            </div>
            @empty
            <div class="item">
                <li>
                    <div class="slider-info banner-view bg bg1">
                        <div class="banner-info">
                            <h3>Welcome to Cinema Booking</h3>
                            <p>Book your favorite movies online with ease</p>
                        </div>
                    </div>
                </li>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Popular Movies Section -->
<section class="w3l-grids">
    <div class="grids-main py-5">
        <div class="container py-lg-3">
            <div class="headerhny-title">
                <div class="w3l-title-grids">
                    <div class="headerhny-left">
                        <h3 class="hny-title">Popular Movies</h3>
                    </div>
                    <div class="headerhny-right text-lg-right">
                        <h4><a class="show-title" href="{{ route('movies.index') }}">Show all</a></h4>
                    </div>
                </div>
            </div>
            <div class="w3l-populohny-grids">
                @forelse($popularMovies as $movie)
                <div class="item vhny-grid">
                    <div class="box16">
                        <a href="{{ route('movies.show', $movie->movie_id) }}">
                            <figure>
                                <img class="img-fluid" src="{{ $movie->display_image_url ?? ($movie->poster_url ? asset('storage/' . $movie->poster_url) : asset('assets/img/default/cinema.jpg')) }}"
                                     alt="{{ $movie->title }}">
                                <div class="movie-overlay">
                                    <div class="rating-badge">
                                        <i class="fa fa-star"></i> {{ $movie->rating ?? 'N/A' }}
                                    </div>
                                </div>
                            </figure>
                            <div class="box-content">
                                <h3 class="title">{{ $movie->title }}</h3>
                                <h4>
                                    <span class="post">
                                        <span class="fa fa-clock-o"></span>
                                        {{ floor($movie->duration / 60) }}h {{ $movie->duration % 60 }}m
                                    </span>
                                    <span class="post fa fa-heart text-right add-to-favorites"
                                          data-movie-id="{{ $movie->movie_id }}"></span>
                                </h4>
                                <div class="movie-info">
                                    <span class="genre">{{ $movie->genre }}</span>
                                    <span class="status status-{{ strtolower(str_replace(' ', '-', $movie->status)) }}">
                                        {{ $movie->status }}
                                    </span>
                                </div>
                            </div>
                            <span class="fa fa-play video-icon" aria-hidden="true"></span>
                        </a>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center">
                    <p>No popular movies available at the moment.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</section>

<!-- New Releases Section -->
<section class="w3l-grids">
    <div class="grids-main py-5">
        <div class="container py-lg-3">
            <div class="headerhny-title">
                <div class="w3l-title-grids">
                    <div class="headerhny-left">
                        <h3 class="hny-title">New Releases</h3>
                    </div>
                    <div class="headerhny-right text-lg-right">
                        <h4><a class="show-title" href="{{ route('movies.index', ['filter' => 'new']) }}">Show all</a></h4>
                    </div>
                </div>
            </div>
            <div class="owl-three owl-carousel owl-theme">
                @forelse($nowShowingMovies as $movie)
                <div class="item vhny-grid">
                    <div class="box16 mb-0">
                        <a href="{{ route('movies.show', $movie->movie_id) }}">
                            <figure>
                                <img class="img-fluid" src="{{ $movie->display_image_url ?? ($movie->poster_url ? asset('storage/' . $movie->poster_url) : asset('assets/img/default/cinema.jpg')) }}" alt="{{ $movie->title }}">
                                <div class="movie-overlay">
                                    <div class="rating-badge">
                                        <i class="fa fa-star"></i> {{ $movie->rating ?? 'N/A' }}
                                    </div>
                                    @if($movie->age_rating)
                                    <div class="age-rating">{{ $movie->age_rating }}</div>
                                    @endif
                                </div>
                            </figure>
                            <div class="box-content">
                                <h4>
                                    <span class="post">
                                        <span class="fa fa-clock-o"></span>
                                        {{ floor($movie->duration / 60) }}h {{ $movie->duration % 60 }}m
                                    </span>
                                    <span class="post fa fa-heart text-right add-to-favorites"
                                          data-movie-id="{{ $movie->movie_id }}"></span>
                                </h4>
                            </div>
                            <span class="fa fa-play video-icon" aria-hidden="true"></span>
                        </a>
                    </div>
                    <h3><a class="title-gd" href="{{ route('movies.show', $movie->movie_id) }}">{{ $movie->title }}</a></h3>
                    <p>{{ Str::limit($movie->description, 80) }}</p>
                    <div class="movie-details mb-3">
                        <span class="genre-tag">{{ $movie->genre }}</span>
                        <span class="language">{{ $movie->language }}</span>
                        @if($movie->release_date)
                        <span class="release-date">{{ \Carbon\Carbon::parse($movie->release_date)->format('M Y') }}</span>
                        @endif
                    </div>
                    <div class="button-center text-center mt-4">
                        <a href="{{ route('movies.show', $movie->movie_id) }}" class="btn watch-button">Book Now</a>
                    </div>
                </div>
                @empty
                <div class="item vhny-grid">
                    <div class="text-center">
                        <p>No new releases available.</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</section>

<!-- Coming Soon Section -->
@if($upcomingMovies->count() > 0)
<section class="w3l-grids bg-light">
    <div class="grids-main py-5">
        <div class="container py-lg-3">
            <div class="headerhny-title">
                <div class="w3l-title-grids">
                    <div class="headerhny-left">
                        <h3 class="hny-title">Coming Soon</h3>
                    </div>
                    <div class="headerhny-right text-lg-right">
                        <h4><a class="show-title" href="{{ route('movies.index', ['status' => 'coming-soon']) }}">Show all</a></h4>
                    </div>
                </div>
            </div>
            <div class="row">
                @foreach($upcomingMovies as $movie)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="upcoming-movie-card">
                        <div class="movie-poster">
                            <img src="{{ $movie->display_image_url ?? ($movie->poster_url ? asset('storage/' . $movie->poster_url) : asset('assets/img/default/cinema.jpg')) }}"
                                 alt="{{ $movie->title }}" class="img-fluid">
                            <div class="coming-soon-badge">Coming Soon</div>
                        </div>
                        <div class="movie-info">
                            <h4>{{ $movie->title }}</h4>
                            <p class="release-date">
                                <i class="fa fa-calendar"></i>
                                {{ \Carbon\Carbon::parse($movie->release_date)->format('M d, Y') }}
                            </p>
                            <p class="genre">{{ $movie->genre }}</p>
                            <a href="{{ route('movies.show', $movie->movie_id) }}" class="btn btn-outline-primary">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

<!-- Featured Slider Section -->
<section class="w3l-mid-slider position-relative">
    <div class="companies20-content">
        <div class="owl-mid owl-carousel owl-theme">
            @forelse($latestTrailers->take(3) as $movie)
            <div class="item">
                <li>
                    <div class="slider-info mid-view bg bg{{ $loop->index + 1 }}"
                         style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('{{ $movie->poster_url ?? asset('assets/images/default-movie.jpg') }}')">
                        <div class="container">
                            <div class="mid-info">
                                <span class="sub-text">{{ $movie->genre }}</span>
                                <h3>{{ $movie->title }}</h3>
                                <p>{{ $movie->release_date ? \Carbon\Carbon::parse($movie->release_date)->format('Y') : '' }} ‧ {{ $movie->genre }} ‧ {{ floor($movie->duration / 60) }}h {{ $movie->duration % 60 }}m</p>
                                <div class="movie-rating mb-3">
                                    <span class="rating-stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($movie->rating && $i <= floor($movie->rating/2))
                                                <i class="fa fa-star"></i>
                                            @else
                                                <i class="fa fa-star-o"></i>
                                            @endif
                                        @endfor
                                    </span>
                                    <span class="rating-number">{{ $movie->rating ?? 'N/A' }}/10</span>
                                </div>
                                <a class="watch" href="{{ route('movies.show', $movie->movie_id) }}">
                                    <span class="fa fa-play" aria-hidden="true"></span>
                                    Book Tickets
                                </a>
                                @if($movie->trailer_url)
                                <a class="watch ml-3" href="{{ $movie->trailer_url }}" target="_blank">
                                    <span class="fa fa-youtube-play" aria-hidden="true"></span>
                                    Watch Trailer
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </li>
            </div>
            @empty
            <div class="item">
                <li>
                    <div class="slider-info mid-view bg bg1">
                        <div class="container">
                            <div class="mid-info">
                                <span class="sub-text">Entertainment</span>
                                <h3>Book Your Movie Tickets</h3>
                                <p>Experience the best movies in town</p>
                            </div>
                        </div>
                    </div>
                </li>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Today's Showtimes Section -->
@if($todayShowtimes->count() > 0)
<section class="w3l-showtimes py-5">
    <div class="container">
        <div class="headerhny-title mb-4">
            <h3 class="hny-title text-center">Today's Showtimes</h3>
            <p class="text-center">Quick booking for today's shows</p>
        </div>
        <div class="row">
            @foreach($todayShowtimes->take(6) as $showtime)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="showtime-card">
                    <div class="showtime-header">
                        <h5>{{ $showtime->movie->title }}</h5>
                        <span class="cinema-name">{{ $showtime->screen->cinema->name }}</span>
                    </div>
                    <div class="showtime-details">
                        <div class="time-slot">
                            <i class="fa fa-clock-o"></i>
                            {{ \Carbon\Carbon::parse($showtime->show_time)->format('h:i A') }}
                        </div>
                        <div class="available-seats">
                            <i class="fa fa-users"></i>
                            {{ $showtime->available_seats }} seats available
                        </div>
                        <div class="price">
                            <i class="fa fa-tag"></i>
                            ${{ number_format($showtime->base_price, 2) }}
                        </div>
                    </div>
                    <a href="{{ route('booking.create', ['showtime' => $showtime->showtime_id]) }}"
                       class="btn btn-primary btn-sm">Book Now</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Movie Statistics Section -->
<section class="w3l-stats py-5 bg-dark text-white">
    <div class="container">
        <div class="row text-center">
            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fa fa-film fa-3x"></i>
                    </div>
                    <div class="stat-number">{{ $movieStats['total_movies'] }}</div>
                    <div class="stat-label">Total Movies</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fa fa-play-circle fa-3x"></i>
                    </div>
                    <div class="stat-number">{{ $movieStats['now_showing'] }}</div>
                    <div class="stat-label">Now Showing</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fa fa-calendar fa-3x"></i>
                    </div>
                    <div class="stat-number">{{ $movieStats['coming_soon'] }}</div>
                    <div class="stat-label">Coming Soon</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fa fa-building fa-3x"></i>
                    </div>
                    <div class="stat-number">{{ $movieStats['total_cinemas'] }}</div>
                    <div class="stat-label">Cinema Locations</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Genre Filter Section -->
@if($genres->count() > 0)
<section class="w3l-genres py-5">
    <div class="container">
        <div class="headerhny-title mb-4">
            <h3 class="hny-title text-center">Browse by Genre</h3>
        </div>
        <div class="genre-filters text-center">
            @foreach($genres as $genre)
            <a href="{{ route('movies.index', ['genre' => $genre]) }}"
               class="genre-tag btn btn-outline-primary m-1">
                {{ $genre }}
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection

@push('styles')
<style>
/* Custom styles for movie booking system */
.movie-overlay {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 5;
}

.rating-badge {
    background: rgba(255, 193, 7, 0.9);
    color: #000;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: bold;
}

.age-rating {
    background: rgba(220, 53, 69, 0.9);
    color: white;
    padding: 5px 8px;
    border-radius: 3px;
    font-size: 10px;
    margin-top: 5px;
}

.movie-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 10px;
}

.genre {
    font-size: 12px;
    color: #666;
}

.status {
    font-size: 11px;
    padding: 3px 8px;
    border-radius: 12px;
    color: white;
}

.status-now-showing {
    background: #28a745;
}

.status-coming-soon {
    background: #17a2b8;
}

.upcoming-movie-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    transition: transform 0.3s;
}

.upcoming-movie-card:hover {
    transform: translateY(-5px);
}

.movie-poster {
    position: relative;
}

.coming-soon-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    background: #dc3545;
    color: white;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: bold;
}

.upcoming-movie-card .movie-info {
    padding: 20px;
}

.showtime-card {
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    transition: all 0.3s;
}

.showtime-card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.showtime-header h5 {
    margin: 0 0 5px 0;
    color: #333;
}

.cinema-name {
    color: #666;
    font-size: 14px;
}

.showtime-details {
    margin: 15px 0;
}

.showtime-details div {
    margin: 5px 0;
    font-size: 14px;
}

.stat-item {
    padding: 20px;
}

.stat-icon {
    margin-bottom: 15px;
    opacity: 0.8;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 10px;
}

.stat-label {
    font-size: 1.1rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.genre-tag {
    margin: 5px;
    transition: all 0.3s;
}

.genre-tag:hover {
    transform: scale(1.05);
}

.movie-details {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.genre-tag, .language, .release-date {
    background: #f8f9fa;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 12px;
    color: #666;
}

.add-to-favorites {
    cursor: pointer;
    transition: color 0.3s;
}

.add-to-favorites:hover {
    color: #dc3545 !important;
}

.movie-meta {
    display: flex;
    gap: 15px;
    margin: 15px 0;
    flex-wrap: wrap;
}

.movie-meta span {
    background: rgba(255,255,255,0.2);
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.rating-stars {
    margin-right: 10px;
}

.rating-number {
    font-weight: bold;
}

@media (max-width: 768px) {
    .movie-meta {
        flex-direction: column;
        gap: 10px;
    }

    .showtime-card {
        margin-bottom: 15px;
    }

    .stat-number {
        font-size: 2rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize carousels
    $('.owl-one').owlCarousel({
        loop: true,
        margin: 0,
        nav: true,
        autoplay: true,
        autoplayTimeout: 5000,
        responsive: {
            0: { items: 1 },
            600: { items: 1 },
            1000: { items: 1 }
        }
    });

    $('.owl-three').owlCarousel({
        loop: true,
        margin: 20,
        nav: true,
        autoplay: true,
        autoplayTimeout: 4000,
        responsive: {
            0: { items: 1 },
            600: { items: 2 },
            1000: { items: 3 }
        }
    });

    $('.owl-mid').owlCarousel({
        loop: true,
        margin: 0,
        nav: true,
        autoplay: true,
        autoplayTimeout: 6000,
        responsive: {
            0: { items: 1 },
            600: { items: 1 },
            1000: { items: 1 }
        }
    });

    // Add to favorites functionality
    $('.add-to-favorites').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var movieId = $(this).data('movie-id');
        var $this = $(this);

        // Toggle favorite state
        if ($this.hasClass('favorited')) {
            $this.removeClass('favorited').css('color', '');
            // AJAX call to remove from favorites
        } else {
            $this.addClass('favorited').css('color', '#dc3545');
            // AJAX call to add to favorites
        }

        // You can implement AJAX call here to save/remove favorites
        // $.post('/favorites/toggle', {movie_id: movieId}, function(response) {
        //     // Handle response
        // });
    });

    // Quick search functionality (if search box exists)
    $('#movie-search').on('input', function() {
        var query = $(this).val();
        if (query.length >= 2) {
            $.get('/search-movies', {q: query}, function(response) {
                if (response.success) {
                    // Display search results
                    displaySearchResults(response.movies);
                }
            });
        }
    });

    function displaySearchResults(movies) {
        var html = '';
        movies.forEach(function(movie) {
            html += '<div class="search-result-item">';
            html += '<img src="' + ($movie->display_image_url ?? ($movie->poster_url ? asset('storage/' . $movie->poster_url) : asset('assets/img/default/cinema.jpg'))) + '" alt="' + movie.title + '">';
            html += '<div class="movie-info">';
            html += '<h6>' + movie.title + '</h6>';
            html += '<span class="rating"><i class="fa fa-star"></i> ' + (movie.rating || 'N/A') + '</span>';
            html += '</div>';
            html += '</div>';
        });
        $('#search-results').html(html).show();
    }
});
</script>
@endpush
