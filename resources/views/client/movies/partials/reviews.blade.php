@props(['movie'])

@php
    // Compatibility: older callers passed $reviews as paginator; we now expect $othersReviews and $userReview
    if (isset($othersReviews)) {
        // already provided
    } elseif (isset($reviews)) {
        $othersReviews = $reviews;
    } else {
        $othersReviews = collect();
    }

    if (isset($userReview)) {
        // already provided
    } elseif (isset($reviews)) {
        $userReview = $reviews->firstWhere('user_id', auth()->id() ?? 0) ?? null;
    } else {
        $userReview = null;
    }

    $ratingCounts = $ratingCounts ?? [];
    $approvedTotal = $approvedTotal ?? (is_countable($othersReviews) ? count($othersReviews) : ($othersReviews->count() ?? 0));
    $avg = $averageRating ?? 0;
@endphp

<div class="reviews-section mb-4">
    <h3 class="section-title">Đánh giá từ khách hàng</h3>
    
    <div class="review-summary mb-4">
        <div class="row align-items-center">
            <div class="col-md-4 text-center">
                <div class="average-rating">
                    <span class="rating-number">{{ number_format($averageRating, 1) }}</span>
                    <div class="stars">
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= ($averageRating/2) ? 'text-warning' : 'text-muted' }}"></i>
                        @endfor
                    </div>
                    <small class="text-muted">{{ $approvedTotal }} đánh giá</small>
                </div>
            </div>
            <div class="col-md-8">
                <div class="rating-bars">
                    @for ($i = 5; $i >= 1; $i--)
                        @php
                            $ratingKey = $i * 2; // ratings stored 1-10
                            $count = isset($ratingCounts[$ratingKey]) ? (int) $ratingCounts[$ratingKey] : 0;
                            $percentage = $approvedTotal > 0 ? ($count / $approvedTotal) * 100 : 0;
                        @endphp
                        <div class="rating-bar mb-2">
                            <div class="d-flex align-items-center">
                                <span class="stars me-2">{{ $i }}</span>
                                <div class="progress flex-grow-1" style="height: 8px;">
                                    <div class="progress-bar bg-warning" style="width: {{ $percentage }}%"></div>
                                </div>
                                <span class="count ms-2">{{ $count }}</span>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    @auth
        @can('create', [App\Models\Review::class, $movie])
            <div class="review-form mb-4">
                <form id="reviewForm" action="{{ route('movies.reviews.store', $movie) }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label>Đánh giá của bạn</label>
                        <div class="rating-input">
                            @for ($i = 10; $i >= 1; $i--)
                                <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" required>
                                <label for="star{{ $i }}"><i class="far fa-star"></i></label>
                            @endfor
                        </div>
                        @error('rating')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="comment">Nhận xét của bạn</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3" required maxlength="1000"></textarea>
                        @error('comment')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                </form>
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                @if(!Auth::check())
                    Vui lòng <a href="{{ route('auth.login') }}">đăng nhập</a> để gửi đánh giá.
                @else
                    @if($userReview)
                        Bạn đã đánh giá phim này.
                    @else
                        Bạn cần phải đặt vé xem phim này để có thể gửi đánh giá.
                    @endif
                @endif
            </div>
        @endcan
    @endauth

    <div id="reviewsList" class="reviews-list">
        @foreach($othersReviews as $review)
            <div class="review-item card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="user-info">
                            <h5 class="card-title mb-1">{{ $review->user->name }}</h5>
                            <div class="rating text-warning mb-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= ($review->rating/2) ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                            </div>
                        </div>
                        <div class="review-meta text-muted">
                            <small>{{ $review->created_at->diffForHumans() }}</small>
                            @if(!$review->is_approved)
                                <span class="badge bg-warning ms-2">Đang chờ duyệt</span>
                            @endif
                        </div>
                    </div>

                    <p class="card-text">{{ $review->comment }}</p>

                    @if(Auth::id() === $review->user_id)
                        <div class="review-actions mt-3">
                            <button class="btn btn-sm btn-outline-primary edit-review-btn" 
                                    data-review-id="{{ $review->review_id }}"
                                    data-rating="{{ $review->rating }}"
                                    data-comment="{{ $review->comment }}">
                                <i class="fas fa-edit"></i> Sửa
                            </button>
                            <form action="{{ route('movies.reviews.delete', [$movie, $review]) }}" 
                                  method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                        onclick="return confirm('Bạn có chắc muốn xóa đánh giá này?')">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        {{ method_exists($othersReviews, 'links') ? $othersReviews->links() : '' }}
    </div>
