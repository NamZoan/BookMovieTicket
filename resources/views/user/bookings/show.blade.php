@extends('client.layouts.app')

@section('title', 'Chi Tiết Vé - ' . $booking->booking_code)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Electronic Ticket -->
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h3 class="mb-2">
                        <i class="bx bx-movie"></i> VÉ XEM PHIM
                    </h3>
                    <p class="mb-0">Mã vé: <strong>{{ $booking->booking_code }}</strong></p>
                </div>

                <div class="card-body p-4">
                    <!-- Movie Information -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            @if($booking->showtime->movie->poster_url ?? false)
                                <img src="{{ asset('storage/' . $booking->showtime->movie->poster_url) }}"
                                     alt="{{ $booking->showtime->movie->title }}"
                                     class="img-fluid rounded shadow">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                     style="height: 200px;">
                                    <i class="bx bx-movie text-muted" style="font-size: 3rem;"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <h4 class="mb-2">{{ $booking->showtime->movie->title ?? 'N/A' }}</h4>
                            <div class="row">
                                <div class="col-sm-6">
                                    <p class="mb-1"><strong>Rạp:</strong> {{ $booking->showtime->screen->cinema->name ?? 'N/A' }}</p>
                                    <p class="mb-1"><strong>Phòng:</strong> {{ $booking->showtime->screen->name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-sm-6">
                                    <p class="mb-1"><strong>Ngày:</strong> {{ $booking->showtime->show_date ? \Carbon\Carbon::parse($booking->showtime->show_date)->format('d/m/Y') : 'N/A' }}</p>
                                    <p class="mb-1"><strong>Giờ:</strong> {{ $booking->showtime->show_time ? \Carbon\Carbon::parse($booking->showtime->show_time)->format('H:i') : 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Customer Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Thông Tin Khách Hàng</h6>
                            <p class="mb-1"><strong>Họ tên:</strong> {{ $booking->customer_name }}</p>
                            <p class="mb-1"><strong>Email:</strong> {{ $booking->customer_email }}</p>
                            <p class="mb-0"><strong>SĐT:</strong> {{ $booking->customer_phone }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Trạng Thái</h6>
                            <div class="mb-2">
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
                            <div class="mb-2">
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
                    </div>

                    <hr>

                    <!-- Seats Information -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">Ghế Đã Đặt</h6>
                        @if($booking->bookingSeats->count() > 0)
                            <div class="row">
                                @foreach($booking->bookingSeats as $bookingSeat)
                                    <div class="col-md-3 mb-2">
                                        <div class="border rounded p-2 text-center bg-light">
                                            <div class="fw-bold">{{ $bookingSeat->seat->seat_name ?? 'N/A' }}</div>
                                            <small class="text-muted">{{ $bookingSeat->seat->seat_type ?? 'N/A' }}</small>
                                            <div class="text-success fw-semibold">
                                                {{ number_format($bookingSeat->seat_price, 0, ',', '.') }} VNĐ
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">Không có ghế nào được đặt</p>
                        @endif
                    </div>

                    <!-- Food Items -->
                    @if($booking->bookingFoods->count() > 0)
                        <div class="mb-4">
                            <h6 class="text-primary mb-3">Đồ Ăn & Nước Uống</h6>
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
                    @endif

                    <hr>

                    <!-- Payment Summary -->
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="text-primary mb-3">Tổng Kết Thanh Toán</h6>
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
                        <div class="col-md-4">
                            <!-- QR Code Placeholder -->
                            <div class="text-center">
                                <div class="border rounded p-3 mb-3" style="height: 150px; display: flex; align-items: center; justify-content: center;">
                                    <div class="text-center">
                                        <i class="bx bx-qr-scan text-muted" style="font-size: 3rem;"></i>
                                        <div class="text-muted small">QR Code</div>
                                    </div>
                                </div>
                                <small class="text-muted">Quét mã QR tại rạp để vào xem phim</small>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="mt-4 pt-3 border-top">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <small class="text-muted">Ngày đặt vé</small>
                                <div class="fw-semibold">{{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y H:i') : 'N/A' }}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Phương thức thanh toán</small>
                                <div class="fw-semibold">{{ $booking->payment_method ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Ngày thanh toán</small>
                                <div class="fw-semibold">{{ $booking->payment_date ? \Carbon\Carbon::parse($booking->payment_date)->format('d/m/Y H:i') : 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="text-center mt-4">
                <a href="{{ route('user.bookings.index') }}" class="btn btn-outline-primary me-2">
                    <i class="bx bx-arrow-back"></i> Quay Lại Danh Sách
                </a>
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="bx bx-printer"></i> In Vé
                </button>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .container .row:last-child {
        display: none !important;
    }
    .card {
        box-shadow: none !important;
        border: 1px solid #000 !important;
    }
}
</style>
@endsection
