@extends('client.layouts.app')

@section('title', 'Chi Tiết Vé - ' . $booking->booking_code)

@push('styles')
<style>
    :root {
        --brand: #e51c23;
        --brand-dark: #b5121c;
        --brand-soft: #ffe5e7;
    }
    .ticket-page {
        background: radial-gradient(circle at 5% 10%, rgba(229, 28, 35, 0.08), transparent 28%),
                    radial-gradient(circle at 90% 15%, rgba(229, 28, 35, 0.06), transparent 32%);
    }
    .ticket-hero {
        background: linear-gradient(120deg, var(--brand), var(--brand-dark));
        color: #fff;
        border-radius: 20px;
        padding: 22px 24px;
        box-shadow: 0 15px 40px rgba(229, 28, 35, 0.25);
        text-align: center;
    }
    .ticket-card {
        border: 1px solid #f2f2f2;
        box-shadow: 0 12px 30px rgba(0,0,0,0.06);
        border-radius: 18px;
        overflow: hidden;
    }
    .ticket-section-title {
        color: var(--brand-dark);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-size: 0.95rem;
    }
    .btn-brand {
        background: linear-gradient(90deg, var(--brand), var(--brand-dark));
        color: #fff;
        border: none;
        box-shadow: 0 12px 24px rgba(229, 28, 35, 0.25);
    }
    .btn-brand:hover { color: #fff; filter: brightness(1.05); }
    .btn-ghost {
        border: 1px solid var(--brand);
        color: var(--brand);
        background: #fff;
    }
    .btn-ghost:hover { background: var(--brand-soft); color: var(--brand-dark); }
    .status-pill {
        padding: 7px 12px;
        border-radius: 999px;
        font-weight: 700;
        display: inline-block;
    }
    .status-confirmed { background:#22c55e1a; color:#15803d; }
    .status-pending { background:#f973161a; color:#c2410c; }
    .status-cancelled { background:#ef44441a; color:#b91c1c; }
    .status-used { background:#0ea5e91a; color:#0369a1; }
    .status-expired { background:#6b72801a; color:#374151; }
    .payment-pill { background:#fff4f4; color:var(--brand-dark); padding:7px 12px; border-radius:12px; font-weight:600; }
    .price-strong { color: var(--brand-dark); font-weight: 800; }
</style>
@endpush

@section('content')
<div class="container py-5 ticket-page">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="ticket-hero mb-4">
                <h3 class="mb-2 fw-bold"><i class="bx bx-movie"></i> VÉ XEM PHIM</h3>
                <p class="mb-0">Mã vé: <strong>{{ $booking->booking_code }}</strong></p>
            </div>

            <!-- Electronic Ticket -->
            <div class="card ticket-card border-0">
                <div class="card-body p-4">
                    <!-- Movie Information -->
                    <div class="row mb-4 g-3 align-items-center">
                        <div class="col-md-3">
                            @if($booking->showtime->movie->poster_url ?? false)
                                <img src="{{ asset('storage/' . $booking->showtime->movie->poster_url) }}"
                                     alt="{{ $booking->showtime->movie->title }}"
                                     class="img-fluid rounded shadow">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="bx bx-movie text-muted" style="font-size: 3rem;"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <h4 class="mb-2 fw-bold">{{ $booking->showtime->movie->title ?? 'N/A' }}</h4>
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
                            <div class="ticket-section-title mb-2">Thông Tin Khách Hàng</div>
                            <p class="mb-1"><strong>Họ tên:</strong> {{ $booking->customer_name }}</p>
                            <p class="mb-1"><strong>Email:</strong> {{ $booking->customer_email }}</p>
                            <p class="mb-0"><strong>SĐT:</strong> {{ $booking->customer_phone }}</p>
                        </div>
                        <div class="col-md-6">
                            <div class="ticket-section-title mb-2">Trạng Thái</div>
                            <div class="mb-2">
                                @if($booking->booking_status == 'Confirmed')
                                    <span class="status-pill status-confirmed">Đã Xác Nhận</span>
                                @elseif($booking->booking_status == 'Pending')
                                    <span class="status-pill status-pending">Chờ Xử Lý</span>
                                @elseif($booking->booking_status == 'Cancelled')
                                    <span class="status-pill status-cancelled">Đã Hủy</span>
                                @elseif($booking->booking_status == 'Used')
                                    <span class="status-pill status-used">Đã Sử Dụng</span>
                                @elseif($booking->booking_status == 'Expired')
                                    <span class="status-pill status-expired">Hết Hạn</span>
                                @else
                                    <span class="status-pill status-expired">{{ $booking->booking_status }}</span>
                                @endif
                            </div>
                            <div class="mb-2">
                                @if($booking->payment_status == 'Paid')
                                    <span class="payment-pill">Đã Thanh Toán</span>
                                @elseif($booking->payment_status == 'Pending')
                                    <span class="payment-pill">Chờ Thanh Toán</span>
                                @elseif($booking->payment_status == 'Failed')
                                    <span class="payment-pill">Thanh Toán Thất Bại</span>
                                @elseif($booking->payment_status == 'Refunded')
                                    <span class="payment-pill">Đã Hoàn Tiền</span>
                                @else
                                    <span class="payment-pill">{{ $booking->payment_status }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Seats Information -->
                    <div class="mb-4">
                        <div class="ticket-section-title mb-2">Ghế Đã Đặt</div>
                        @if($booking->bookingSeats->count() > 0)
                            <div class="row">
                                @foreach($booking->bookingSeats as $bookingSeat)
                                    <div class="col-md-3 mb-2">
                                        <div class="border rounded p-2 text-center bg-light">
                                            <div class="fw-bold">{{ $bookingSeat->seat->row_name ?? '' }}{{ $bookingSeat->seat->seat_number ?? '' }}</div>
                                            <small class="text-muted">{{ $bookingSeat->seat->seat_type ?? 'Normal' }}</small>
                                            <div class="text-success fw-semibold">
                                                {{ number_format($bookingSeat->seat_price, 0, ',', '.') }} VND
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
                            <div class="ticket-section-title mb-2">Đồ Ăn & Nước Uống</div>
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
                                                <td>{{ number_format($bookingFood->unit_price, 0, ',', '.') }} VND</td>
                                                <td>{{ number_format($bookingFood->total_price, 0, ',', '.') }} VND</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <hr>

                    <!-- Payment Summary -->
                    <div class="row g-3 align-items-start">
                        <div class="col-md-8">
                            <div class="ticket-section-title mb-2">Tổng Kết Thanh Toán</div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tổng tiền vé:</span>
                                <span>{{ number_format($booking->total_amount, 0, ',', '.') }} VND</span>
                            </div>
                            @if($booking->discount_amount > 0)
                                <div class="d-flex justify-content-between mb-2 text-success">
                                    <span>Giảm giá:</span>
                                    <span>-{{ number_format($booking->discount_amount, 0, ',', '.') }} VND</span>
                                </div>
                            @endif
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Thành tiền:</span>
                                <span class="price-strong">{{ number_format($booking->final_amount, 0, ',', '.') }} VND</span>
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
                <a href="{{ route('user.bookings.index') }}" class="btn btn-ghost me-2">
                    <i class="bx bx-arrow-back"></i> Quay Lại Danh Sách
                </a>
                <button onclick="window.print()" class="btn btn-brand">
                    <i class="bx bx-printer"></i> In Vé
                </button>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .container .row:last-child { display: none !important; }
    .ticket-card { box-shadow: none !important; border: 1px solid #000 !important; }
}
</style>
@endsection
