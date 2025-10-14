@extends('client.layouts.app')
@section('title', 'Xác nhận đặt vé')
@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .confirmation-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .success-message {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .success-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }
        
        .booking-details {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .booking-code {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .booking-code h3 {
            color: #e61e4d;
            margin-bottom: 10px;
        }
        
        .code-display {
            font-size: 1.5rem;
            font-weight: bold;
            color: #28a745;
            letter-spacing: 2px;
        }
        
        .movie-info {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        
        .movie-poster {
            width: 120px;
            height: 160px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .movie-details h3 {
            color: #e61e4d;
            margin-bottom: 10px;
        }
        
        .details-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .details-table th,
        .details-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        
        .details-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            width: 30%;
        }
        
        .seat-list {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        
        .seat-badge {
            background: #e61e4d;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9rem;
        }
        
        .action-buttons {
            text-align: center;
            margin-top: 30px;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            margin: 0 10px;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background-color: #e61e4d;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #d70442;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }
        
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 5px;
        }
        
        .alert-info {
            color: #0c5460;
            background-color: #d1ecf1;
            border-color: #bee5eb;
        }
    </style>
@endpush

@section('content')
<div class="confirmation-container">
    <div class="success-message">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1>Đặt vé thành công!</h1>
        <p>Vé của bạn đã được đặt thành công. Vui lòng kiểm tra thông tin bên dưới.</p>
    </div>
    
    <div class="booking-details">
        <div class="booking-code">
            <h3>Mã đặt vé</h3>
            <div class="code-display">{{ $booking->booking_code }}</div>
        </div>
        
        <div class="movie-info">
            <img src="{{ $booking->showtime->movie->poster_url ?? 'https://via.placeholder.com/120x160/f5f5f5/333?text=Movie+Poster' }}" 
                 alt="{{ $booking->showtime->movie->title }}" class="movie-poster">
            <div class="movie-details">
                <h3>{{ $booking->showtime->movie->title }}</h3>
                <p><strong>Rạp:</strong> {{ $booking->showtime->screen->cinema->name }} - {{ $booking->showtime->screen->name }}</p>
                <p><strong>Ngày:</strong> {{ \Carbon\Carbon::parse($booking->showtime->show_date)->format('d/m/Y') }}</p>
                <p><strong>Giờ:</strong> {{ \Carbon\Carbon::parse($booking->showtime->show_time)->format('H:i') }}</p>
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
                <th>Trạng thái thanh toán:</th>
                <td>
                    @if($booking->payment_status == 'Paid')
                        <span class="badge bg-success">Đã thanh toán</span>
                    @elseif($booking->payment_status == 'Pending')
                        <span class="badge bg-warning">Chờ thanh toán</span>
                    @else
                        <span class="badge bg-danger">{{ $booking->payment_status }}</span>
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
                        <span class="badge bg-success">WELCOME50K - Ưu đãi người mới</span>
                    @else
                        <span class="badge bg-primary">{{ $booking->promotion_code }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Giảm giá:</th>
                <td><span class="text-success">-{{ number_format($booking->discount_amount) }}đ</span></td>
            </tr>
            @endif
            <tr>
                <th>Thành tiền:</th>
                <td><strong>{{ number_format($booking->final_amount ?? $booking->total_amount) }}đ</strong></td>
            </tr>
            <tr>
                <th>Ngày đặt vé:</th>
                <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
        
        @if($booking->customer_name || $booking->customer_phone || $booking->customer_email)
        <div class="mt-4 p-3" style="background: #f8f9fa; border-radius: 8px;">
            <h5 class="mb-3">Thông tin liên hệ</h5>
            @if($booking->customer_name)
                <p><strong>Họ tên:</strong> {{ $booking->customer_name }}</p>
            @endif
            @if($booking->customer_phone)
                <p><strong>Số điện thoại:</strong> {{ $booking->customer_phone }}</p>
            @endif
            @if($booking->customer_email)
                <p><strong>Email:</strong> {{ $booking->customer_email }}</p>
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
        <div class="mt-4 p-3" style="background: #f8f9fa; border-radius: 8px;">
            <h5 class="mb-3">Đồ ăn đã đặt</h5>
            <div class="row">
                @foreach($booking->bookingFoods as $food)
                <div class="col-md-6 mb-2">
                    <div class="d-flex justify-content-between">
                        <span>{{ $food->item->name }} x{{ $food->quantity }}</span>
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
        <i class="fas fa-info-circle"></i>
        <strong>Lưu ý:</strong> Bạn sẽ thanh toán bằng tiền mặt khi đến rạp. Vui lòng đến rạp trước giờ chiếu 15 phút để thanh toán và nhận vé.
    </div>
    @else
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <strong>Thanh toán thành công!</strong> Vé đã được xác nhận. Bạn có thể đến rạp trước giờ chiếu 15 phút để nhận vé.
    </div>
    @endif
    
    <div class="action-buttons">
        <a href="{{ route('booking.ticket', $booking) }}" class="btn btn-primary" target="_blank">
            <i class="fas fa-ticket-alt"></i> Xem vé
        </a>
        <a href="{{ route('account.bookings') }}" class="btn btn-secondary">
            <i class="fas fa-list"></i> Xem lịch sử đặt vé
        </a>
        <a href="{{ route('home') }}" class="btn btn-success">
            <i class="fas fa-home"></i> Về trang chủ
        </a>
    </div>
</div>
@endsection
