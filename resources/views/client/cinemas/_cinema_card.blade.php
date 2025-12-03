@php
    use Carbon\Carbon;

    $upcomingMovies = collect($cinema->screens ?? [])
        ->flatMap(function ($screen) {
            return collect($screen->showtimes ?? [])->map(function ($showtime) use ($screen) {
                return [
                    'movie' => $showtime->movie,
                    'showtime' => $showtime,
                    'screen' => $screen->screen_name,
                ];
            });
        })
        ->filter(function ($item) {
            if (!$item['movie'] || !$item['showtime']) {
                return false;
            }

            return $item['showtime']->status === 'Active'
                && $item['showtime']->show_date->gte(Carbon::today());
        })
        ->sortBy(function ($item) {
            return $item['showtime']->show_date->format('Y-m-d') . $item['showtime']->show_time->format('H:i');
        })
        ->groupBy(fn ($item) => $item['showtime']->movie_id);

    $totalShowtimes = $upcomingMovies->flatten(1)->count();
@endphp

@once
    @push('styles')
        <style>
            .cinema-card {
                border: 1px solid #f2f2f2;
                border-radius: 18px;
                box-shadow: 0 14px 28px rgba(0,0,0,0.06);
                overflow: hidden;
            }
            .cinema-card__header {
                background: linear-gradient(120deg, #e51c23, #b5121c);
                color: #fff;
                padding: 18px 20px;
            }
            .cinema-meta {
                color: #f8d7da;
                font-size: 0.95rem;
            }
            .showtime-chip {
                border: 1px solid #e51c23;
                color: #b5121c;
                padding: 6px 10px;
                border-radius: 999px;
                font-weight: 600;
                background: #ffe5e7;
                display: inline-flex;
                gap: 6px;
                align-items: center;
            }
            .movie-pill {
                border: 1px solid #f2f2f2;
                border-radius: 12px;
                padding: 10px 12px;
                background: #fff7f7;
            }
            .btn-brand {
                background: linear-gradient(90deg, #e51c23, #b5121c);
                color: #fff;
                border: none;
            }
            .btn-ghost {
                border: 1px solid #e51c23;
                color: #b5121c;
                background: #fff;
            }
            .btn-ghost:hover { background: #ffe5e7; color: #b5121c; }
        </style>
    @endpush
@endonce

<div class="cinema-card bg-white">
    <div class="cinema-card__header d-flex flex-column flex-md-row align-items-start align-items-md-center gap-2">
        <div class="flex-grow-1">
            <p class="text-uppercase small mb-1">{{ $cinema->city }}</p>
            <h3 class="h5 mb-1 fw-bold">{{ $cinema->name }}</h3>
            <p class="cinema-meta mb-0">
                <i class="bi bi-geo-alt me-1"></i>{{ $cinema->address }}
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <span class="badge bg-light text-dark">{{ __(':count phòng', ['count' => $cinema->screens->count()]) }}</span>
            <span class="badge bg-light text-dark">{{ __(':count suất', ['count' => $totalShowtimes]) }}</span>
        </div>
    </div>

    <div class="p-3 p-md-4 d-flex flex-column gap-3">
        @if($upcomingMovies->isNotEmpty())
            @foreach($upcomingMovies->take(3) as $movieId => $items)
                @php
                    $movie = $items->first()['movie'];
                    $showtimes = $items->take(4);
                @endphp
                <div class="movie-pill d-flex align-items-start gap-3 flex-wrap">
                    <div class="flex-shrink-0">
                        <img src="{{ $movie?->poster_url ? asset('storage/' . $movie->poster_url) : asset('assets/img/default/cinema.jpg') }}"
                             alt="{{ $movie?->title }}"
                             style="width: 70px; height: 100px; object-fit: cover; border-radius: 10px;">
                    </div>
                    <div class="flex-grow-1">
                        <p class="mb-1 fw-semibold">{{ $movie?->title }}</p>
                        <p class="text-muted small mb-1">
                            {{ __('Thời lượng: :duration phút', ['duration' => $movie?->duration ?? __('Đang cập nhật')]) }}
                            @if(!empty($movie?->rating))
                                • <i class="bi bi-star-fill text-warning"></i> {{ number_format($movie->rating, 1) }}/10
                            @endif
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($showtimes as $slot)
                                <a href="{{ route('booking.seatSelection', $slot['showtime']->showtime_id) }}" class="showtime-chip text-decoration-none">
                                    <span>{{ $slot['showtime']->show_date->format('d/m') }}</span>
                                    <span>{{ $slot['showtime']->show_time->format('H:i') }}</span>
                                    <span>{{ $slot['screen'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <p class="text-muted mb-0">{{ __('Hiện chưa có suất chiếu sẵn sàng.') }}</p>
        @endif

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('cinemas.show', $cinema->cinema_id ?? $cinema->id) }}" class="btn btn-ghost btn-sm">
                {{ __('Chi tiết rạp') }}
            </a>
            <a href="{{ route('movies.showtimes', $cinema->cinema_id ?? $cinema->id) }}" class="btn btn-brand btn-sm">
                {{ __('Xem toàn bộ lịch chiếu') }}
            </a>
        </div>
    </div>
</div>
