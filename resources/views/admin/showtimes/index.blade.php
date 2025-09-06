@extends('admin.layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Danh sách lịch chiếu</h5>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="refreshTable()">
                            <i class="bx bx-refresh me-1"></i> Làm mới
                        </button>
                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover" id="showtimesTable">
                        <thead>
                            <tr>
                                <th>Phim</th>
                                <th>Phòng chiếu</th>
                                <th>Ngày chiếu</th>
                                <th>Giờ bắt đầu</th>
                                <th>Giờ kết thúc</th>
                                <th>Giá vé</th>
                                <th>Ghế trống</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse($showtimes as $showtime)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @php
                                                $posterPath = $showtime->movie->poster_url ?? null;
                                                $posterUrl = $posterPath ? asset('storage/' . $posterPath) : asset('assets/img/default/cinema.jpg');
                                            @endphp
                                            <img src="{{ $posterUrl }}" alt="Poster" class="rounded me-3" style="width: 48px; height: 48px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0">{{ $showtime->movie->title ?? 'N/A' }}</h6>
                                                <small class="text-muted">{{ $showtime->movie->duration ?? 0 }} phút</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-info">
                                            {{ $showtime->screen->screen_name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-medium">
                                            {{ $showtime->show_date ? date('d/m/Y', strtotime($showtime->show_date)) : 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-success">
                                            {{ $showtime->show_time ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-warning">
                                            {{ $showtime->end_time ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-primary">
                                            {{ number_format($showtime->base_price ?? 0) }} VNĐ
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                @php
                                                    $totalSeats = $showtime->screen->total_seats ?? 0;
                                                    $availableSeats = $showtime->available_seats ?? 0;
                                                    $percentage = $totalSeats > 0 ? ($availableSeats / $totalSeats) * 100 : 0;
                                                @endphp
                                                <div class="progress-bar bg-{{ $percentage > 50 ? 'success' : ($percentage > 20 ? 'warning' : 'danger') }}"
                                                     style="width: {{ $percentage }}%"></div>
                                            </div>
                                            <span class="text-muted small">{{ $availableSeats }}/{{ $totalSeats }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $now = now();
                                            $showDateTime = \Carbon\Carbon::parse($showtime->show_date . ' ' . $showtime->show_time);
                                            $endDateTime = \Carbon\Carbon::parse($showtime->show_date . ' ' . $showtime->end_time);

                                            if ($now < $showDateTime) {
                                                $status = 'Sắp chiếu';
                                                $statusClass = 'warning';
                                            } elseif ($now >= $showDateTime && $now <= $endDateTime) {
                                                $status = 'Đang chiếu';
                                                $statusClass = 'success';
                                            } else {
                                                $status = 'Đã chiếu';
                                                $statusClass = 'secondary';
                                            }
                                        @endphp
                                        <span class="badge bg-label-{{ $statusClass }}">
                                            {{ $status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route('admin.showtimes.edit', $showtime->showtime_id) }}">
                                                    <i class="bx bx-edit-alt me-1"></i> Chỉnh sửa
                                                </a>
                                                <a class="dropdown-item" href="javascript:void(0);"
                                                   onclick="viewShowtimeDetails({{ $showtime->showtime_id }})">
                                                    <i class="bx bx-show me-1"></i> Xem chi tiết
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item text-danger" href="javascript:void(0);"
                                                    onclick="deleteShowtime({{ $showtime->showtime_id }}, '{{ $showtime->movie->title ?? 'N/A' }}')">
                                                    <i class="bx bx-trash me-1"></i> Xóa
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bx bx-calendar-x bx-lg text-muted mb-2"></i>
                                            <h6 class="text-muted">Chưa có lịch chiếu nào</h6>
                                            <p class="text-muted small">Hãy tạo lịch chiếu đầu tiên để bắt đầu</p>
                                            <a href="{{ route('admin.showtimes.create') }}" class="btn btn-primary btn-sm">
                                                <i class="bx bx-plus me-1"></i> Thêm lịch chiếu
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Chi tiết lịch chiếu -->
    <div class="modal fade" id="showtimeDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chi tiết lịch chiếu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="showtimeDetailsContent">
                    <!-- Nội dung sẽ được load bằng AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let table = new DataTable('#showtimesTable', {
        responsive: true,
        order: [[2, 'asc'], [3, 'asc']], // Sắp xếp theo ngày và giờ
        columnDefs: [
            { orderable: false, targets: 8 } // Cột thao tác không sắp xếp được
        ]
    });

    function refreshTable() {
        table.ajax.reload();
        location.reload();
    }

    function viewShowtimeDetails(showtimeId) {
        // Hiển thị loading
        $('#showtimeDetailsContent').html('<div class="text-center"><i class="bx bx-loader-alt bx-spin bx-lg"></i><p>Đang tải...</p></div>');
        $('#showtimeDetailsModal').modal('show');

        // Load chi tiết bằng AJAX (có thể implement sau)
        setTimeout(() => {
            $('#showtimeDetailsContent').html(`
                <div class="alert alert-info">
                    <i class="bx bx-info-circle me-2"></i>
                    Tính năng xem chi tiết sẽ được phát triển trong phiên bản tiếp theo.
                </div>
            `);
        }, 1000);
    }

    function deleteShowtime(showtimeId, movieTitle) {
        if (confirm(`Bạn có chắc chắn muốn xóa lịch chiếu phim "${movieTitle}"?`)) {
            // Tạo form ẩn để submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/showtimes/${showtimeId}`;
            form.style.display = 'none';

            // Thêm CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            // Thêm method DELETE
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);

            document.body.appendChild(form);
            form.submit();
        }
    }

    // Hiển thị thông báo thành công nếu có
    @if(session('success'))
        // Tạo toast notification
        const toast = document.createElement('div');
        toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed top-0 end-0 m-3';
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bx bx-check-circle me-2"></i>
                    {{ session('success') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        document.body.appendChild(toast);

        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        // Tự động xóa toast sau 5 giây
        setTimeout(() => {
            toast.remove();
        }, 5000);
    @endif
</script>
@endpush
