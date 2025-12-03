@props([
    'movie',
    'compact' => false,
])

@php
    $status = data_get($movie, 'badges.status');
    $rating = data_get($movie, 'badges.rating');
@endphp

<article class="movie-card h-100 d-flex flex-column overflow-hidden">
    <div class="movie-card__media position-relative">
        <img src="{{ $movie['poster_url'] ?? asset('assets/img/default/cinema.jpg') }}" class="img-fluid w-100" alt="{{ $movie['title'] }}" onerror="this.src='{{ asset('assets/img/default/cinema.jpg') }}'">

        @if($status)
            <span class="badge badge-status position-absolute top-0 end-0 m-3 text-uppercase small">
                {{ $status['label'] }}
            </span>
        @endif

        @if($rating)
            <span class="badge badge-status position-absolute bottom-0 start-0 m-3 d-inline-flex align-items-center gap-1">
                <i class="bi bi-star-fill"></i>
                <span>{{ $rating }}</span>
            </span>
        @endif
    </div>

    <div class="p-4 d-flex flex-column gap-3 flex-grow-1">
        <div>
            <h3 class="movie-card__title fs-5 fw-semibold mb-1">
                <a href="{{ $movie['details_url'] ?? route('movies.showtimes', $movie['id'] ?? $movie['movie_id'] ?? 0) }}" class="text-decoration-none text-black">
                    {{ $movie['title'] }}
                </a>
            </h3>
            @if(!empty($movie['release_date']))
                <p class="small mb-0">
                    <i class="bi bi-calendar3 me-1"></i>{{ $movie['release_date'] }}
                </p>
            @endif
        </div>

        @if(!$compact)
            <p class="movie-card__summary small flex-grow-1 mb-0">
                {{ $movie['summary'] }}
            </p>
        @endif

        <div class="d-flex flex-wrap gap-2">
            @foreach($movie['genres'] ?? [] as $genre)
                <span class="badge badge-status text-uppercase small">{{ $genre }}</span>
            @endforeach
        </div>


        <div class="d-flex flex-wrap gap-2 mt-auto">
            <a href="{{ $movie['details_url'] ?? $movie['book_url'] ?? route('movies.showtimes', $movie['id'] ?? $movie['movie_id'] ?? 0) }}" class="btn btn-sm btn-brand flex-grow-1">
                <i class="bi me-1"></i>{{ __('Đặt vé') }}
            </a>
        </div>
    </div>
</article>
