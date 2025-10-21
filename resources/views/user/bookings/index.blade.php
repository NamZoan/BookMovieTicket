@extends('client.layouts.app')

@section('title', 'Lịch Sử Đặt Vé')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Lịch Sử Đặt Vé</h2>
                    <p class="text-muted mb-0">Quản lý và xem lại các đơn đặt vé của bạn</p>
                </div>
                <div>
                    <a href="{{ route('home') }}" class="btn btn-outline-primary">
                        <i class="bx bx-home"></i> Về Trang Chủ
                    </a>
                </div>
            </div>

            <!-- Filter Form -->
            <div class="card mb-4">
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
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-search"></i> Lọc
                                </button>
                                <a href="{{ route('user.bookings.index') }}" class="btn btn-outline-secondary">
                                    <i class="bx bx-refresh"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Bookings List -->
            @forelse($bookings as $booking)
                <div class="card mb-4">
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
                                        <h5 class="card-title mb-1">{{ $booking->showtime->movie->title ?? 'N/A' }}</h5>
                                        <p class="text-muted mb-2">
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
                                            <span class="badge bg-primary">{{ $booking->booking_code }}</span>
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
                                        <span class="h5 text-success">
                                            {{ number_format($booking->final_amount, 0, ',', '.') }} VNĐ
                                        </span>
                                    </div>
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
                                    <div class="mb-3">
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
                                    <a href="{{ route('user.bookings.show', $booking->booking_id) }}"
                                       class="btn btn-outline-primary">
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
                    <a href="{{ route('home') }}" class="btn btn-primary">
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
