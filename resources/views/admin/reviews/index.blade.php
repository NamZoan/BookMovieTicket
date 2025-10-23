@extends('admin.layouts.app')

@section('title', 'Quản lý đánh giá')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý đánh giá</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Danh sách đánh giá</h6>
                <div class="filter-group">
                    <button class="btn btn-outline-primary btn-sm filter-btn" data-filter="all">Tất cả</button>
                    <button class="btn btn-outline-warning btn-sm filter-btn" data-filter="pending">Chờ duyệt</button>
                    <button class="btn btn-outline-success btn-sm filter-btn" data-filter="approved">Đã duyệt</button>
                    <button class="btn btn-outline-danger btn-sm filter-btn" data-filter="rejected">Đã từ chối</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="reviewsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Phim</th>
                            <th>Người đánh giá</th>
                            <th>Điểm</th>
                            <th>Nội dung</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reviews as $review)
                        <tr class="review-row {{ $review->is_approved ? 'table-success' : 'table-warning' }}">
                            <td>{{ $review->review_id }}</td>
                            <td>{{ $review->movie->title }}</td>
                            <td>{{ $review->user->name }}</td>
                            <td>{{ $review->rating }}/10</td>
                            <td>
                                <button type="button" class="btn btn-link view-comment" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#commentModal"
                                        data-comment="{{ $review->comment }}">
                                    Xem nội dung
                                </button>
                            </td>
                            <td>
                                @if($review->is_approved)
                                    <span class="badge bg-success">Đã duyệt</span>
                                @else
                                    <span class="badge bg-warning">Chờ duyệt</span>
                                @endif
                            </td>
                            <td>{{ $review->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if(!$review->is_approved)
                                    <form action="{{ route('admin.reviews.approve', $review) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-check"></i> Duyệt
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.reviews.reject', $review) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-times"></i> Từ chối
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $reviews->links() }}
        </div>
    </div>
</div>

<!-- Comment Modal -->
<div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commentModalLabel">Nội dung đánh giá</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="commentContent"></p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .filter-group {
        gap: 0.5rem;
        display: flex;
    }

    .filter-btn.active {
        border-width: 2px;
    }

    .review-row {
        transition: all 0.3s ease;
    }

    .review-row:hover {
        background-color: rgba(0,0,0,0.05) !important;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Handle comment modal
    $('.view-comment').on('click', function() {
        const comment = $(this).data('comment');
        $('#commentContent').text(comment);
    });

    // Handle filters
    $('.filter-btn').on('click', function() {
        const filter = $(this).data('filter');
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');

        if (filter === 'all') {
            $('.review-row').show();
        } else {
            $('.review-row').hide();
            $(`.review-row.${filter}`).show();
        }
    });

    // Initialize DataTable
    $('#reviewsTable').DataTable({
        "order": [[6, "desc"]], // Sort by created_at by default
        "pageLength": 25,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Vietnamese.json"
        }
    });
});
</script>
@endpush