{{-- Reviews UI --}}
@auth
    @if ($canReview && !$userHasReviewed)
        <div class="review-card review-card--highlight">
            <div class="review-card__header">
                <div>
                    <p class="review-label">Viết đánh giá</p>
                    <h3 class="review-title">Chia sẻ cảm nhận của bạn</h3>
                    <p class="review-subtitle">Chỉ khán giả đã đặt vé mới có thể gửi đánh giá.</p>
                </div>
                <div class="review-badge">Khán giả đã xem</div>
            </div>

            <form id="review-form" action="{{ route('movies.reviews.store', $movie) }}" method="POST" class="review-form">
                @csrf

                <div class="form-group">
                    <label class="form-label">Bạn chấm phim này bao nhiêu sao?</label>
                    <div class="star-input" aria-label="Chọn số sao">
                        @for ($i = 5; $i >= 1; $i--)
                            <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" required />
                            <label for="star{{ $i }}" title="{{ $i }} sao">★</label>
                        @endfor
                    </div>
                </div>

                <div class="form-group">
                    <label for="comment" class="form-label">Cảm nhận của bạn</label>
                    <textarea name="comment" id="comment" rows="4" class="input-textarea" placeholder="Viết ngắn gọn về diễn xuất, nội dung, nhịp phim..."></textarea>
                </div>

                <button type="submit" class="btn-primary">
                    Gửi đánh giá
                </button>
            </form>
        </div>
    @elseif($userHasReviewed)
        <div class="review-card review-card--success">
            <div class="review-success-icon">✓</div>
            <div>
                <p class="review-label">Đã gửi</p>
                <h3 class="review-title">Cảm ơn bạn đã đánh giá phim này!</h3>
                <p class="review-subtitle">Đánh giá của bạn giúp người khác chọn phim dễ dàng hơn.</p>
            </div>
        </div>
    @else
        <div class="review-card review-card--info">
            <div class="review-info-icon">🎟️</div>
            <div>
                <p class="review-label">Cần đặt vé</p>
                <h3 class="review-title">Đặt vé xem phim để mở khóa đánh giá</h3>
                <p class="review-subtitle">Hãy trải nghiệm phim, sau đó quay lại để chia sẻ nhận xét của bạn.</p>
            </div>
        </div>
    @endif
@else
    <div class="review-card review-card--warning">
        <div class="review-warning-icon">🔒</div>
        <div>
            <p class="review-label">Đăng nhập để đánh giá</p>
            <h3 class="review-title">Hãy đăng nhập để viết đánh giá</h3>
            <a href="{{ route('auth.login') }}?redirect={{ url()->current() }}" class="btn-link">Đăng nhập ngay</a>
        </div>
    </div>
@endauth

{{-- Rating summary --}}
<div class="summary-grid">
    <div class="summary-card">
        <p class="review-label">Điểm trung bình</p>
        <div class="summary-score">
            <span class="summary-score__number">{{ number_format($averageRating, 1) }}</span>
            <span class="summary-score__outof">/ 5</span>
        </div>
        <p class="summary-meta">{{ $totalReviews }} đánh giá</p>
        <div class="summary-stars">
            @for ($i = 1; $i <= 5; $i++)
                <span class="summary-star {{ $i <= round($averageRating) ? 'summary-star--filled' : '' }}">★</span>
            @endfor
        </div>
    </div>

    <div class="distribution-card">
        <p class="review-label">Phân bố sao</p>
        <div class="distribution-list">
            @for ($i = 5; $i >= 1; $i--)
                <div class="distribution-row">
                    <span class="distribution-row__label">{{ $i }} sao</span>
                    <div class="distribution-row__bar">
                        <span class="distribution-row__fill" style="width: {{ $ratingDistribution[$i]['percentage'] }}%"></span>
                    </div>
                    <span class="distribution-row__count">{{ $ratingDistribution[$i]['count'] }}</span>
                </div>
            @endfor
        </div>
    </div>
</div>

