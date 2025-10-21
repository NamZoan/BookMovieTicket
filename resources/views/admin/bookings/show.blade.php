@extends('admin.layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <!-- Header -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Chi Tiết Đơn Hàng: {{ $booking->booking_code }}</h5>
                            <small class="text-muted">Đặt lúc: {{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y H:i') : 'N/A' }}</small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary">
                                <i class="bx bx-arrow-back"></i> Quay Lại
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Booking Information -->
                    <div class="col-lg-8">
                        <!-- Customer Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Thông Tin Khách Hàng</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Họ Tên</label>
                                            <div>{{ $booking->customer_name }}</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Email</label>
                                            <div>{{ $booking->customer_email }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Số Điện Thoại</label>
                                            <div>{{ $booking->customer_phone }}</div>
                                        </div>
                                        @if($booking->user)
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Tài Khoản</label>
                                                <div>
                                                    <a href="#" class="text-primary">{{ $booking->user->full_name }}</a>
                                                    <small class="text-muted d-block">{{ $booking->user->email }}</small>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Movie & Showtime Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Thông Tin Suất Chiếu</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Tên Phim</label>
                                            <div>{{ $booking->showtime->movie->title ?? 'N/A' }}</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Rạp Chiếu</label>
                                            <div>{{ $booking->showtime->screen->cinema->name ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Phòng Chiếu</label>
                                            <div>{{ $booking->showtime->screen->name ?? 'N/A' }}</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Ngày & Giờ Chiếu</label>
                                            <div>
                                                {{ $booking->showtime->show_date ? \Carbon\Carbon::parse($booking->showtime->show_date)->format('d/m/Y') : 'N/A' }}
                                                lúc {{ $booking->showtime->show_time ? \Carbon\Carbon::parse($booking->showtime->show_time)->format('H:i') : 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Seats Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Ghế Đã Đặt</h6>
                            </div>
                            <div class="card-body">
                                @if($booking->bookingSeats->count() > 0)
                                    <div class="row">
                                        @foreach($booking->bookingSeats as $bookingSeat)
                                            <div class="col-md-3 mb-2">
                                                <div class="border rounded p-2 text-center">
                                                    <div class="fw-semibold">{{ $bookingSeat->seat->seat_name ?? 'N/A' }}</div>
                                                    <small class="text-muted">{{ $bookingSeat->seat->seat_type ?? 'N/A' }}</small>
                                                    <div class="text-success fw-semibold">
                                                        {{ number_format($bookingSeat->seat_price, 0, ',', '.') }} VNĐ
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center text-muted py-3">
                                        <i class="bx bx-chair" style="font-size: 2rem;"></i>
                                        <div>Không có ghế nào được đặt</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Food Items -->
                        @if($booking->bookingFoods->count() > 0)
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Đồ Ăn & Nước Uống</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Tên Món</th>
                                                    <th>Số Lượng</th>
                                                    <th>Đơn Giá</th>
                                                    <th>Thành Tiền</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($booking->bookingFoods as $bookingFood)
                                                    <tr>
                                                        <td>{{ $bookingFood->foodItem->name ?? 'N/A' }}</td>
                                                        <td>{{ $bookingFood->quantity }}</td>
                                                        <td>{{ number_format($bookingFood->unit_price, 0, ',', '.') }} VNĐ</td>
                                                        <td>{{ number_format($bookingFood->total_price, 0, ',', '.') }} VNĐ</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Payment & Status Information -->
                    <div class="col-lg-4">
                        <!-- Payment Summary -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Tổng Kết Thanh Toán</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tổng tiền vé:</span>
                                    <span>{{ number_format($booking->total_amount, 0, ',', '.') }} VNĐ</span>
                                </div>
                                @if($booking->discount_amount > 0)
                                    <div class="d-flex justify-content-between mb-2 text-success">
                                        <span>Giảm giá:</span>
                                        <span>-{{ number_format($booking->discount_amount, 0, ',', '.') }} VNĐ</span>
                                    </div>
                                @endif
                                <hr>
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>Thành tiền:</span>
                                    <span class="text-success">{{ number_format($booking->final_amount, 0, ',', '.') }} VNĐ</span>
                                </div>
                                @if($booking->promotion_code)
                                    <div class="mt-2">
                                        <small class="text-muted">Mã khuyến mãi: {{ $booking->promotion_code }}</small>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Status Update -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Cập Nhật Trạng Thái</h6>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('admin.bookings.update', $booking->booking_id) }}">
                                    @csrf
                                    @method('PUT')

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Trạng Thái Hiện Tại</label>
                                        <div>
                                            @if($booking->booking_status == 'Confirmed')
                                                <span class="badge bg-success fs-6">{{ $booking->booking_status }}</span>
                                            @elseif($booking->booking_status == 'Pending')
                                                <span class="badge bg-warning fs-6">{{ $booking->booking_status }}</span>
                                            @elseif($booking->booking_status == 'Cancelled')
                                                <span class="badge bg-danger fs-6">{{ $booking->booking_status }}</span>
                                            @else
                                                <span class="badge bg-secondary fs-6">{{ $booking->booking_status }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="booking_status" class="form-label">Cập Nhật Trạng Thái</label>
                                        <select class="form-select" id="booking_status" name="booking_status" required>
                                            @foreach($statusOptions as $value => $label)
                                                <option value="{{ $value }}" {{ $booking->booking_status == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bx bx-check"></i> Cập Nhật Trạng Thái
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Payment Information -->
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Thông Tin Thanh Toán</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Phương Thức Thanh Toán</label>
                                    <div>{{ $booking->payment_method ?? 'N/A' }}</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Trạng Thái Thanh Toán</label>
                                    <div>
                                        @if($booking->payment_status == 'Paid')
                                            <span class="badge bg-success">{{ $booking->payment_status }}</span>
                                        @elseif($booking->payment_status == 'Pending')
                                            <span class="badge bg-warning">{{ $booking->payment_status }}</span>
                                        @elseif($booking->payment_status == 'Failed')
                                            <span class="badge bg-danger">{{ $booking->payment_status }}</span>
                                        @else
                                            <span class="badge bg-info">{{ $booking->payment_status }}</span>
                                        @endif
                                    </div>
                                </div>

                                @if($booking->payment_date)
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Ngày Thanh Toán</label>
                                        <div>{{ \Carbon\Carbon::parse($booking->payment_date)->format('d/m/Y H:i') }}</div>
                                    </div>
                                @endif

                                @if($booking->expires_at)
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Hết Hạn Lúc</label>
                                        <div>{{ \Carbon\Carbon::parse($booking->expires_at)->format('d/m/Y H:i') }}</div>
                                    </div>
                                @endif

                                @if($booking->notes)
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Ghi Chú</label>
                                        <div class="border rounded p-2 bg-light">{{ $booking->notes }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->
</div>
@endsection
