@extends('client.layouts.app')

@section('title', 'Lịch sử đánh giá')

@push('styles')
<style>
    .review-page {
        background: radial-gradient(circle at 12% 20%, rgba(229, 28, 35, 0.08), transparent 30%),
                    radial-gradient(circle at 90% 10%, rgba(229, 28, 35, 0.06), transparent 30%);
    }
    .review-hero {
        background: linear-gradient(120deg, #e51c23, #b5121c);
        color: #fff;
        border-radius: 20px;
        padding: 22px 24px;
        box-shadow: 0 15px 40px rgba(229, 28, 35, 0.25);
    }
    .review-card {
        border: 1px solid #f2f2f2;
        box-shadow: 0 8px 20px rgba(0,0,0,0.04);
        border-radius: 16px;
    }
    .rating-pill {
        background: #fff4f4;
        color: #b5121c;
        padding: 6px 10px;
        border-radius: 12px;
        font-weight: 600;
    }
    .status-approved { background:#22c55e1a; color:#15803d; }
    .status-pending { background:#f973161a; color:#c2410c; }
</style>
@endpush

@section('content')
<div class="container py-5 review-page">
    <div class="row">
        <div class="col-12">
            <div class="review-hero d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div>
                    <p class="mb-1 text-uppercase fw-bold" style="letter-spacing: 0.08em;">Lịch sử đánh giá</p>
                    <h2 class="mb-0 fw-bold">Đánh giá phim của bạn</h2>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('home') }}" class="btn btn-light">
                        Về trang chủ
                    </a>
                </div>
            </div>

            @forelse($reviews as $review)
                <div class="card review-card mb-4">
                    <div class="card-body">
                        <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center">
                            <div class="flex-shrink-0">
                                @if($review->movie && $review->movie->poster_url)
                                    <img src="{{ asset('storage/' . $review->movie->poster_url) }}"
                                         alt="{{ $review->movie->title }}"
                                         class="rounded"
                                         style="width: 72px; height: 108px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                         style="width: 72px; height: 108px;">
                                        <span class="text-muted">Không có hình ảnh</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-1">{{ $review->movie->title ?? 'Phim không xác định' }}</h5>
                                <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                                    <span class="rating-pill">Đánh giá: {{ $review->rating }}/5</span>
                                    @if($review->is_approved)
                                        <span class="badge status-approved">Đã duyệt</span>
                                    @else
                                        <span class="badge status-pending">Đang chờ</span>
                                    @endif
                                </div>
                                <p class="mb-2 text-muted">
                                    {{ $review->comment ?: 'Không có bình luận.' }}
                                </p>
                                <small class="text-muted">
                                    Đánh giá lúc: {{ $review->created_at ? $review->created_at->format('d/m/Y H:i') : 'N/A' }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <h4 class="text-muted">Chưa có đánh giá nào</h4>
                    <p class="text-muted">Khi bạn đánh giá một bộ phim, nó sẽ hiển thị ở đây.</p>
                    <a href="{{ route('movies.index') }}" class="btn btn-outline-primary">
                        Duyệt phim
                    </a>
                </div>
            @endforelse

            @if($reviews->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $reviews->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
