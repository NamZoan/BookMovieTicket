@extends('client.layouts.app')
@section('title', 'Thanh toán')
@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .payment-container {
            max-width: 1000px;
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
            margin-top: 20px;
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

        .customer-form {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }

        .form-control {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: #e61e4d;
            box-shadow: 0 0 0 0.2rem rgba(230, 30, 77, 0.25);
        }

        .payment-methods {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .payment-option {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 15px;
        }

        .payment-option:hover {
            border-color: #e61e4d;
            background-color: #fff5f5;
        }

        .payment-option.selected {
            border-color: #e61e4d;
            background-color: #fff5f5;
        }

        .payment-option i {
            font-size: 2.5rem;
            color: #6c757d;
            margin-bottom: 10px;
        }

        .payment-option.selected i {
            color: #e61e4d;
        }

        .btn-payment {
            background: linear-gradient(45deg, #e61e4d, #d70442);
            color: white;
            border: none;
            border-radius: 25px;
            padding: 15px 40px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
        }

        .btn-payment:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(230, 30, 77, 0.4);
        }

        .btn-payment:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .countdown {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
        }

        .countdown-text {
            font-weight: 600;
            color: #856404;
        }

        .countdown-timer {
            font-size: 1.5rem;
            font-weight: bold;
            color: #e61e4d;
        }

        .terms-checkbox {
            margin: 20px 0;
        }

        .terms-checkbox input[type="checkbox"] {
            margin-right: 10px;
        }

        .alert {
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .payment-container {
                padding: 10px;
            }

            .customer-form,
            .payment-methods {
                padding: 20px;
            }
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

    <form method="POST" action="{{ route('booking.process-payment') }}" id="payment-form">
        @csrf
        <input type="hidden" name="showtime_id" value="{{ $showtime->showtime_id }}">
        <input type="hidden" name="promotion_code" value="{{ $bookingData['promotion_code'] ?? '' }}">

        {{-- Booking Summary --}}
        <div class="booking-summary">
            <h3>Thông tin đặt vé</h3>

            <div class="movie-info">
                <img src="{{ '/storage/'.$showtime->movie->poster_url ?? 'https://via.placeholder.com/120x160/f5f5f5/333?text=Movie+Poster' }}"
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
                        <th>Đã chọn</th>
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

                    <tr style="border-top: 2px solid #dee2e6; background-color: #f8f9fa;">
                        <td colspan="2"><strong>Tạm tính:</strong></td>
                        <td><strong>{{ number_format($bookingData['total_amount']) }}đ</strong></td>
                    </tr>

                    @if(($bookingData['discount_amount'] ?? 0) > 0)
                    <tr style="color: #28a745;">
                        <td colspan="2"><strong><i class="fas fa-tag"></i> Giảm giá:</strong></td>
                        <td><strong>-{{ number_format($bookingData['discount_amount']) }}đ</strong></td>
                    </tr>
                    @endif

                    <tr class="total-row" style="border-top: 2px solid #e61e4d; background-color: #fff5f5;">
                        <td colspan="2"><strong>Thành tiền:</strong></td>
                        <td><strong>{{ number_format($bookingData['final_amount'] ?? $bookingData['total_amount']) }}đ</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Customer Information Form --}}
        <div class="customer-form">
            <h3 class="mb-4">Thông tin người đặt vé</h3>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="customer_name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('customer_name') is-invalid @enderror"
                               id="customer_name" name="customer_name"
                               value="{{ old('customer_name', auth()->user()->full_name ?? '') }}" required>
                        @error('customer_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="customer_phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control @error('customer_phone') is-invalid @enderror"
                               id="customer_phone" name="customer_phone"
                               value="{{ old('customer_phone', auth()->user()->phone ?? '') }}" required>
                        @error('customer_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="customer_email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control @error('customer_email') is-invalid @enderror"
                       id="customer_email" name="customer_email"
                       value="{{ old('customer_email', auth()->user()->email ?? '') }}" required>
                @error('customer_email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="user_notes" class="form-label">Ghi chú (tùy chọn)</label>
                <textarea class="form-control @error('user_notes') is-invalid @enderror"
                          id="user_notes" name="user_notes" rows="3"
                          placeholder="Ghi chú thêm cho đơn đặt vé...">{{ old('user_notes') }}</textarea>
                @error('user_notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>



        {{-- Payment Button --}}
        <button type="submit" class="btn-payment" id="payment-btn">
            <i class="fas fa-lock"></i> Thanh toán {{ number_format($bookingData['final_amount'] ?? $bookingData['total_amount']) }}đ
        </button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Countdown timer
    const countdownElement = document.getElementById('countdown-timer');
    const expiresAt = new Date('{{ $bookingData['expires_at'] ?? now()->addMinutes(15) }}');



    // Payment method selection
    document.querySelectorAll('.payment-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.payment-option').forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
        });
    });

    // Form submission
    document.getElementById('payment-form').addEventListener('submit', function(e) {
        const paymentBtn = document.getElementById('payment-btn');
        paymentBtn.disabled = true;
        paymentBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

        // Re-enable after 10 seconds in case of error
        setTimeout(() => {
            paymentBtn.disabled = false;
            paymentBtn.innerHTML = '<i class="fas fa-lock"></i> Thanh toán {{ number_format($bookingData['final_amount'] ?? $bookingData['total_amount']) }}đ';
        }, 1000);
    });
});
</script>
@endsection
