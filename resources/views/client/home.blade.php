@extends('client.layouts.app')

@section('title', 'MyShowz - Đặt vé phim trực tuyến')

@section('content')
    @include('client.components.trailer-modal')

    <section class="hero-section py-5">
        <div class="container">
            <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="6500">
                @if(!empty($heroSlides))
                    <div class="carousel-inner">
                        @foreach($heroSlides as $index => $slide)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <div class="hero-slide" style="background-image: url('{{ asset('BG.png') }}'); background-size: cover; background-position: center;">
                                    <div class="hero-overlay w-100">
                                        <div class="row align-items-center g-4">
                                            <div class="col-lg-7">
                                                <span class="badge badge-status mb-3">{{ __('Đề xuất cho bạn') }}</span>
                                                <h1 class="display-5 fw-bold mb-3">{{ $slide['title'] }}</h1>
                                                <p class="fw-medium mb-4">{{ $slide['summary'] }}</p>
                                                <div class="d-flex flex-wrap gap-3 align-items-center mb-4">
                                                    @if(!empty($slide['rating']))
                                                        <span class="badge badge-status"><i class="bi bi-star-fill me-1"></i>{{ $slide['rating'] }}</span>
                                                    @endif
                                                    @if(!empty($slide['duration']))
                                                        <span class="badge badge-status"><i class="bi bi-clock-history me-1"></i>{{ $slide['duration'] }}</span>
                                                    @endif
                                                    @foreach($slide['genres'] ?? [] as $genre)
                                                        <span class="badge badge-status">{{ $genre }}</span>
                                                    @endforeach
                                                </div>
                                                <div class="d-flex flex-wrap gap-3">
                                                    <a href="{{ $slide['cta']['url'] ?? $slide['details_url'] }}" class="btn btn-brand btn-lg">
                                                        <i class="bi bi-ticket-perforated me-2"></i>{{ $slide['cta']['label'] ?? __('Đặt vé ngay') }}
                                                    </a>
                                                    <button class="btn btn-outline-brand btn-lg js-open-trailer" data-movie-id="{{ $slide['id'] }}" data-trailer-url="{{ $slide['trailer_url'] }}" data-movie-title="{{ $slide['title'] }}">
                                                        <i class="bi bi-play-circle me-2"></i>{{ __('Xem trailer') }}
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-lg-5">
                                                <div class="movie-hero-card p-4 h-100 d-flex flex-column justify-content-between">
                                                    <div class="d-flex flex-column gap-2">
                                                        <span class="fw-semibold">{{ __('Thông tin nhanh') }}</span>
                                                        <ul class="list-unstyled mb-0">
                                                            <li><strong>{{ __('Thời lượng:') }}</strong> {{ $slide['duration'] ?? __('Đang cập nhật') }}</li>
                                                            <li><strong>{{ __('Ngày khởi chiếu:') }}</strong> {{ $slide['release_date'] ?? __('Đang cập nhật') }}</li>
                                                            <li><strong>{{ __('Trạng thái:') }}</strong> {{ $slide['status'] ?? __('Chưa xác định') }}</li>
                                                        </ul>
                                                    </div>
                                                    <a href="{{ $slide['book_url'] }}" class="btn btn-outline-brand mt-4">
                                                        {{ __('Đặt vé / Xem lịch chiếu') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">{{ __('Trước') }}</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">{{ __('Sau') }}</span>
                    </button>
                @else
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <div class="hero-slide" style="background-image:  url('{{ asset('images/BG.png') }}'); background-size: cover; background-position: center;">
                                <div class="hero-overlay w-100">
                                    <h1 class="display-5 fw-bold mb-3">{{ __('Chào mừng đến MyShowz') }}</h1>
                                    <p class="fw-medium mb-4">{{ __('Khám phá và đặt vé phim yêu thích của bạn với trải nghiệm nhanh chóng và trực quan.') }}</p>
                                    <a href="{{ route('movies.index') }}" class="btn btn-brand btn-lg">{{ __('Bắt đầu khám phá') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                @foreach($movieStats as $stat)
                    <div class="col-6 col-lg-3">
                        @include('client.components.stats-tile', ['stat' => $stat])
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
                <div>
                    <h2 class="section-title mb-1">{{ __('Phim đang chiếu') }}</h2>
                    <p class="fw-medium mb-0">{{ __('Danh sách phim nổi bật đang có lịch chiếu tại hệ thống MyShowz.') }}</p>
                </div>
                <a href="{{ route('movies.index', ['status' => 'now-showing']) }}" class="btn btn-outline-brand btn-sm">{{ __('Xem tất cả') }}</a>
            </div>
            @include('client.movies.partials.movie-card-grid', ['movies' => collect($nowShowingMovies ?? []), 'emptyMessage' => __('Hiện chưa có phim đang chiếu.')])
        </div>
    </section>

    <section class="py-5 section-accent">
        <div class="container">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
                <div>
                    <h2 class="section-title mb-1">{{ __('Lọc theo thể loại') }}</h2>
                    <p class="fw-medium mb-0">{{ __('Chọn thể loại yêu thích để xem phim phù hợp ngay lập tức.') }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($genreFilters as $genre)
                        <button class="btn btn-outline-brand btn-sm genre-filter" data-genre="{{ $genre }}">{{ $genre }}</button>
                    @endforeach
                </div>
            </div>
            <div id="genreSpotlightGrid">
                @include('client.movies.partials.movie-card-grid', [
                    'movies' => collect($nowShowingMovies ?? []),
                    'emptyMessage' => __('Chọn một thể loại để bắt đầu.'),
                ])
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
                <div>
                    <h2 class="section-title mb-1">{{ __('Phim sắp khởi chiếu') }}</h2>
                    <p class="fw-medium mb-0">{{ __('Đặt lịch cho những bộ phim đang được mong chờ nhất.') }}</p>
                </div>
                <a href="{{ route('movies.index', ['status' => 'coming-soon']) }}" class="btn btn-outline-brand btn-sm">{{ __('Đặt vé trước') }}</a>
            </div>
            @include('client.movies.partials.movie-card-grid', ['movies' => collect($comingSoonMovies ?? [])->take(8), 'emptyMessage' => __('Hiện chưa có phim sắp chiếu.')])
        </div>
    </section>

    @if(!empty($trendingMovies))
        <section class="py-5 section-accent">
            <div class="container">
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
                    <div>
                        <h2 class="section-title mb-1">{{ __('Xu hướng tuần này') }}</h2>
                        <p class="fw-medium mb-0">{{ __('Những bộ phim được khán giả MyShowz quan tâm nhiều nhất.') }}</p>
                    </div>
                    <a href="{{ route('movies.index', ['sort' => 'popularity-desc']) }}" class="btn btn-outline-brand btn-sm">{{ __('Khám phá thêm') }}</a>
                </div>
                @include('client.movies.partials.movie-card-grid', ['movies' => collect($trendingMovies), 'emptyMessage' => __('Hiện chưa có phim xu hướng.')])
            </div>
        </section>
    @endif

    @if(!empty($recommendations))
        <section class="py-5">
            <div class="container">
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
                    <div>
                        <h2 class="section-title mb-1">{{ __('Gợi ý cho riêng bạn') }}</h2>
                        <p class="fw-medium mb-0">{{ __('Dựa trên lịch sử đặt vé và thể loại yêu thích của bạn.') }}</p>
                    </div>
                </div>
                @include('client.movies.partials.movie-card-grid', ['movies' => collect($recommendations), 'emptyMessage' => __('Hãy đặt vé để nhận gợi ý cá nhân hóa.')])
            </div>
        </section>
    @endif

    <section class="py-5 section-accent">
        <div class="container">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
                <div>
                    <h2 class="section-title mb-1">{{ __('Đặt vé nhanh') }}</h2>
                    <p class="fw-medium mb-0">{{ __('Chọn suất chiếu phù hợp và giữ ghế ngay trong vài giây.') }}</p>
                </div>
                <a href="{{ route('movies.index') }}" class="btn btn-outline-brand btn-sm">{{ __('Xem thêm suất chiếu') }}</a>
            </div>
            <div class="row g-3">
                @foreach(array_chunk($quickShowtimes ?? [], 4) as $chunk)
                    <div class="col-12 col-lg-6">
                        <div class="list-group rounded-4 shadow-sm">
                            @foreach($chunk as $showtime)
                                <div class="list-group-item d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 py-3">
                                    <div>
                                        <div class="fw-semibold">{{ $showtime['movie_title'] }}</div>
                                        <div>{{ $showtime['date'] }} • {{ $showtime['start_time'] }}</div>
                                        <div>{{ $showtime['cinema']['name'] }} — {{ $showtime['cinema']['city'] }}</div>
                                        <div>{{ __('Còn :seats ghế', ['seats' => $showtime['available_seats']]) }}</div>
                                    </div>
                                    <div class="d-flex flex-column flex-md-row align-items-md-center gap-2">
                                        <a href="{{ route('movies.showtimes', $showtime['movie_id']) }}" class="btn btn-outline-brand btn-sm">{{ __('Chi tiết') }}</a>
                                        <a href="{{ $showtime['booking_url'] }}" class="btn btn-brand btn-sm">{{ __('Giữ ghế') }}</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
                <div>
                    <h2 class="section-title mb-1">{{ __('Trailer nổi bật') }}</h2>
                    <p class="fw-medium mb-0">{{ __('Xem trước những thước phim ấn tượng vừa cập nhật.') }}</p>
                </div>
            </div>
            <div class="row g-4">
                @foreach($latestTrailers as $entry)
                    @php($movie = $entry['movie'])
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="trailer-card card h-100 border-0 overflow-hidden">
                            <div class="position-relative">
                                <img src="{{ $movie['poster_url'] ?? asset('assets/img/default/cinema.jpg') }}" class="img-fluid w-100" alt="{{ $movie['title'] }}" onerror="this.src='{{ asset('assets/img/default/cinema.jpg') }}'">
                                <button class="btn btn-brand rounded-circle position-absolute top-50 start-50 translate-middle js-open-trailer"
                                    data-movie-id="{{ $movie['id'] }}"
                                    data-trailer-url="{{ $entry['trailer_url'] }}"
                                    data-movie-title="{{ $movie['title'] }}">
                                    <i class="bi bi-play-fill fs-4"></i>
                                </button>
                            </div>
                            <div class="card-body d-flex flex-column gap-2">
                                <h3 class="fs-6 fw-semibold mb-0">{{ $movie['title'] }}</h3>
                                <p class="mb-0">{{ $movie['summary'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-5 section-accent">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="section-title mb-1">{{ __('Khán giả nói gì?') }}</h2>
                <p class="fw-medium mb-0">{{ __('Những cảm nhận thực tế từ người dùng MyShowz.') }}</p>
            </div>
            @include('client.components.testimonial-slider', ['testimonials' => $testimonials])
        </div>
    </section>
@endsection

@push('scripts')
<script>
    (function($) {
        'use strict';

        const trailerModalEl = document.getElementById('trailerModal');
        const trailerFrame = document.getElementById('trailerModalFrame');
        const trailerModalTitle = document.getElementById('trailerModalLabel');
        const trailerModal = trailerModalEl ? new bootstrap.Modal(trailerModalEl) : null;

        function formatNumber(num) {
            return new Intl.NumberFormat('vi-VN').format(num);
        }

        function animateCounters() {
            $('.stats-tile').each(function() {
                const $tile = $(this);
                const target = parseInt($tile.data('count'), 10) || 0;
                const suffix = $tile.data('suffix') || '';
                const $value = $tile.find('.stats-tile__value');

                $({countNum: 0}).animate({countNum: target}, {
                    duration: 1600,
                    easing: 'swing',
                    step: function(now) {
                        $value.text(formatNumber(Math.floor(now)) + suffix);
                    },
                    complete: function() {
                        $value.text(formatNumber(target) + suffix);
                    }
                });
            });
        }

        function bindTrailerButtons(scope) {
            scope.find('.js-open-trailer').off('click').on('click', function() {
                if (!trailerModal) {
                    window.location.href = $(this).data('trailer-url');
                    return;
                }

                const trailerUrl = $(this).data('trailer-url');
                const title = $(this).data('movie-title') || '{{ __('Trailer phim') }}';

                trailerFrame.src = trailerUrl;
                trailerModalTitle.textContent = title;
                trailerModal.show();
            });
        }

        function initGenreFilters() {
            const $grid = $('#genreSpotlightGrid');
            const $buttons = $('.genre-filter');

            $buttons.on('click', function() {
                const $btn = $(this);
                $buttons.removeClass('active');
                $btn.addClass('active');

                const genre = $btn.data('genre');
                $grid.addClass('is-loading');

                $.get('{{ route('movies.by-genre') }}', { genre }, function(response) {
                    if (response.success) {
                        $grid.html(response.html);
                        bindTrailerButtons($grid);
                    }
                }).fail(function() {
                    $grid.html('<div class="alert alert-danger">{{ __('Không thể tải dữ liệu. Vui lòng thử lại sau.') }}</div>');
                }).always(function() {
                    $grid.removeClass('is-loading');
                });
            });
        }

        function initNewsletterForm() {
            const $form = $('#newsletter-form');
            const $feedback = $('#newsletter-feedback');

            $form.on('submit', function(event) {
                event.preventDefault();

                $.post($form.attr('action'), $form.serialize())
                    .done(function(response) {
                        if (response.success) {
                            $feedback.text(response.message).removeClass('text-danger').addClass('text-success');
                            $form.trigger('reset');
                        } else {
                            $feedback.text(response.message || '{{ __('Không thể xử lý yêu cầu.') }}').removeClass('text-success').addClass('text-danger');
                        }
                    })
                    .fail(function(xhr) {
                        const message = xhr.responseJSON?.message || '{{ __('Đăng ký không thành công. Vui lòng thử lại.') }}';
                        $feedback.text(message).removeClass('text-success').addClass('text-danger');
                    });
            });
        }

        function handleTrailerModalCleanup() {
            if (!trailerModalEl) return;
            trailerModalEl.addEventListener('hidden.bs.modal', function() {
                trailerFrame.src = '';
            });
        }

        $(function() {
            animateCounters();
            bindTrailerButtons($(document));
            initGenreFilters();
            initNewsletterForm();
            handleTrailerModalCleanup();
        });
    })(jQuery);
</script>
@endpush
