@extends('client.layouts.app')
@section('title', 'Xác nhận đặt vé thành công')
@push('styles')
    {{-- Google Fonts: Inter for body, Yanone Kaffeesatz for headings --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Yanone+Kaffeesatz:wght@400;600;700&display=swap" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            /* New Red/White Palette */
            --primary-red: #D32F2F;
            --primary-red-dark: #C62828;
            --background-color: #F5F5F7; /* Soft gray backdrop */
            --surface-color: #FFFFFF;
            --text-primary: #111111;
            --text-secondary: #6c757d;
            --border-color: #E0E0E6;
            --charcoal-separator: #1F1F24;

            /* Status Colors (WCAG AA Compliant) */
            --success-color: #2E7D32;
            --warning-bg: #FFF8E1;
            --warning-text: #6D4C41;
            --warning-border: #FFB300;
            --info-border: #2196F3;
        }

        body {
            background-color: var(--background-color);
            color: var(--text-primary);
            font-family: 'Inter', sans-serif;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Yanone Kaffeesatz', sans-serif;
            color: var(--text-primary);
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .confirmation-container {
            max-width: 760px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .success-icon {
            font-size: 4rem;
            color: var(--success-color);
            margin-bottom: 0.5rem;
        }

        .booking-details {
            background: var(--surface-color);
            border-radius: 10px;
            padding: 30px;
            border: 1px solid var(--border-color);
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .booking-code {
            background: var(--surface-color);
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
            border-top: 4px solid var(--primary-red);
            border: 1px solid var(--border-color);
        }

        .booking-code h3 {
            color: var(--text-secondary);
            margin-bottom: 10px;
            font-size: 1rem;
            text-transform: uppercase;
            font-weight: 700;
        }

        .code-display {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: 2px;
        }

        .movie-info {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
            padding-bottom: 25px;
            border-bottom: 1px solid var(--border-color);
        }

        .movie-poster {
            width: 120px;
            height: 180px;
            object-fit: cover;
            border-radius: 8px;
            flex-shrink: 0;
        }

        .movie-details h3 {
            color: var(--primary-red);
            margin-bottom: 10px;
            font-size: 1.75rem;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table th,
        .details-table td {
            padding: 12px;
            color: var(--text-primary);
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .details-table th {
            color: var(--text-secondary);
            font-weight: 600;
            width: 30%;
        }

        .seat-list {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .seat-badge {
            background: var(--primary-red);
            color: white;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.9rem;
            transition: box-shadow 0.2s ease;
        }

        .action-buttons {
            text-align: center;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-align: center;
            margin: 0 10px;
            transition: all 0.3s ease;
        }
        .btn:focus-visible {
            outline: 2px solid var(--primary-red);
            outline-offset: 2px;
        }

        .btn-primary {
            background-color: var(--primary-red);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-red-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(211, 47, 47, 0.4);
        }

        .btn-secondary {
            background-color: transparent;
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background-color: var(--surface-dark);
            border-color: var(--text-light);
            transform: translateY(-2px);
        }

        .btn-success {
            background-color: var(--success-color); /* Kept for consistency */
            color: white;
        }

        .btn-success:hover {
            background-color: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(46, 125, 50, 0.4);
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 8px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            background-color: var(--surface-color);
            color: var(--text-primary);
        }

        .alert-info {
            border-left: 5px solid var(--info-border);
        }

        .alert-success {
            border-left: 5px solid var(--success-color);
        }

        .alert .fa-info-circle, .alert .fa-check-circle {
            font-size: 1.2rem;
            margin-top: 2px;
        }
        .alert .fa-info-circle { color: var(--info-border); }
        .alert .fa-check-circle { color: var(--success-color); }

        .badge {
            font-size: 0.85em;
            padding: .5em .8em;
            font-weight: 600;
        }
        .bg-success { background-color: var(--success-color) !important; }
        .bg-warning {
            background-color: var(--warning-bg) !important;
            color: var(--warning-text) !important;
            border: 1px solid var(--warning-border);
        }

        @media (max-width: 768px) {
            .confirmation-container {
                padding: 20px 15px;
            }
            .action-buttons {
                flex-direction: column;
                gap: 10px;
            }
            .btn {
                width: 100%;
                margin: 0;
            }
        }
    </style>
@endpush

@section('content')
<div class="confirmation-container">
    <div class="text-center mb-4">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1>Đặt vé thành công!</h1>
        <p>Vé của bạn đã được đặt thành công. Vui lòng kiểm tra thông tin bên dưới.</p>
    </div>

    <div class="booking-details">
        <div class="booking-code">
            <h3>Mã đặt vé của bạn</h3>
            <div class="code-display">{{ $booking->booking_code }}</div>
        </div>

        <div class="movie-info">
            <img src="{{ '/storage/' . $booking->showtime->movie->poster_url ?? 'https://via.placeholder.com/120x160/1E1E1E/A0A0A0?text=Poster' }}"
                 alt="{{ $booking->showtime->movie->title }}" class="movie-poster">
            <div class="movie-details">
                <h3 class="h2">{{ $booking->showtime->movie->title }}</h3>
                <p class="text-muted"><strong>Rạp:</strong> {{ $booking->showtime->screen->cinema->name }} - {{ $booking->showtime->screen->name }}</p>
                <p class="text-muted"><strong>Ngày:</strong> {{ \Carbon\Carbon::parse($booking->showtime->show_date)->format('d/m/Y') }}</p>
                <p class="text-muted"><strong>Giờ:</strong> {{ \Carbon\Carbon::parse($booking->showtime->show_time)->format('H:i') }}</p>
            </div>
        </div>

        <table class="details-table">
            <tr>
                <th>Ghế đã chọn:</th>
                <td>
                    <div class="seat-list">
                        @foreach($booking->bookingSeats as $bookingSeat)
                            <span class="seat-badge">{{ $bookingSeat->seat->row_name }}{{ $bookingSeat->seat->seat_number }}</span>
                        @endforeach
                    </div>
                </td>
            </tr>
            <tr>
                <th>Tổng số ghế:</th>
                <td>{{ $booking->bookingSeats->count() }} ghế</td>
            </tr>
            <tr>
                <th>Phương thức thanh toán:</th>
                <td>{{ $booking->payment_method }}</td>
            </tr>
            <tr>
                <th>Trạng thái đặt vé:</th>
                <td>
                    @if($booking->booking_status == 'Confirmed')
                        <span class="badge rounded-pill bg-success">Đã Xác Nhận</span>
                    @elseif($booking->booking_status == 'Pending')
                        <span class="badge rounded-pill bg-warning text-dark">Chờ Xử Lý</span>
                    @elseif($booking->booking_status == 'Cancelled')
                        <span class="badge rounded-pill bg-danger">Đã Hủy</span>
                    @elseif($booking->booking_status == 'Used')
                        <span class="badge rounded-pill bg-info">Đã Sử Dụng</span>
                    @elseif($booking->booking_status == 'Expired')
                        <span class="badge rounded-pill bg-secondary">Hết Hạn</span>
                    @else
                        <span class="badge rounded-pill bg-secondary">{{ $booking->booking_status }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Trạng thái thanh toán:</th>
                <td>
                    @if($booking->payment_status == 'Paid')
                        <span class="badge rounded-pill bg-success">Đã Thanh Toán</span>
                    @elseif($booking->payment_status == 'Pending')
                        <span class="badge rounded-pill bg-warning text-dark">Chờ Thanh Toán</span>
                    @elseif($booking->payment_status == 'Failed')
                        <span class="badge rounded-pill bg-danger">Thanh Toán Thất Bại</span>
                    @elseif($booking->payment_status == 'Refunded')
                        <span class="badge rounded-pill bg-info">Đã Hoàn Tiền</span>
                    @else
                        <span class="badge rounded-pill bg-secondary">{{ $booking->payment_status }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Tạm tính:</th>
                <td><strong>{{ number_format($booking->total_amount) }}đ</strong></td>
            </tr>
            @if($booking->discount_amount > 0)
            <tr>
                <th>Mã giảm giá:</th>
                <td>
                    @if($booking->promotion_code === 'WELCOME50K')
                        <span class="badge rounded-pill bg-info text-dark">WELCOME50K</span>
                    @else
                        <span class="badge rounded-pill bg-info text-dark">{{ $booking->promotion_code }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Giảm giá:</th>
                <td><span class="text-success fw-bold">-{{ number_format($booking->discount_amount) }}đ</span></td>
            </tr>
            @endif
            <tr>
                <th>Thành tiền:</th>
                <td><strong class="h5" style="color: var(--primary-color)">{{ number_format($booking->final_amount ?? $booking->total_amount) }}đ</strong></td>
            </tr>
            <tr>
                <th>Ngày đặt vé:</th>
                <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y H:i') }}</td>
            </tr>
        </table>

        @if($booking->customer_name || $booking->customer_phone || $booking->customer_email)
        <div class="mt-4 p-3" style="background: var(--background-color); border-radius: 8px;">
            <h5 class="mb-3">Thông tin liên hệ</h5>
            @if($booking->customer_name)
                <p class="text-muted"><strong>Họ tên:</strong> {{ $booking->customer_name }}</p>
            @endif
            @if($booking->customer_phone)
                <p class="text-muted"><strong>Số điện thoại:</strong> {{ $booking->customer_phone }}</p>
            @endif
            @if($booking->customer_email)
                <p class="text-muted"><strong>Email:</strong> {{ $booking->customer_email }}</p>
            @endif
        </div>
        @endif

        @if($booking->notes)
        <div class="mt-3">
            <strong>Ghi chú:</strong> {{ $booking->notes }}
        </div>
        @endif

        {{-- Food Items Section --}}
        @if($booking->bookingFoods->count() > 0)
        <div class="mt-4 p-3" style="background: var(--background-color); border-radius: 8px;">
            <h5 class="mb-3">Đồ ăn đã đặt</h5>
            <div class="row">
                @foreach($booking->bookingFoods as $food)
                <div class="col-md-6 mb-2">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">{{ $food->item->name }} x{{ $food->quantity }}</span>
                        <span class="fw-bold">{{ number_format($food->total_price) }}đ</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    @if($booking->payment_method == 'Cash')
    <div class="alert alert-info">
        <i class="fas fa-info-circle fa-fw"></i>
        <div>
            <strong>Lưu ý:</strong> Bạn sẽ thanh toán bằng tiền mặt khi đến rạp. Vui lòng đến trước giờ chiếu 15 phút để hoàn tất thanh toán và nhận vé.
        </div>
    </div>
    @else
    <div class="alert alert-success">
        <i class="fas fa-check-circle fa-fw"></i>
        <div>
            <strong>Thanh toán thành công!</strong> Vé đã được xác nhận. Bạn có thể đến thẳng cửa soát vé.
        </div>
    </div>
    @endif

    <div class="action-buttons">
        <a href="{{ route('booking.ticket', $booking) }}" class="btn btn-primary" target="_blank">
            <i class="fas fa-ticket-alt"></i> Xem vé
        </a>
        <a href="{{ route('user.bookings.index') }}" class="btn btn-secondary">
            <i class="fas fa-history"></i> Lịch sử đặt vé
        </a>
        <a href="{{ route('home') }}" class="btn btn-success">
            <i class="fas fa-home"></i> Trang chủ
        </a>
    </div>
</div>
@endsection
