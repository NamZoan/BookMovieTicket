@extends('admin.layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Quản Lý Đơn Hàng</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="bx bx-arrow-back"></i> Quay Lại Dashboard
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Search and Filter Form -->
                        <form method="GET" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="search" class="form-label">Tìm Kiếm</label>
                                    <input type="text"
                                           class="form-control"
                                           id="search"
                                           name="search"
                                           value="{{ request('search') }}"
                                           placeholder="Mã đơn, tên khách hàng, email...">
                                </div>
                                <div class="col-md-2">
                                    <label for="status" class="form-label">Trạng Thái Đơn</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="">Tất Cả</option>
                                        @foreach($statusOptions as $value => $label)
                                            <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="payment_status" class="form-label">Trạng Thái Thanh Toán</label>
                                    <select class="form-select" id="payment_status" name="payment_status">
                                        <option value="">Tất Cả</option>
                                        @foreach($paymentStatusOptions as $value => $label)
                                            <option value="{{ $value }}" {{ request('payment_status') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="date_from" class="form-label">Từ Ngày</label>
                                    <input type="date"
                                           class="form-control"
                                           id="date_from"
                                           name="date_from"
                                           value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-2">
                                    <label for="date_to" class="form-label">Đến Ngày</label>
                                    <input type="date"
                                           class="form-control"
                                           id="date_to"
                                           name="date_to"
                                           value="{{ request('date_to') }}">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bx bx-search"></i>
                                        </button>
                                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary">
                                            <i class="bx bx-refresh"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Bookings Table -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã Đơn</th>
                                        <th>Khách Hàng</th>
                                        <th>Phim</th>
                                        <th>Rạp</th>
                                        <th>Ngày Chiếu</th>
                                        <th>Tổng Tiền</th>
                                        <th>Trạng Thái Đơn</th>
                                        <th>Trạng Thái Thanh Toán</th>
                                        <th>Ngày Đặt</th>
                                        <th>Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($bookings as $booking)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.bookings.show', $booking->booking_id) }}"
                                                   class="text-primary fw-semibold">
                                                    {{ $booking->booking_code }}
                                                </a>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="fw-semibold">{{ $booking->customer_name }}</div>
                                                    <small class="text-muted">{{ $booking->customer_phone }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold">{{ $booking->showtime->movie->title ?? 'N/A' }}</div>
                                            </td>
                                            <td>
                                                <div>{{ $booking->showtime->screen->cinema->name ?? 'N/A' }}</div>
                                            </td>
                                            <td>
                                                <div>
                                                    <div>{{ $booking->showtime->show_date ? \Carbon\Carbon::parse($booking->showtime->show_date)->format('d/m/Y') : 'N/A' }}</div>
                                                    <small class="text-muted">{{ $booking->showtime->show_time ? \Carbon\Carbon::parse($booking->showtime->show_time)->format('H:i') : 'N/A' }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold text-success">
                                                    {{ number_format($booking->final_amount, 0, ',', '.') }} VNĐ
                                                </div>
                                            </td>
                                            <td>
                                                @if($booking->booking_status == 'Confirmed')
                                                    <span class="badge bg-success">{{ $booking->booking_status }}</span>
                                                @elseif($booking->booking_status == 'Pending')
                                                    <span class="badge bg-warning">{{ $booking->booking_status }}</span>
                                                @elseif($booking->booking_status == 'Cancelled')
                                                    <span class="badge bg-danger">{{ $booking->booking_status }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $booking->booking_status }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($booking->payment_status == 'Paid')
                                                    <span class="badge bg-success">{{ $booking->payment_status }}</span>
                                                @elseif($booking->payment_status == 'Pending')
                                                    <span class="badge bg-warning">{{ $booking->payment_status }}</span>
                                                @elseif($booking->payment_status == 'Failed')
                                                    <span class="badge bg-danger">{{ $booking->payment_status }}</span>
                                                @else
                                                    <span class="badge bg-info">{{ $booking->payment_status }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div>{{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y H:i') : 'N/A' }}</div>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle"
                                                            type="button"
                                                            data-bs-toggle="dropdown">
                                                        Thao Tác
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item"
                                                               href="{{ route('admin.bookings.show', $booking->booking_id) }}">
                                                                <i class="bx bx-show me-2"></i>Xem Chi Tiết
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="bx bx-search-alt-2" style="font-size: 3rem;"></i>
                                                    <div class="mt-2">Không tìm thấy đơn hàng nào</div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($bookings->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $bookings->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->
</div>
@endsection
