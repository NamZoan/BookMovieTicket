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
        <img src="{{ $movie['poster_url'] }}" class="img-fluid w-100" alt="{{ $movie['title'] }}">

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
                <a href="{{ $movie['details_url'] }}" class="stretched-link text-decoration-none text-black">
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
            <a href="{{ $movie['book_url'] }}" class="btn btn-sm btn-brand flex-grow-1">
                <i class="bi bi-ticket-perforated me-1"></i>{{ __('Đặt vé') }}
            </a>
            <button type="button"
                class="btn btn-sm btn-outline-brand js-open-trailer"
                data-movie-id="{{ $movie['id'] }}"
                data-trailer-url="{{ $movie['trailer_url'] }}"
                data-movie-title="{{ $movie['title'] }}">
                <i class="bi bi-play-circle me-1"></i>{{ __('Trailer') }}
            </button>
        </div>
    </div>
</article>
