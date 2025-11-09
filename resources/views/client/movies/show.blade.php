@extends('client.layouts.app')

@section('title', $movie->title)

@push('styles')
<style>
.nav-tabs .nav-link.active {
    color: #C62828;
    border-color: #C62828;
    border-bottom-width: 2px;
}
.rating-stars input:checked ~ label {
    color: #FFC107;
}
.star-rating > span {
    color: #FFC107;
}
.review-card {
    transition: all 0.3s ease;
}
.review-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
</style>
@endpush

@section('content')
<div class="container my-4">
    <div class="row">
        <div class="col-lg-8">
            <!-- Movie Info -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h2 mb-2">{{ $movie->title }}</h1>
                    <div class="d-flex align-items-center mb-3">
                        <div class="star-rating me-2">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="{{ $i <= round($movie->rating ?? 0) ? 'text-warning' : 'text-muted' }}">★</span>
                            @endfor
                        </div>
                        <span class="text-muted">({{ number_format($movie->rating ?? 0, 1) }}/5)</span>
                    </div>

                    <div class="mb-3">
                        <span class="badge bg-primary me-2">{{ $movie->duration }} phút</span>
                        <span class="badge bg-secondary me-2">{{ $movie->rating_label }}</span>
                        @foreach(explode(',', $movie->genres) as $genre)
                            <span class="badge bg-light text-dark me-1">{{ trim($genre) }}</span>
                        @endforeach
                    </div>

                    <p class="text-muted">{{ $movie->description }}</p>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs mb-4" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="showtimes-tab" href="#showtimes" role="tab" aria-controls="showtimes" aria-selected="true">
                        Lịch chiếu
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="reviews-tab" href="#reviews" role="tab" aria-controls="reviews" aria-selected="false">
                        Đánh giá <span class="total-reviews badge rounded-pill bg-secondary ms-1">0</span>
                    </a>
                </li>
            </ul>

            <!-- Tabs Content -->
            <div class="tab-content">
                <!-- Showtimes Tab -->
                <div class="tab-pane fade show active" id="showtimes" role="tabpanel" aria-labelledby="showtimes-tab">
                    @include('client.movies.showtime-list', ['showtimes' => $showtimes])
                </div>

                <!-- Reviews Tab -->
                <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                    <div id="reviews-content">
                        <div class="text-center py-4">
                            <div class="spinner-border text-danger" role="status">
                                <span class="visually-hidden">Đang tải đánh giá...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Movie Poster -->
            <div class="card shadow-sm mb-4">
                <img src="{{ asset('storage/'.$movie->poster_url) }}" class="card-img-top" alt="{{ $movie->title }}">
                @if($movie->trailer_url)
                    <div class="card-body text-center">
                        <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#trailerModal">
                            <i class="fas fa-play me-2"></i>Xem trailer
                        </button>
                    </div>
                @endif
            </div>

            <!-- Movie Details -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Thông tin chi tiết</h5>
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Đạo diễn</dt>
                        <dd class="col-sm-8">{{ $movie->director }}</dd>

                        <dt class="col-sm-4">Diễn viên</dt>
                        <dd class="col-sm-8">{{ $movie->cast }}</dd>

                        <dt class="col-sm-4">Khởi chiếu</dt>
                        <dd class="col-sm-8">{{ \Carbon\Carbon::parse($movie->release_date)->format('d/m/Y') }}</dd>

                        <dt class="col-sm-4">Ngôn ngữ</dt>
                        <dd class="col-sm-8">{{ $movie->language }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Trailer Modal -->
@if($movie->trailer_url)
<div class="modal fade" id="trailerModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Trailer: {{ $movie->title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="ratio ratio-16x9">
                    <iframe src="{{ $movie->trailer_url }}" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
// Main app functions
window.MovieApp = {
    reviewsLoaded: false,

    init: function() {
        this.bindEvents();
        this.initTrailer();
    },

    bindEvents: function() {
        // Handle tab switching
        $('#showtimes-tab, #reviews-tab').on('click', function(e) {
            e.preventDefault();
            MovieApp.switchToTab($(this).attr('href').substring(1));
        });

        // Handle review form submission
        $(document).on('submit', '#review-form', function(e) {
            e.preventDefault();
            MovieApp.submitReview($(this));
        });

        // Handle review deletion
        $(document).on('click', '.delete-review', function(e) {
            e.preventDefault();
            if (confirm('Bạn có chắc chắn muốn xoá đánh giá này?')) {
                MovieApp.deleteReview($(this).data('id'));
            }
        });
    },

    switchToTab: function(tabId) {
        // Hide all tab panes
        $('.tab-pane').removeClass('show active');
        // Show selected tab pane
        $('#' + tabId).addClass('show active');
        // Update tab state
        $('.nav-link').removeClass('active');
        $(`[href="#${tabId}"]`).addClass('active');

        // Load reviews if needed
        if (tabId === 'reviews' && !this.reviewsLoaded) {
            this.loadReviews();
        }
    },

    loadReviews: function() {
        const loadingHtml = `
            <div class="text-center py-4">
                <div class="spinner-border text-danger" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>`;

        $('#reviews-content').html(loadingHtml);

        $.ajax({
            url: '{{ route('movies.reviews', $movie) }}',
            method: 'GET',
            success: (response) => {
                if (response.success && response.html) {
                    $('#reviews-content').html(response.html);
                    if (response.stats) {
                        this.updateRatingStats(response.stats);
                    }
                } else {
                    $('#reviews-content').html('<div class="alert alert-danger">Không thể tải đánh giá.</div>');
                }
                this.reviewsLoaded = true;
            },
            error: () => {
                $('#reviews-content').html('<div class="alert alert-danger">Có lỗi xảy ra khi tải đánh giá.</div>');
            }
        });
    },

    submitReview: function($form) {
        const submitBtn = $form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang gửi...');

        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            success: (response) => {
                if (response.success) {
                    this.loadReviews(); // Reload reviews to show the new one
                    $form.trigger('reset');
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: (xhr) => {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(key => {
                        toastr.error(errors[key][0]);
                    });
                } else {
                    toastr.error('Có lỗi xảy ra, vui lòng thử lại sau.');
                }
            },
            complete: () => {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    },

    deleteReview: function(reviewId) {
        $.ajax({
            url: `{{ url('reviews') }}/${reviewId}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => {
                if (response.success) {
                    this.loadReviews(); // Reload reviews
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: () => {
                toastr.error('Có lỗi xảy ra khi xoá đánh giá.');
            }
        });
    },

    updateRatingStats: function(stats) {
        if (stats.total > 0) {
            $('.movie-rating').text(stats.average.toFixed(1));
            $('.total-reviews').text(stats.total);
            // Update stars
            $('.star-rating span').each(function(index) {
                $(this).toggleClass('text-warning', index < Math.round(stats.average))
                      .toggleClass('text-muted', index >= Math.round(stats.average));
            });
        }
    },

    initTrailer: function() {
        // Reset iframe src when modal is closed to stop the video
        $('#trailerModal').on('hidden.bs.modal', function() {
            $(this).find('iframe').attr('src', function(i, val) { return val; });
        });
    }
};

// Initialize when document is ready
$(document).ready(function() {
    MovieApp.init();
});
</script>
@endpush

@endsection
