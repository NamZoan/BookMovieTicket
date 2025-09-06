@extends('client.layouts.app')

@section('title', 'Movies - CinemaBook')

@section('content')
<div class="container py-5">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <h1 class="page-title">All Movies</h1>
            <p class="page-subtitle">Discover amazing movies and book your tickets</p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <div class="movie-stats">
                <span class="stat-item">
                    <strong>{{ $stats['total'] }}</strong> Total Movies
                </span>
                <span class="stat-item ms-3">
                    <strong>{{ $stats['now_showing'] }}</strong> Now Showing
                </span>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="filters-sidebar">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-filter me-2"></i>Filters</h5>
                    </div>
                    <div class="card-body">
                        <form id="filter-form" method="GET">
                            <!-- Status Filter -->
                            <div class="filter-group mb-4">
                                <h6 class="filter-title">Status</h6>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" value="" id="status-all"
                                           {{ !request('status') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status-all">All Movies</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" value="now-showing" id="status-showing"
                                           {{ request('status') == 'now-showing' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status-showing">Now Showing</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" value="coming-soon" id="status-coming"
                                           {{ request('status') == 'coming-soon' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status-coming">Coming Soon</label>
                                </div>
                            </div>

                            <!-- Genre Filter -->
                            <div class="filter-group mb-4">
                                <h6 class="filter-title">Genre</h6>
                                <select name="genre" class="form-select">
                                    <option value="">All Genres</option>
                                    @foreach($genres as $genre)
                                    <option value="{{ $genre }}" {{ request('genre') == $genre ? 'selected' : '' }}>
                                        {{ $genre }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Rating Filter -->
                            <div class="filter-group mb-4">
                                <h6 class="filter-title">Minimum Rating</h6>
                                <select name="min_rating" class="form-select">
                                    <option value="">Any Rating</option>
                                    <option value="9" {{ request('min_rating') == '9' ? 'selected' : '' }}>9+ ⭐</option>
                                    <option value="8" {{ request('min_rating') == '8' ? 'selected' : '' }}>8+ ⭐</option>
                                    <option value="7" {{ request('min_rating') == '7' ? 'selected' : '' }}>7+ ⭐</option>
                                    <option value="6" {{ request('min_rating') == '6' ? 'selected' : '' }}>6+ ⭐</option>
                                    <option value="5" {{ request('min_rating') == '5' ? 'selected' : '' }}>5+ ⭐</option>
                                </select>
                            </div>

                            <!-- Language Filter -->
                            <div class="filter-group mb-4">
                                <h6 class="filter-title">Language</h6>
                                <select name="language" class="form-select">
                                    <option value="">All Languages</option>
                                    @foreach($languages as $language)
                                    <option value="{{ $language }}" {{ request('language') == $language ? 'selected' : '' }}>
                                        {{ $language }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Age Rating Filter -->
                            <div class="filter-group mb-4">
                                <h6 class="filter-title">Age Rating</h6>
                                <select name="age_rating" class="form-select">
                                    <option value="">All Ages</option>
                                    @foreach($ageRatings as $ageRating)
                                    <option value="{{ $ageRating }}" {{ request('age_rating') == $ageRating ? 'selected' : '' }}>
                                        {{ $ageRating }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Search Filter -->
                            <div class="filter-group mb-4">
                                <h6 class="filter-title">Search</h6>
                                <input type="text" name="search" class="form-control"
                                       placeholder="Movie title, cast, director..."
                                       value="{{ request('search') }}">
                            </div>

                            <div class="filter-actions">
                                <button type="submit" class="btn btn-primary w-100 mb-2">
                                    <i class="fa fa-search me-1"></i>Apply Filters
                                </button>
                                <a href="{{ route('movies.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fa fa-refresh me-1"></i>Clear All
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Popular Genres -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-tags me-2"></i>Popular Genres</h5>
                    </div>
                    <div class="card-body">
                        <div class="genre-tags">
                            @foreach($genres->take(8) as $genre)
                            <a href="{{ route('movies.genre', $genre) }}"
                               class="genre-tag {{ request('genre') == $genre ? 'active' : '' }}">
                                {{ $genre }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Movies Grid -->
        <div class="col-lg-9">
            <!-- Sort & View Options -->
            <div class="movies-toolbar mb-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="results-info">
                            <span class="text-muted">
                                Showing {{ $movies->firstItem() ?? 0 }}-{{ $movies->lastItem() ?? 0 }}
                                of {{ $movies->total() }} movies
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="sort-options">
                            <select name="sort" class="form-select form-select-sm d-inline-block w-auto" id="sort-select">
                                <option value="release_date-desc" {{ request('sort') == 'release_date-desc' ? 'selected' : '' }}>
                                    Latest Release
                                </option>
                                <option value="rating-desc" {{ request('sort') == 'rating-desc' ? 'selected' : '' }}>
                                    Highest Rated
                                </option>
                                <option value="title-asc" {{ request('sort') == 'title-asc' ? 'selected' : '' }}>
                                    Title A-Z
                                </option>
                                <option value="duration-desc" {{ request('sort') == 'duration-desc' ? 'selected' : '' }}>
                                    Duration
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Movies Grid -->
            @if($movies->count() > 0)
            <div class="movies-grid">
                <div class="row">
                    @foreach($movies as $movie)
                    <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                        <div class="movie-card">
                            <div class="movie-poster position-relative">
                                <img src="{{$movie->display_image_url ?? ($movie->poster_url ? asset('storage/' . $movie->poster_url) : asset('assets/img/default/cinema.jpg'))}}"
                                     alt="{{ $movie->title }}" class="img-fluid">

                                <!-- Movie Status Badge -->
                                <div class="status-badge status-{{ strtolower(str_replace(' ', '-', $movie->status)) }}">
                                    {{ $movie->status }}
                                </div>

                                <!-- Rating Badge -->
                                @if($movie->rating)
                                <div class="rating-badge">
                                    <i class="fa fa-star"></i> {{ number_format($movie->rating, 1) }}
                                </div>
                                @endif

                                <!-- Age Rating -->
                                @if($movie->age_rating)
                                <div class="age-rating-badge">{{ $movie->age_rating }}</div>
                                @endif

                                <!-- Overlay -->
                                <div class="movie-overlay">
                                    <div class="movie-actions">
                                        <a href="{{ route('movies.show', $movie->movie_id) }}"
                                           class="btn btn-primary btn-sm">
                                            <i class="fa fa-eye"></i> View Details
                                        </a>
                                        @if($movie->status == 'Now Showing')
                                        <a href="{{ route('movies.showtimes', $movie->movie_id) }}"
                                           class="btn btn-success btn-sm mt-2">
                                            <i class="fa fa-ticket"></i> Book Now
                                        </a>
                                        @endif
                                    </div>
                                    <button class="btn btn-link add-to-favorites"
                                            data-movie-id="{{ $movie->movie_id }}"
                                            title="Add to Favorites">
                                        <i class="fa fa-heart"></i>
                                    </button>
                                </div>

                                <!-- Play Button for Trailers -->
                                @if($movie->trailer_url)
                                <div class="play-button">
                                    <a href="{{ $movie->trailer_url }}" class="trailer-link" target="_blank">
                                        <i class="fa fa-play"></i>
                                    </a>
                                </div>
                                @endif
                            </div>

                            <div class="movie-info">
                                <h5 class="movie-title">
                                    <a href="{{ route('movies.show', $movie->movie_id) }}">{{ $movie->title }}</a>
                                </h5>

                                @if($movie->original_title && $movie->original_title != $movie->title)
                                <p class="original-title">{{ $movie->original_title }}</p>
                                @endif

                                <div class="movie-meta">
                                    <span class="duration">
                                        <i class="fa fa-clock"></i>
                                        {{ floor($movie->duration / 60) }}h {{ $movie->duration % 60 }}m
                                    </span>
                                    <span class="release-year">
                                        <i class="fa fa-calendar"></i>
                                        {{ $movie->release_date ? \Carbon\Carbon::parse($movie->release_date)->format('Y') : 'TBA' }}
                                    </span>
                                </div>

                                <div class="movie-genre">
                                    @if($movie->genre)
                                        @foreach(explode(',', $movie->genre) as $genre)
                                        <span class="genre-tag">{{ trim($genre) }}</span>
                                        @endforeach
                                    @endif
                                </div>

                                @if($movie->description)
                                <p class="movie-description">{{ Str::limit($movie->description, 100) }}</p>
                                @endif

                                <div class="movie-footer">
                                    @if($movie->director)
                                    <small class="director">
                                        <i class="fa fa-user"></i> {{ $movie->director }}
                                    </small>
                                    @endif

                                    @if($movie->language)
                                    <small class="language">
                                        <i class="fa fa-globe"></i> {{ $movie->language }}
                                    </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Pagination -->
            <div class="movies-pagination mt-4">
                {{ $movies->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
            @else
            <!-- No Movies Found -->
            <div class="no-movies-found text-center py-5">
                <div class="no-results-icon mb-4">
                    <i class="fa fa-film fa-4x text-muted"></i>
                </div>
                <h3>No Movies Found</h3>
                <p class="text-muted">We couldn't find any movies matching your criteria.</p>
                <a href="{{ route('movies.index') }}" class="btn btn-primary">
                    <i class="fa fa-refresh me-2"></i>View All Movies
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.page-title {
    color: #333;
    font-weight: 700;
    margin-bottom: 10px;
}

.page-subtitle {
    color: #666;
    font-size: 1.1rem;
}

.movie-stats .stat-item {
    color: #666;
}

/* Filters Sidebar */
.filters-sidebar .card {
    border: 1px solid #e3e6f0;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.filter-group .filter-title {
    color: #5a5c69;
    font-weight: 600;
    margin-bottom: 10px;
}

.filter-actions .btn {
    border-radius: 6px;
}

.genre-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.genre-tag {
    display: inline-block;
    background: #f8f9fc;
    color: #5a5c69;
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 12px;
    text-decoration: none;
    transition: all 0.3s;
    border: 1px solid #e3e6f0;
}

.genre-tag:hover,
.genre-tag.active {
    background: #4e73df;
    color: white;
    text-decoration: none;
}

/* Movies Toolbar */
.movies-toolbar {
    background: #f8f9fc;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.sort-options select {
    min-width: 150px;
}

/* Movie Cards */
.movie-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    transition: all 0.3s ease;
    height: 100%;
}

.movie-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.25rem 2rem 0 rgba(58, 59, 69, 0.2);
}

.movie-poster {
    position: relative;
    height: 400px;
    overflow: hidden;
}

.movie-poster img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.movie-card:hover .movie-poster img {
    transform: scale(1.05);
}

/* Badges */
.status-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    z-index: 3;
}

.status-now-showing {
    background: #1cc88a;
    color: white;
}

.status-coming-soon {
    background: #36b9cc;
    color: white;
}

.status-ended {
    background: #e74a3b;
    color: white;
}

.rating-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(255, 193, 7, 0.95);
    color: #333;
    padding: 6px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    z-index: 3;
}

.age-rating-badge {
    position: absolute;
    bottom: 15px;
    right: 15px;
    background: rgba(220, 53, 69, 0.9);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    z-index: 3;
}

/* Movie Overlay */
.movie-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s;
    z-index: 2;
}

.movie-card:hover .movie-overlay {
    opacity: 1;
}

.movie-actions {
    text-align: center;
}

.add-to-favorites {
    position: absolute;
    bottom: 20px;
    right: 20px;
    color: white;
    font-size: 18px;
    padding: 10px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    transition: all 0.3s;
}

.add-to-favorites:hover {
    background: #e74a3b;
    color: white;
}

.add-to-favorites.favorited {
    color: #e74a3b;
    background: rgba(255, 255, 255, 0.9);
}

/* Play Button */
.play-button {
    position: absolute;
    bottom: 20px;
    left: 20px;
    z-index: 3;
}

.trailer-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 50%;
    color: #333;
    font-size: 18px;
    text-decoration: none;
    transition: all 0.3s;
}

.trailer-link:hover {
    background: #4e73df;
    color: white;
    transform: scale(1.1);
}

/* Movie Info */
.movie-info {
    padding: 20px;
}

.movie-title {
    margin-bottom: 8px;
    font-size: 1.2rem;
    font-weight: 600;
}

.movie-title a {
    color: #333;
    text-decoration: none;
    transition: color 0.3s;
}

.movie-title a:hover {
    color: #4e73df;
}

.original-title {
    font-size: 0.9rem;
    color: #666;
    font-style: italic;
    margin-bottom: 10px;
}

.movie-meta {
    display: flex;
    gap: 15px;
    margin-bottom: 12px;
    font-size: 0.85rem;
    color: #666;
}

.movie-genre {
    margin-bottom: 12px;
}

.movie-genre .genre-tag {
    font-size: 11px;
    margin-right: 6px;
    margin-bottom: 4px;
}

.movie-description {
    font-size: 0.9rem;
    color: #666;
    line-height: 1.5;
    margin-bottom: 15px;
}

.movie-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 15px;
    border-top: 1px solid #e3e6f0;
    font-size: 0.8rem;
    color: #666;
}

/* No Results */
.no-movies-found {
    background: white;
    border-radius: 12px;
    padding: 60px 40px;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.no-results-icon {
    opacity: 0.5;
}

/* Responsive */
@media (max-width: 768px) {
    .movie-poster {
        height: 300px;
    }

    .movies-toolbar {
        text-align: center;
    }

    .movies-toolbar .col-md-6 {
        margin-bottom: 15px;
    }

    .sort-options select {
        width: 100% !important;
    }

    .filter-actions .btn {
        margin-bottom: 10px;
    }
}

/* Loading States */
.movie-card.loading {
    background: #f8f9fc;
    animation: pulse 1.5s ease-in-out infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-submit form when filters change
    $('#filter-form select, #filter-form input[type="radio"]').on('change', function() {
        $('#filter-form').submit();
    });

    // Sort functionality
    $('#sort-select').on('change', function() {
        var sortValue = $(this).val().split('-');
        var sortBy = sortValue[0];
        var direction = sortValue[1];

        var currentUrl = new URL(window.location);
        currentUrl.searchParams.set('sort', sortBy);
        currentUrl.searchParams.set('direction', direction);

        window.location.href = currentUrl.toString();
    });

    // Add to favorites functionality
    $('.add-to-favorites').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var movieId = $(this).data('movie-id');
        var $button = $(this);

        @auth
        $.post('/account/favorites/toggle', {
            movie_id: movieId
        })
        .done(function(response) {
            if (response.success) {
                $button.toggleClass('favorited');
                var icon = $button.find('i');
                if ($button.hasClass('favorited')) {
                    icon.removeClass('fa-heart-o').addClass('fa-heart');
                } else {
                    icon.removeClass('fa-heart').addClass('fa-heart-o');
                }
            }
        })
        .fail(function(xhr) {
            alert('Unable to update favorites. Please try again.');
        });
        @else
        alert('Please login to add movies to favorites.');
        window.location.href = '{{ route("auth.login") }}';
        @endauth
    });

    // Search input debounce
    var searchTimeout;
    $('input[name="search"]').on('input', function() {
        clearTimeout(searchTimeout);
        var $form = $(this).closest('form');

        searchTimeout = setTimeout(function() {
            $form.submit();
        }, 500);
    });

    // Load more movies on scroll (infinite scroll - optional)
    var isLoading = false;
    var currentPage = {{ $movies->currentPage() }};
    var lastPage = {{ $movies->lastPage() }};

    $(window).scroll(function() {
        if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
            if (!isLoading && currentPage < lastPage) {
                loadMoreMovies();
            }
        }
    });

    function loadMoreMovies() {
        isLoading = true;
        currentPage++;

        var currentUrl = new URL(window.location);
        currentUrl.searchParams.set('page', currentPage);

        $.get(currentUrl.toString())
        .done(function(response) {
            var $newMovies = $(response).find('.movies-grid .row').children();
            $('.movies-grid .row').append($newMovies);

            // Update pagination if exists
            var $newPagination = $(response).find('.movies-pagination');
            if ($newPagination.length) {
                $('.movies-pagination').html($newPagination.html());
            }

            isLoading = false;
        })
        .fail(function() {
            currentPage--; // Revert page number on failure
            isLoading = false;
        });
    }
});
</script>
@endpush
