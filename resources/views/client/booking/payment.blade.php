@extends('client.layouts.app')
@section('title', 'Thanh toán')
@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .payment-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .booking-summary {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .movie-info {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
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
        
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .summary-table th,
        .summary-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        
        .summary-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        .total-row {
            font-weight: bold;
            font-size: 1.1em;
            color: #e61e4d;
        }
        
        .payment-form {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #e61e4d;
            box-shadow: 0 0 0 2px rgba(230, 30, 77, 0.25);
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-primary {
            background-color: #e61e4d;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #d70442;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 5px;
        }
        
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
    </style>
@endpush

@section('content')
<div class="payment-container">
    <h1 class="text-center mb-4">Thanh toán đặt vé</h1>
    
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    <div class="booking-summary">
        <h3>Thông tin đặt vé</h3>
        
        <div class="movie-info">
            <img src="{{ $showtime->movie->poster_url ?? 'https://via.placeholder.com/120x160/f5f5f5/333?text=Movie+Poster' }}" 
                 alt="{{ $showtime->movie->title }}" class="movie-poster">
            <div class="movie-details">
                <h3>{{ $showtime->movie->title }}</h3>
                <p><strong>Rạp:</strong> {{ $showtime->screen->cinema->name }} - {{ $showtime->screen->name }}</p>
                <p><strong>Ngày:</strong> {{ \Carbon\Carbon::parse($showtime->show_date)->format('d/m/Y') }}</p>
                <p><strong>Giờ:</strong> {{ \Carbon\Carbon::parse($showtime->show_time)->format('H:i') }}</p>
            </div>
        </div>
        
        <table class="summary-table">
            <thead>
                <tr>
                    <th>Ghế đã chọn</th>
                    <th>Giá vé</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($selectedSeats as $seat)
                <tr>
                    <td>Ghế {{ $seat->row_name }}{{ $seat->seat_number }}</td>
                    <td>{{ number_format($bookingData['seat_prices']['seats'][$seat->seat_id] ?? $showtime->base_price) }}đ</td>
                    <td>{{ number_format($bookingData['seat_prices']['seats'][$seat->seat_id] ?? $showtime->base_price) }}đ</td>
                </tr>
                @endforeach
                
                @if(!empty($bookingData['food_items']))
                    @foreach($bookingData['food_items'] as $foodItem)
                    <tr>
                        <td>{{ $foodItem['name'] }} x{{ $foodItem['quantity'] }}</td>
                        <td>{{ number_format($foodItem['unit_price']) }}đ</td>
                        <td>{{ number_format($foodItem['total_price']) }}đ</td>
                    </tr>
                    @endforeach
                @endif
                
                <tr class="total-row">
                    <td colspan="2"><strong>Tổng cộng:</strong></td>
                    <td><strong>{{ number_format($bookingData['total_amount']) }}đ</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="payment-form">
        <h3>Thông tin thanh toán</h3>
        
        <form method="POST" action="{{ route('booking.process-payment') }}">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Phương thức thanh toán</label>
                <select name="payment_method" class="form-control" required>
                    <option value="Cash" {{ $bookingData['payment_method'] == 'Cash' ? 'selected' : '' }}>Tiền mặt</option>
                    <option value="Credit Card" {{ $bookingData['payment_method'] == 'Credit Card' ? 'selected' : '' }}>Thẻ tín dụng</option>
                    <option value="Banking" {{ $bookingData['payment_method'] == 'Banking' ? 'selected' : '' }}>Chuyển khoản</option>
                    <option value="E-Wallet" {{ $bookingData['payment_method'] == 'E-Wallet' ? 'selected' : '' }}>Ví điện tử</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Mã khuyến mãi (nếu có)</label>
                <input type="text" name="promotion_code" class="form-control" placeholder="Nhập mã khuyến mãi">
            </div>
            
            <div class="form-group">
                <label class="form-label">Ghi chú</label>
                <textarea name="user_notes" class="form-control" rows="3" placeholder="Ghi chú thêm (tùy chọn)"></textarea>
            </div>
            
            <div class="text-center">
                <a href="{{ route('booking.seatSelection', $showtime) }}" class="btn btn-secondary me-3">Quay lại</a>
                <button type="submit" class="btn btn-primary">Xác nhận thanh toán</button>
            </div>
        </form>
    </div>
</div>
@endsection
