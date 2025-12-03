@extends('client.layouts.app')

@section('title', $cinema->name)

@section('content')
<div class="container py-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between gap-3">
                    <div>
                        <p class="text-uppercase text-muted small mb-1">{{ __('Rạp chiếu phim') }}</p>
                        <h1 class="h3 mb-2">{{ $cinema->name }}</h1>
                        <p class="text-muted mb-0">
                            <i class="bi bi-geo-alt me-1 text-danger"></i>{{ $cinema->address }} · {{ $cinema->city }}
                        </p>
                    </div>
                    <div class="text-md-end">
                        <p class="mb-1 fw-semibold">{{ __('Có :screens phòng chiếu trong hệ thống', ['screens' => $cinema->screens->count()]) }}</p>
                        <a href="{{ route('cinemas.showtimes', $cinema->cinema_id) }}" class="btn btn-brand">
                            {{ __('Xem toàn bộ lịch chiếu dạng JSON') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h2 class="h5 mb-3">{{ __('Phim đang chiếu tại rạp') }}</h2>
                    @if(!empty($groupedShowtimes))
                        @foreach($groupedShowtimes as $date => $movies)
                            <div class="border rounded-3 p-3 mb-3">
                                <h3 class="h6 text-uppercase text-muted mb-3">
                                    {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d/m/Y') }}
                                </h3>
                                @foreach($movies as $movieId => $movieGroup)
                                    @php $movie = $movieGroup['movie']; @endphp
                                    <div class="d-flex flex-column flex-lg-row gap-3 align-items-start align-items-lg-center mb-3">
                                        <div class="d-flex gap-3 align-items-start flex-grow-1">
                                            <img src="{{ $movie && $movie->poster_url ? asset('storage/' . ltrim($movie->poster_url, '/')) : asset('assets/img/default/cinema.jpg') }}"
                                                 alt="{{ $movie?->title }}"
                                                 class="rounded" style="width:80px;height:110px;object-fit:cover;">
                                            <div>
                                                <p class="mb-1 fw-semibold">{{ $movie?->title ?? __('Phim chưa xác định') }}</p>
                                                <p class="text-muted small mb-0">
                                                    {{ __('Thời lượng: :minutes phút', ['minutes' => $movie?->duration ?? __('Đang cập nhật')]) }}
                                                </p>
                                                <a href="{{ $movie ? route('movies.showtimes', $movie->movie_id) : '#' }}" class="btn btn-link p-0 small">
                                                    {{ __('Xem các suất chiếu của phim') }}
                                                </a>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 text-lg-end">
                                            @foreach($movieGroup['showtimes'] as $slot)
                                                <a href="{{ route('booking.seatSelection', $slot['id']) }}"
                                                   class="btn btn-outline-secondary btn-sm mb-1">
                                                    {{ $slot['time'] }} · {{ $slot['screen'] }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted mb-0">{{ __('Rạp chưa có suất chiếu khả dụng.') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h2 class="h6 text-uppercase text-muted mb-3">{{ __('Thông tin liên hệ') }}</h2>
                    <p class="mb-1"><i class="bi bi-telephone me-2 text-danger"></i>{{ $cinema->phone ?? __('Đang cập nhật') }}</p>
                    <p class="mb-1"><i class="bi bi-envelope me-2 text-danger"></i>{{ $cinema->email ?? __('Đang cập nhật') }}</p>
                    <p class="mb-0">
                        <i class="bi bi-globe2 me-2 text-danger"></i>
                        @if(!empty($cinema->website_url))
                            <a href="{{ $cinema->website_url }}" target="_blank" rel="noopener">{{ $cinema->website_url }}</a>
                        @else
                            {{ __('Đang cập nhật') }}
                        @endif
                    </p>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h6 text-uppercase text-muted mb-3">{{ __('Sơ đồ') }}</h2>
                    @if(!empty($cinema->latitude) && !empty($cinema->longitude))
                        <iframe src="https://www.google.com/maps?q={{ $cinema->latitude }},{{ $cinema->longitude }}&output=embed"
                                style="width:100%;height:220px;border:0;border-radius:12px"></iframe>
                    @else
                        <img src="{{ asset('assets/img/default/cinema.jpg') }}" alt="" class="img-fluid rounded-3">
                        <p class="text-muted small mt-2 mb-0">{{ __('Rạp chưa cập nhật vị trí bản đồ.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
