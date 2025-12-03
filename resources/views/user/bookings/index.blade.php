@extends('client.layouts.app')

@section('title', 'Lịch Sử Đặt Vé')

@push('styles')
<style>
    :root {
        --brand: #e51c23;
        --brand-dark: #b5121c;
        --brand-soft: #ffe5e7;
    }
    .booking-page {
        background: radial-gradient(circle at 12% 20%, rgba(229, 28, 35, 0.08), transparent 30%),
                    radial-gradient(circle at 90% 10%, rgba(229, 28, 35, 0.06), transparent 30%);
    }
    .booking-hero {
        background: linear-gradient(120deg, var(--brand), var(--brand-dark));
        color: #fff;
        border-radius: 20px;
        padding: 22px 24px;
        box-shadow: 0 15px 40px rgba(229, 28, 35, 0.25);
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
    .filter-card, .booking-card {
        border: 1px solid #f2f2f2;
        box-shadow: 0 8px 20px rgba(0,0,0,0.04);
        border-radius: 16px;
    }
    .badge-code {
        background: var(--brand-soft);
        color: var(--brand-dark);
        border: 1px dashed var(--brand);
    }
    .price-tag { color: var(--brand-dark); }
    .status-pill {
        padding: 6px 10px;
        border-radius: 999px;
        font-weight: 700;
    }
    .status-confirmed { background:#22c55e1a; color:#15803d; }
    .status-pending { background:#f973161a; color:#c2410c; }
    .status-cancelled { background:#ef44441a; color:#b91c1c; }
    .status-used { background:#0ea5e91a; color:#0369a1; }
    .status-expired { background:#6b72801a; color:#374151; }
    .payment-pill { background:#fff4f4; color:var(--brand-dark); padding:6px 10px; border-radius:12px; font-weight:600; }
</style>
@endpush

@section('content')
<div class="container py-5 booking-page">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="booking-hero d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div>
                    <p class="mb-1 text-uppercase fw-bold" style="letter-spacing: 0.08em;">Lịch Sử Đặt Vé</p>
                    <h2 class="mb-0 fw-bold">Quản lý và xem lại đơn đặt vé của bạn</h2>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('home') }}" class="btn btn-ghost">
                        <i class="bx bx-home"></i> Về Trang Chủ
                    </a>
                </div>
            </div>

            <!-- Filter Form -->
            <div class="card filter-card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
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
                        <div class="col-md-3">
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
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-brand">
                                    <i class="bx bx-search"></i> Lọc
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Bookings List -->
            @forelse($bookings as $booking)
                <div class="card booking-card mb-4">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex align-items-start">
                                    <div class="me-3">
                                        @if($booking->showtime->movie->poster_url ?? false)
                                            <img src="{{ asset('storage/' . $booking->showtime->movie->poster_url) }}"
                                                 alt="{{ $booking->showtime->movie->title }}"
                                                 class="rounded"
                                                 style="width: 80px; height: 120px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                 style="width: 80px; height: 120px;">
                                                <i class="bx bx-movie text-muted" style="font-size: 2rem;"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="card-title mb-1 text-dark">{{ $booking->showtime->movie->title ?? 'N/A' }}</h5>
                                        <p class="text-muted mb-1">
                                            <i class="bx bx-building me-1"></i>
                                            {{ $booking->showtime->screen->cinema->name ?? 'N/A' }}
                                            - {{ $booking->showtime->screen->name ?? 'N/A' }}
                                        </p>
                                        <p class="text-muted mb-2">
                                            <i class="bx bx-calendar me-1"></i>
                                            {{ $booking->showtime->show_date ? \Carbon\Carbon::parse($booking->showtime->show_date)->format('d/m/Y') : 'N/A' }}
                                            lúc {{ $booking->showtime->show_time ? \Carbon\Carbon::parse($booking->showtime->show_time)->format('H:i') : 'N/A' }}
                                        </p>
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="badge badge-code">{{ $booking->booking_code }}</span>
                                            <small class="text-muted">
                                                Đặt lúc: {{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y H:i') : 'N/A' }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-end">
                                    <div class="mb-2">
                                        <span class="h5 price-tag fw-bold">
                                            {{ number_format($booking->final_amount, 0, ',', '.') }} VND
                                        </span>
                                    </div>
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
                                    <div class="mb-3">
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
                                    <a href="{{ route('user.bookings.show', $booking->booking_id) }}"
                                       class="btn btn-ghost">
                                        <i class="bx bx-show"></i> Xem Chi Tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="bx bx-movie text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 text-muted">Chưa Có Đơn Đặt Vé Nào</h4>
                    <p class="text-muted">Bạn chưa có đơn đặt vé nào. Hãy khám phá các bộ phim và đặt vé ngay!</p>
                    <a href="{{ route('home') }}" class="btn btn-brand">
                        <i class="bx bx-movie"></i> Khám Phá Phim
                    </a>
                </div>
            @endforelse

            <!-- Pagination -->
            @if($bookings->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $bookings->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
