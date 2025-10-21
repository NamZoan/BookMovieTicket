@extends('client.layouts.app')
@section('title', 'Thanh toán')
@push('styles')
    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Yanone+Kaffeesatz:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #E51C23;
            --surface-dark: #ffffff;
            --text-muted: #A0A0A0;
            --border-color: #333333;
            --accent-gold: #FFD700;
            --success-color: #198754;
        }

        body {
            background-color: var(--background-dark);
            font-family: 'Inter', sans-serif;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Yanone Kaffeesatz', sans-serif;
            color: var(--text-light);
            letter-spacing: 0.5px;
        }

        .payment-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 20px;
        }

        .booking-summary {
            background: var(--surface-dark);
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid var(--border-color);
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
            border-radius: 6px;
        }

        .movie-details h3 {
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table th,
        .summary-table td {
            padding: 10px;
            color: var(--text-light);
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .summary-table th {
            color: var(--text-muted);
            font-weight: 600;
        }

        .total-row {
            font-weight: bold;
            font-size: 1.1em;
            color: var(--primary-color);
        }

        .customer-form {
            background: var(--surface-dark);
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid var(--border-color);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label, label {
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-muted);
        }

        .form-control {
            background-color: var(--background-dark);
            color: var(--text-light);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        .form-control:focus {
            background-color: var(--background-dark);
            color: var(--text-light);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(229, 28, 35, 0.25);
        }

        .payment-methods {
            background: var(--surface-dark);
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid var(--border-color);
        }

        .payment-option {
            border: 2px solid var(--border-color);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 15px;
            background-color: var(--background-dark);
        }

        .payment-option:hover {
            border-color: var(--primary-color);
            transform: translateY(-3px);
        }

        .payment-option.selected {
            border-color: var(--primary-color);
            box-shadow: 0 0 15px rgba(229, 28, 35, 0.3);
        }

        .payment-option i {
            font-size: 2.5rem;
            color: var(--text-muted);
            margin-bottom: 10px;
        }

        .payment-option.selected i {
            color: var(--primary-color);
        }

        .btn-payment {
            background: linear-gradient(45deg, var(--primary-color), #d70442);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 15px 40px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
        }

        .btn-payment:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-payment:not(:disabled):hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(229, 28, 35, 0.4);
        }

        .text-danger {
            color: var(--primary-color) !important;
        }

        .text-muted {
            color: var(--text-muted) !important;
        }
        .text-success {
            color: var(--success-color) !important;
        }

        .alert {
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .alert-danger {
            background-color: rgba(229, 28, 35, 0.1);
            border: 1px solid var(--primary-color);
            color: var(--text-light);
        }

        @media (max-width: 992px) {
            .payment-container {
                padding: 10px;
                display: flex; flex-direction: column;
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
        <h1 class="text-center mb-5 display-4">Hoàn Tất Đặt Vé</h1>

        @if (session('error'))
            <div class="alert alert-danger text-center">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <div class="row gx-4">
            <div class="col-lg-7">
                <form method="POST" action="{{ route('booking.process-payment') }}" id="payment-form">
                    @csrf
                    <input type="hidden" name="showtime_id" value="{{ $showtime->showtime_id }}">
                    <input type="hidden" name="promotion_code" value="{{ $bookingData['promotion_code'] ?? '' }}">

                    {{-- Customer Information Form --}}
                    <div class="customer-form">
                        <h3 class="mb-4">Thông tin người đặt vé</h3>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customer_name" class="form-label">Họ và tên <span
                                            class="text-danger">*</span></label>
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
                                    <label for="customer_phone" class="form-label">Số điện thoại <span
                                            class="text-danger">*</span></label>
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
                            <textarea class="form-control @error('user_notes') is-invalid @enderror" id="user_notes" name="user_notes"
                                rows="3" placeholder="Ghi chú thêm cho đơn đặt vé...">{{ old('user_notes') }}</textarea>
                            @error('user_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Payment Methods --}}
                    <div class="payment-methods">
                        <h3 class="mb-4">Chọn phương thức thanh toán</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="payment-option selected" data-method="VNPAY">
                                    <i class="fas fa-credit-card"></i>
                                    <h6>Thanh toán VNPAY</h6>
                                    <p class="text-muted small">Hỗ trợ thẻ ATM, Visa, Master, QR Pay</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="payment-option" data-method="Cash">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <h6>Thanh toán tại quầy</h6>
                                    <p class="text-muted small">Thanh toán bằng tiền mặt khi đến rạp</p>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="payment_method" id="payment-method-input" value="VNPAY">
                    </div>

                    <button type="submit" class="btn-payment" id="payment-btn">
                        <span id="payment-btn-text">
                            <i class="fas fa-lock"></i> Thanh toán
                            {{ number_format($bookingData['final_amount'] ?? $bookingData['total_amount']) }}đ
                        </span>
                    </button>
                </form>
            </div>

            <div class="col-lg-5">
                {{-- Booking Summary --}}
                <div class="booking-summary">
                    <h3 class="mb-3">Thông tin đặt vé</h3>

                    <div class="movie-info">
                        <img src="{{ '/storage/' . $showtime->movie->poster_url ?? 'https://via.placeholder.com/120x160/1E1E1E/A0A0A0?text=Poster' }}"
                            alt="{{ $showtime->movie->title }}" class="movie-poster">
                        <div class="movie-details">
                            <h3>{{ $showtime->movie->title }}</h3>
                            <p class="text-muted"><strong>Rạp:</strong> {{ $showtime->screen->cinema->name }} - {{ $showtime->screen->name }}</p>
                            <p class="text-muted"><strong>Ngày:</strong> {{ \Carbon\Carbon::parse($showtime->show_date)->format('d/m/Y') }}</p>
                            <p class="text-muted"><strong>Giờ:</strong> {{ \Carbon\Carbon::parse($showtime->show_time)->format('H:i') }}</p>
                        </div>
                    </div>

                    <table class="summary-table">
                        <tbody>
                            @foreach ($selectedSeats as $seat)
                                <tr>
                                    <td>Ghế {{ $seat->row_name }}{{ $seat->seat_number }}</td>
                                    <td class="text-end">{{ number_format($bookingData['seat_prices']['seats'][$seat->seat_id] ?? 0) }}đ</td>
                                </tr>
                            @endforeach

                            @if (!empty($bookingData['food_items']))
                                @foreach ($bookingData['food_items'] as $foodItem)
                                    <tr>
                                        <td>{{ $foodItem['name'] }} x{{ $foodItem['quantity'] }}</td>
                                        <td class="text-end">{{ number_format($foodItem['total_price']) }}đ</td>
                                    </tr>
                                @endforeach
                            @endif

                            <tr style="border-top: 2px solid var(--border-color);">
                                <td><strong>Tạm tính:</strong></td>
                                <td class="text-end"><strong>{{ number_format($bookingData['total_amount']) }}đ</strong></td>
                            </tr>

                            @if (($bookingData['discount_amount'] ?? 0) > 0)
                                <tr style="color: var(--success-color);">
                                    <td><strong><i class="fas fa-tag"></i> Giảm giá:</strong></td>
                                    <td class="text-end"><strong>-{{ number_format($bookingData['discount_amount']) }}đ</strong></td>
                                </tr>
                            @endif

                            <tr class="total-row" style="border-top: 2px solid var(--primary-color); background-color: rgba(229, 28, 35, 0.1);">
                                <td class="h5"><strong>Thành tiền:</strong></td>
                                <td class="text-end h5"><strong>{{ number_format($bookingData['final_amount'] ?? $bookingData['total_amount']) }}đ</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Payment method selection
            document.querySelectorAll('.payment-option').forEach(option => {
                option.addEventListener('click', function() {
                    document.querySelectorAll('.payment-option').forEach(opt => opt.classList.remove('selected'));
                    this.classList.add('selected');
                    const paymentMethod = this.getAttribute('data-method');
                    document.getElementById('payment-method-input').value = paymentMethod;

                    const buttonTextSpan = document.getElementById('payment-btn-text');
                    if (paymentMethod === 'VNPAY') {
                        buttonTextSpan.innerHTML = `<i class="fas fa-lock"></i> Thanh toán {{ number_format($bookingData['final_amount'] ?? $bookingData['total_amount']) }}đ`;
                    } else {
                        buttonTextSpan.innerHTML = `<i class="fas fa-check-circle"></i> Hoàn tất đặt vé`;
                    }
                });
            });

            // Form submission handling
            document.getElementById('payment-form').addEventListener('submit', function(e) {
                const paymentBtn = document.getElementById('payment-btn');
                const originalBtnHTML = document.getElementById('payment-btn-text').innerHTML;

                paymentBtn.disabled = true;
                paymentBtn.querySelector('#payment-btn-text').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

                // Re-enable after 10 seconds in case of error
                setTimeout(() => {
                    if (paymentBtn.disabled) { // Only re-enable if it's still disabled
                        paymentBtn.disabled = false;
                        paymentBtn.querySelector('#payment-btn-text').innerHTML = originalBtnHTML;
                    }
                }, 10000); // Tăng thời gian chờ lên 10s
            });
        });
    </script>
@endsection