</div>

@push('styles')
<style>
    .rating-input {
        display: inline-flex;
        flex-direction: row-reverse;
        margin: 0;
        padding: 0;
    }

    .rating-input input {
        display: none;
    }

    .rating-input label {
        cursor: pointer;
        width: 25px;
        height: 25px;
        margin: 0;
        padding: 0;
        font-size: 25px;
        line-height: 25px;
    }

    .rating-input label:before {
        content: '\f005';
        font-family: 'Font Awesome 5 Free';
        font-weight: 400;
        color: #ddd;
    }

    .rating-input input:checked ~ label:before {
        color: #ffc107;
        font-weight: 900;
    }

    .rating-input label:hover:before,
    .rating-input label:hover ~ label:before {
        color: #ffc107;
        font-weight: 900;
    }

    .average-rating {
        padding: 20px;
        border-right: 1px solid #dee2e6;
    }

    .rating-number {
        font-size: 48px;
        font-weight: bold;
        color: #ffc107;
    }

    .rating-bars .progress {
        background-color: #f8f9fa;
    }

    .review-item {
        transition: all 0.3s ease;
    }

    .review-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .review-actions {
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .review-item:hover .review-actions {
        opacity: 1;
    }
</style>
@endpush

@push('scripts')
<script>
$(function() {
    // Initialize review form handling
    const $reviewForm = $('#reviewForm');
    const $reviewsList = $('#reviewsList');

    // Handle form submission via AJAX
    $reviewForm.on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Add the new review to the list
                    const newReview = createReviewElement(response.review);
                    $reviewsList.prepend(newReview);
                    
                    // Reset form
                    $reviewForm[0].reset();
                    
                    // Show success message
                    showAlert('success', response.message);
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON.errors;
                let errorMessage = 'Có lỗi xảy ra. Vui lòng thử lại.';
                if (errors) {
                    errorMessage = Object.values(errors)[0][0];
                }
                showAlert('danger', errorMessage);
            }
        });
    });

    // Handle review editing
    $(document).on('click', '.edit-review-btn', function() {
        const reviewId = $(this).data('review-id');
        const rating = $(this).data('rating');
        const comment = $(this).data('comment');

        // Populate form with existing data
        $(`#star${rating}`).prop('checked', true);
        $('#comment').val(comment);

        // Update form action and method
        $reviewForm.attr('action', '{{ route("movies.reviews.update", [$movie, "__id__"]) }}'.replace('__id__', reviewId));
        $reviewForm.append('<input type="hidden" name="_method" value="PUT">');

        // Scroll to form
        $('html, body').animate({
            scrollTop: $reviewForm.offset().top - 100
        }, 500);
    });

    function createReviewElement(review) {
        return `
            <div class="review-item card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="user-info">
                            <h5 class="card-title mb-1">${review.user.name}</h5>
                            <div class="rating text-warning mb-2">
                                ${generateStars(review.rating)}
                            </div>
                        </div>
                        <div class="review-meta text-muted">
                            <small>Vừa xong</small>
                            ${!review.is_approved ? '<span class="badge bg-warning ms-2">Đang chờ duyệt</span>' : ''}
                        </div>
                    </div>
                    <p class="card-text">${review.comment}</p>
                    <div class="review-actions mt-3">
                        <button class="btn btn-sm btn-outline-primary edit-review-btn"
                                data-review-id="${review.review_id}"
                                data-rating="${review.rating}"
                                data-comment="${review.comment}">
                            <i class="fas fa-edit"></i> Sửa
                        </button>
                        <form action="/movies/${review.movie_id}/reviews/${review.review_id}" 
                              method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Bạn có chắc muốn xóa đánh giá này?')">
                                <i class="fas fa-trash"></i> Xóa
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        `;
    }

    function generateStars(rating) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            stars += `<i class="fas fa-star ${i <= (rating/2) ? 'text-warning' : 'text-muted'}"></i>`;
        }
        return stars;
    }

    function showAlert(type, message) {
        const alert = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        $reviewForm.before(alert);
    }
});
</script>
@endpush