{{-- Reviews list --}}
<div class="reviews-list">
    <div class="reviews-list__header">
        <h3 class="review-title">Tất cả đánh giá ({{ $reviews->total() }})</h3>
    </div>

    @if($reviews->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">☆</div>
            <p class="review-title">Chưa có đánh giá nào</p>
            <p class="review-subtitle">Hãy là người đầu tiên chia sẻ cảm nhận về bộ phim này.</p>
        </div>
    @else
        <div class="reviews-stack">
            @foreach($reviews as $review)
                <div class="review-item">
                    <div class="review-item__avatar">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($review->user->full_name) }}&color=F97316&background=FFF4E5" alt="{{ $review->user->full_name }}">
                    </div>
                    <div class="review-item__body">
                        <div class="review-item__meta">
                            <div>
                                <p class="review-item__name">{{ $review->user->full_name }}</p>
                                <p class="review-item__time">{{ $review->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="review-item__stars">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span class="summary-star {{ $i <= $review->rating ? 'summary-star--filled' : '' }}">★</span>
                                @endfor
                            </div>
                        </div>
                        @if($review->comment)
                            <p class="review-item__comment">
                                {{ $review->comment }}
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        @if ($reviews->hasPages())
            <div class="reviews-pagination">
                {{ $reviews->links() }}
            </div>
        @endif
    @endif
</div>

<style>
.review-card {
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    padding: 20px;
    display: flex;
    gap: 12px;
    align-items: center;
    background: #fff;
}
.review-card--highlight {
    background: linear-gradient(135deg, #fff7f7, #fff);
    border-color: #fef2f2;
    box-shadow: 0 10px 30px rgba(229, 28, 35, 0.08);
    flex-direction: column;
    align-items: stretch;
}
.review-card__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
.review-card--success {
    background: #f0fdf4;
    border-color: #bbf7d0;
}
.review-card--info {
    background: #eff6ff;
    border-color: #bfdbfe;
}
.review-card--warning {
    background: #fff7ed;
    border-color: #fed7aa;
}
.review-badge, .review-info-icon, .review-warning-icon, .review-success-icon {
    padding: 8px 12px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.9rem;
    color: #b91c1c;
    background: #fee2e2;
}
.review-success-icon { background:#bbf7d0; color:#166534; }
.review-info-icon { background:#dbeafe; color:#1d4ed8; }
.review-warning-icon { background:#fed7aa; color:#c2410c; }

.review-label {
    text-transform: uppercase;
    letter-spacing: 0.03em;
    font-size: 0.75rem;
    font-weight: 700;
    color: #9ca3af;
    margin: 0 0 4px;
}
.review-title {
    font-size: 1.15rem;
    font-weight: 800;
    color: #111827;
    margin: 0;
}
.review-subtitle {
    color: #6b7280;
    margin: 4px 0 0;
}

.review-form {
    margin-top: 16px;
    display: grid;
    gap: 16px;
}
.form-group { display: grid; gap: 8px; }
.form-label { font-weight: 700; color: #111827; }
.star-input {
    display: inline-flex;
    gap: 6px;
    font-size: 28px;
    color: #e5e7eb;
    direction: rtl;
}
.star-input input {
    display: none;
}
.star-input label {
    cursor: pointer;
    transition: transform 0.15s ease, color 0.2s ease;
}
.star-input input:checked ~ label,
.star-input label:hover,
.star-input label:hover ~ label {
    color: #f59e0b;
}
.star-input label:active { transform: scale(0.95); }

.input-textarea {
    width: 100%;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 12px 14px;
    min-height: 110px;
    resize: vertical;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
.input-textarea:focus {
    outline: none;
    border-color: #ef4444;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
}

.btn-primary {
    background: linear-gradient(90deg, #ef4444, #f97316);
    color: #fff;
    font-weight: 700;
    padding: 12px 20px;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: transform 0.1s ease, box-shadow 0.2s ease;
    box-shadow: 0 10px 20px rgba(239, 68, 68, 0.2);
}
.btn-primary:hover { transform: translateY(-1px); }
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }
.btn-link {
    display: inline-block;
    margin-top: 6px;
    color: #ef4444;
    font-weight: 700;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 16px;
    margin: 20px 0 12px;
}
.summary-card, .distribution-card {
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    padding: 16px;
    background: #fff;
}
.summary-score {
    display: flex;
    align-items: flex-end;
    gap: 4px;
    margin: 6px 0;
}
.summary-score__number { font-size: 40px; font-weight: 800; color: #ef4444; line-height: 1; }
.summary-score__outof { color: #9ca3af; font-weight: 600; }
.summary-meta { color: #6b7280; margin: 0; }
.summary-stars { display: flex; gap: 4px; margin-top: 6px; }
.summary-star { color: #e5e7eb; font-size: 18px; }
.summary-star--filled { color: #fbbf24; }

.distribution-list { display: grid; gap: 10px; }
.distribution-row { display: grid; grid-template-columns: 60px 1fr 40px; align-items: center; gap: 8px; }
.distribution-row__label { color: #4b5563; font-weight: 600; font-size: 0.95rem; }
.distribution-row__bar {
    background: #f3f4f6;
    border-radius: 999px;
    height: 10px;
    overflow: hidden;
}
.distribution-row__fill {
    display: block;
    height: 100%;
    background: linear-gradient(90deg, #f59e0b, #f97316);
}
.distribution-row__count { text-align: right; color: #6b7280; font-weight: 700; }

.reviews-list { margin-top: 20px; }
.reviews-list__header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
.reviews-stack { display: grid; gap: 14px; }
.review-item {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 12px;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 14px;
    background: #fff;
}
.review-item__avatar img {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    border: 2px solid #fee2e2;
}
.review-item__meta { display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap; }
.review-item__name { font-weight: 800; color: #111827; margin: 0; }
.review-item__time { margin: 0; color: #9ca3af; font-size: 0.9rem; }
.review-item__comment { margin: 8px 0 0; color: #374151; line-height: 1.6; }
.review-item__stars { display: flex; gap: 4px; }

.empty-state {
    text-align: center;
    padding: 40px 20px;
    border: 1px dashed #e5e7eb;
    border-radius: 16px;
    background: #f9fafb;
}
.empty-icon { font-size: 26px; color: #d1d5db; margin-bottom: 8px; }

.reviews-pagination { margin-top: 18px; }

@media (max-width: 640px) {
    .review-card__header { flex-direction: column; align-items: flex-start; }
    .distribution-row { grid-template-columns: 55px 1fr 34px; }
    .review-item { grid-template-columns: 1fr; }
    .review-item__avatar { order: 1; }
}
</style>

<script>
    // Handle review form submission via AJAX
    const reviewForm = document.getElementById('review-form');
    if (reviewForm) {
        reviewForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const button = this.querySelector('button[type="submit"]');
            button.disabled = true;
            button.textContent = 'Đang gửi...';

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload reviews to show the new one
                    loadReviews();
                } else {
                    alert(data.message || 'Đã có lỗi xảy ra.');
                    button.disabled = false;
                    button.textContent = 'Gửi đánh giá';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Đã có lỗi xảy ra, vui lòng thử lại.');
                button.disabled = false;
                button.textContent = 'Gửi đánh giá';
            });
        });
    }
</script>
