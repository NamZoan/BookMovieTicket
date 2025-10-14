@extends('client.layouts.app')

@section('title', 'Đặt vé xem phim')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Yanone+Kaffeesatz:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #e61e4d;
            --primary-dark: #d70442;
            --secondary: #101011;
            --light-bg: #f8f9fa;
            --dark-bg: #121212;
            --text-light: #f8f9fa;
            --text-dark: #212529;
            --border-light: #dee2e6;
            --success: #198754;
            --warning: #ffc107;
            --danger: #dc3545;
            --info: #0dcaf0;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.15);
            --border-radius: 12px;
            --transition: all 0.3s ease;
        }

        .dark-theme {
            --bg-color: var(--dark-bg);
            --text-color: var(--text-light);
            --card-bg: #1e1e1e;
            --border-color: #2d2d2d;
        }

        .light-theme {
            --bg-color: var(--light-bg);
            --text-color: var(--text-dark);
            --card-bg: #ffffff;
            --border-color: var(--border-light);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: var(--transition);
        }

        /* Movie Info Card */
        .movie-info-card {
            background: var(--card-bg);
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: var(--shadow);
        }

        .movie-poster {
            width: 100%;
            border-radius: 8px;
            object-fit: cover;
        }

        .movie-details {
            line-height: 2;
        }

        /* Progress Steps */
        .progress-container {
            margin: 30px 0;
        }

        .progress-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 30px;
        }

        .progress-steps::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 0;
            width: 100%;
            height: 4px;
            background-color: #dee2e6;
            z-index: 1;
        }

        .progress-bar {
            position: absolute;
            top: 15px;
            left: 0;
            height: 4px;
            background-color: var(--primary);
            z-index: 2;
            transition: width 0.5s ease;
        }

        .step {
            position: relative;
            z-index: 3;
            text-align: center;
            width: 100%;
        }

        .step::before {
            counter-increment: step;
            content: counter(step);
            display: flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            background-color: #dee2e6;
            color: #495057;
            border-radius: 50%;
            margin: 0 auto 10px;
            font-weight: 600;
        }

        .step.active::before {
            background-color: var(--primary);
            color: white;
        }

        .step.completed::before {
            background-color: var(--success);
            color: white;
            content: '✓';
        }

        .step-label {
            font-size: 0.85rem;
            font-weight: 500;
        }

        /* Booking Form */
        .booking-form {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 20px;
        }

        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .step-title {
            font-size: 1.5rem;
            color: var(--primary);
            font-weight: 600;
        }

        /* Seat Selection */
        .seat-selection-container {
            display: grid;
            gap: 20px;
        }

        @media (min-width: 992px) {
            .seat-selection-container {
                grid-template-columns: 2fr 1fr;
            }
        }

        .seat-map {
            background-color: var(--bg-color);
            padding: 20px;
            border-radius: var(--border-radius);
            text-align: center;
        }

        .screen-indicator {
            background: linear-gradient(to bottom, #c0c0c0, #e0e0e0);
            color: #333;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 30px;
            font-weight: 600;
            position: relative;
        }

        .screen-indicator::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(to right, #ff0000, #ff5a00, #ff9a00, #ffce00, #ff9a00, #ff5a00, #ff0000);
        }

        .seats-container {
            margin-bottom: 20px;
        }

        .seat-row {
            display: flex;
            justify-content: center;
            margin-bottom: 8px;
            gap: 4px;
        }

        .seat {
            width: 35px;
            height: 35px;
            border-radius: 8px 8px 3px 3px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        }

        .seat.normal {
            background: linear-gradient(to bottom, #5C9CE5, #4A89DC);
            color: white;
            border: 1px solid #4A89DC;
        }

        .seat.vip {
            background: linear-gradient(to bottom, #FFD700, #FFC200);
            color: #000;
            border: 1px solid #FFC200;
        }

        .seat.couple {
            background: linear-gradient(to bottom, #FF7BAC, #FF5B93);
            color: white;
            border: 1px solid #FF5B93;
            width: 74px;
        }

        .seat.selected {
            background: linear-gradient(to bottom, #2ECC71, #27AE60) !important;
            color: white !important;
            border: 1px solid #27AE60 !important;
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(46, 204, 113, 0.3);
        }

        .seat.booked,
        .seat.held {
            background: linear-gradient(to bottom, #E74C3C, #C0392B);
            color: white;
            cursor: not-allowed;
            border: 1px solid #C0392B;
            opacity: 0.8;
        }

        .seat:not(.booked):not(.held):hover {
            transform: translateY(-2px);
        }

        /* Seat Legend */
        .seat-legend {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 20px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
        }

        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Selected Seats Panel */
        .selected-seats-panel {
            background-color: var(--bg-color);
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: var(--shadow);
            position: sticky;
            top: 20px;
        }

        .panel-title {
            font-size: 1.2rem;
            margin-bottom: 15px;
            font-weight: 600;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .selected-seats-list {
            max-height: 300px;
            overflow-y: auto;
            margin-bottom: 20px;
        }

        .seat-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background: var(--card-bg);
            border-radius: 6px;
            margin-bottom: 8px;
        }

        .seat-remove-btn {
            background: none;
            border: none;
            color: var(--danger);
            cursor: pointer;
            font-size: 1.2rem;
            padding: 0;
        }

        .summary-section {
            border-top: 2px solid var(--border-color);
            padding-top: 15px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .summary-total {
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--primary);
        }

        /* Food Selection */
        .food-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
            margin: 10px 0;
        }

        .food-card {
            background: var(--card-bg);
            border-radius: var(--border-radius);
            padding: 15px;
            text-align: center;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .food-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .food-image {
            width: 100%;
            height: 120px;
            object-fit: contain;
            margin-bottom: 10px;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 15px;
        }

        .qty-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: none;
            background: var(--primary);
            color: white;
            cursor: pointer;
            font-weight: bold;
        }

        .qty-input {
            width: 50px;
            text-align: center;
            padding: 5px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            background: var(--card-bg);
            color: var(--text-color);
        }

        /* Payment Methods */
        .payment-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }

        .payment-option {
            padding: 20px;
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
        }

        .payment-option.selected {
            border-color: var(--primary);
            background: rgba(230, 30, 77, 0.05);
        }

        .payment-option i {
            font-size: 2rem;
            margin-bottom: 10px;
            color: #6c757d;
        }

        .payment-option.selected i {
            color: var(--primary);
        }

        /* Navigation Buttons */
        .form-navigation {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-top: 30px;
        }

        .nav-btn {
            padding: 12px 30px;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
            font-size: 1rem;
        }

        .btn-prev {
            background: #6c757d;
            color: white;
        }

        .btn-prev:hover {
            background: #5a6268;
        }

        .btn-next,
        .btn-submit {
            background: var(--primary);
            color: white;
        }

        .btn-next:hover:not(:disabled),
        .btn-submit:hover:not(:disabled) {
            background: var(--primary-dark);
        }

        .btn-next:disabled,
        .btn-submit:disabled {
            background: #6c757d;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .btn-cancel {
            background: var(--danger);
            color: white;
        }

        .btn-cancel:hover {
            background: #bb2d3b;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .seat {
                width: 30px;
                height: 30px;
                font-size: 0.7rem;
            }

            .seat.couple {
                width: 64px;
            }

            .food-grid {
                grid-template-columns: 1fr;
            }

            .payment-methods {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">
        {{-- Movie Information --}}
        <div class="movie-info-card">
            <div class="row">
                <div class="col-md-3">
                    <img src="{{ '/storage/' . optional($showtime->movie)->poster_url ?? 'https://bizmac.com/Images/Editor/images/404-not-found-la-gi.jpg' }}"
                        alt="{{ optional($showtime->movie)->title ?? 'Không có tên phim' }}" class="movie-poster">
                </div>
                <div class="col-md-9">
                    <h2 class="movie-title mb-3">{{ $showtime->movie->title }}</h2>
                    <div class="movie-details">
                        <p><strong>Rạp:</strong> {{ optional($showtime->screen->cinema)->name ?? 'Không có thông tin' }} -
                            {{ optional($showtime->screen)->name ?? 'Không có thông tin' }}
                        </p>
                        <p><strong>Ngày:</strong> {{ \Carbon\Carbon::parse($showtime->show_date)->format('d/m/Y') }}</p>
                        <p><strong>Giờ:</strong> {{ \Carbon\Carbon::parse($showtime->show_time)->format('H:i') }}</p>
                        <p><strong>Thời lượng:</strong> {{ $showtime->movie->duration }} phút</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="progress-container">
            <div class="progress-steps" style="counter-reset: step;">
                <div class="step active" data-step="1">
                    <span class="step-label">Chọn ghế</span>
                </div>
                <div class="step" data-step="2">
                    <span class="step-label">Đồ ăn</span>
                </div>
                <div class="step" data-step="3">
                    <span class="step-label">Chi tiết</span>
                </div>
                <div class="progress-bar" id="progress-bar"></div>
            </div>
        </div>

        {{-- Seat Hold Timer --}}
        <div id="seat-hold-timer" class="alert alert-info text-center" style="display: none;">
            <i class="fas fa-clock"></i>
            <strong>Thời gian giữ ghế:</strong> <span id="hold-countdown">3:00</span>
            <small class="d-block text-muted">Ghế sẽ được giải phóng sau khi hết thời gian</small>
        </div>
        @csrf
        <input type="hidden" name="showtime_id" value="{{ $showtime->showtime_id }}">
        <input type="hidden" name="selected_seats" id="selected-seats-input">
        <input type="hidden" name="payment_method" id="payment-method-input" value="Cash">

        <div class="booking-form ">
            {{-- Step 1: Seat Selection --}}
            <div class="form-step active" id="step-1">
                <h2 class="step-title">Chọn Ghế Ngồi</h2>

                <div class="seat-selection-container">
                    <div class="seat-map-container">
                        <div class="seat-map">
                            <div class="screen-indicator">Màn Hình</div>

                            <div class="seats-container">
                                @foreach ($seatMap as $rowName => $seats)
                                    <div class="seat-row" data-row="{{ $rowName }}">
                                        @foreach ($seats as $seat)
                                            <div class="seat {{ strtolower($seat['seat_type']) }} {{ $seat['status'] }}"
                                                data-seat-id="{{ $seat['seat_id'] }}" data-row="{{ $seat['row'] }}"
                                                data-number="{{ $seat['number'] }}" data-type="{{ $seat['seat_type'] }}">
                                                {{ $seat['row'] }}{{ $seat['number'] }}
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>

                            <div class="seat-legend">
                                <div class="legend-item">
                                    <div class="legend-color"
                                        style="background: linear-gradient(to bottom, #5C9CE5, #4A89DC);"></div>
                                    <span>Thường ({{ number_format($pricing['Normal']) }}đ)</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color"
                                        style="background: linear-gradient(to bottom, #FFD700, #FFC200);"></div>
                                    <span>VIP ({{ number_format($pricing['VIP']) }}đ)</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color"
                                        style="background: linear-gradient(to bottom, #FF7BAC, #FF5B93);"></div>
                                    <span>Đôi ({{ number_format($pricing['Couple']) }}đ)</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color"
                                        style="background: linear-gradient(to bottom, #2ECC71, #27AE60);"></div>
                                    <span>Đã chọn</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-color"
                                        style="background: linear-gradient(to bottom, #E74C3C, #C0392B);"></div>
                                    <span>Đã đặt</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="selected-seats-panel">
                        <h3 class="panel-title">
                            <i class="fas fa-ticket-alt"></i>
                            Ghế đã chọn
                        </h3>

                        <div class="selected-seats-list" id="selected-seats-list">
                            <p class="text-center text-muted">Chưa có ghế nào được chọn</p>
                        </div>

                        <div class="summary-section">
                            <div class="summary-row">
                                <span>Số ghế:</span>
                                <span id="seats-count">0</span>
                            </div>
                            <div class="summary-row summary-total">
                                <span>Tổng tiền:</span>
                                <span id="seats-total">0đ</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-navigation">
                    <button type="button" class="nav-btn btn-prev" disabled>Trở về</button>
                    <button type="button" class="nav-btn btn-next" id="step-1-next" disabled>Tiếp tục</button>
                </div>
            </div>

            {{-- Step 2: Food Selection --}}
            <div class="form-step" id="step-2">
                <h2 class="step-title">Đồ Ăn & Thức Uống</h2>

                @if ($foodItems->isNotEmpty())
                    @foreach ($foodItems as $category => $items)
                        <h4 class="mt-4">{{ $category }}</h4>
                        <div class="food-grid">
                            @foreach ($items as $item)
                                <div class="food-card">
                                    <img src="{{ $item->image_url ?? 'https://via.placeholder.com/150' }}"
                                        alt="{{ $item->name }}" class="food-image">
                                    <h5>{{ $item->name }}</h5>
                                    <p class="text-primary fw-bold">{{ number_format($item->price) }}đ</p>

                                    <div class="quantity-controls">
                                        <button type="button" class="qty-btn minus"
                                            data-id="{{ $item->item_id }}">-</button>
                                        <input type="number" class="qty-input" value="0" min="0"
                                            data-id="{{ $item->item_id }}" data-name="{{ $item->name }}"
                                            data-price="{{ $item->price }}" name="food_items[{{ $item->item_id }}]">
                                        <button type="button" class="qty-btn plus"
                                            data-id="{{ $item->item_id }}">+</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach

                    <div class="mt-4 p-3" style="background: var(--bg-color); border-radius: var(--border-radius);">
                        <h5>Tổng đồ ăn: <span id="food-total" class="text-primary">0đ</span></h5>
                    </div>
                @else
                    <p class="text-center text-muted">Hiện không có đồ ăn thức uống</p>
                @endif

                <div class="form-navigation">
                    <button type="button" class="nav-btn btn-prev">Trở về</button>
                    <button type="button" class="nav-btn btn-next">Tiếp tục</button>
                </div>
            </div>

            {{-- Step 3: Promotion & Summary --}}
            <div class="form-step" id="step-3">
                <h2 class="step-title">Mã Giảm Giá & Tóm Tắt</h2>

                {{-- Promotion Section --}}
                <div class="promotion-section mb-4">
                    <h4 class="mb-3">Mã Giảm Giá</h4>

                    {{-- Auto-applied promotions --}}
                    <div id="auto-promotions" class="mb-3" style="display: none;">
                        <div class="alert alert-success">
                            <i class="fas fa-gift"></i>
                            <span id="auto-promotion-text"></span>
                        </div>
                    </div>

                    {{-- Available promotions --}}
                    <div class="available-promotions mb-3">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="load-promotions-btn">
                            <i class="fas fa-tags"></i> Xem mã giảm giá khả dụng
                        </button>
                        <div id="promotions-list" class="mt-3" style="display: none;">
                            <!-- Promotions will be loaded here -->
                        </div>
                    </div>

                    {{-- Promotion input --}}
                    <div class="promotion-input-group">
                        <div class="input-group">
                            <input type="text" class="form-control" id="promotion-code-input"
                                placeholder="Nhập mã giảm giá" maxlength="20">
                            <button type="button" class="btn btn-primary" id="apply-promotion-btn">
                                <i class="fas fa-check"></i> Áp dụng
                            </button>
                            <button type="button" class="btn btn-secondary" id="remove-promotion-btn"
                                style="display: none;">
                                <i class="fas fa-times"></i> Bỏ áp dụng
                            </button>
                        </div>
                        <div id="promotion-message" class="mt-2"></div>
                    </div>
                </div>

                {{-- Order Summary --}}
                <div class="order-summary p-4" style="background: var(--bg-color); border-radius: var(--border-radius);">
                    <h4 class="mb-3">Tóm tắt đơn hàng</h4>

                    <div class="summary-row">
                        <span>Tiền vé:</span>
                        <span id="summary-ticket-price">0đ</span>
                    </div>
                    <div class="summary-row">
                        <span>Đồ ăn:</span>
                        <span id="summary-food-price">0đ</span>
                    </div>

                    <div class="summary-row" id="subtotal-row"
                        style="border-top: 1px solid var(--border-color); padding-top: 10px; margin-top: 10px;">
                        <span><strong>Tạm tính:</strong></span>
                        <span id="summary-subtotal">0đ</span>
                    </div>

                    {{-- Promotion discount row --}}
                    <div class="summary-row" id="discount-row" style="display: none; color: var(--success);">
                        <span><i class="fas fa-tag"></i> Giảm giá:</span>
                        <span id="summary-discount">-0đ</span>
                    </div>

                    <div class="summary-row summary-total"
                        style="border-top: 2px solid var(--primary); padding-top: 15px; margin-top: 15px;">
                        <span><strong>Thành tiền:</strong></span>
                        <span id="summary-total-price">0đ</span>
                    </div>

                    {{-- Hidden inputs --}}
                    <input type="hidden" name="promotion_code" id="promotion-code-hidden">
                    <input type="hidden" name="order_summary" id="order-summary-input">
                    <input type="hidden" name="order_total" id="order-total-input">
                </div>

                {{-- Order Details --}}
                <div class="order-details mt-4 p-4"
                    style="background: var(--bg-color); border-radius: var(--border-radius);">
                    <h5 class="mb-3">Chi tiết đơn hàng</h5>

                    <h6>Ghế đã chọn:</h6>
                    <div id="summary-seats-list" style="max-height:180px; overflow:auto; margin-bottom:15px">
                        <p class="text-muted">Chưa có ghế nào được chọn</p>
                    </div>

                    <h6>Đồ ăn đã chọn:</h6>
                    <div id="summary-food-list" style="max-height:180px; overflow:auto; margin-bottom:23px">
                        <p class="text-muted">Chưa có đồ ăn nào được chọn</p>
                    </div>
                </div>

                <div class="form-navigation">
                    <button type="button" class="nav-btn btn-prev">Trở về</button>
                    <button type="button" class="nav-btn btn-cancel"
                        onclick="window.location.href='{{ route('home') }}'">Hủy</button>
                    <button type="button" class="nav-btn btn-submit" id="submit-btn" onclick="submitBookingForm()">
                        <span class="button-text">Tiếp tục thanh toán</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </div>
        </div>

        @if ($errors->has('showtime_id'))
            <div class="error">{{ $errors->first('showtime_id') }}</div>
        @endif
        @if ($errors->has('selected_seats'))
            <div class="error">{{ $errors->first('selected_seats') }}</div>
        @endif
        @if ($errors->has('food_items'))
            <div class="error">{{ $errors->first('food_items') }}</div>
        @endif
        @if ($errors->has('payment_method'))
            <div class="error">{{ $errors->first('payment_method') }}</div>
        @endif

        </form>
    </div>
@endsection

@push('scripts')
    <!-- jQuery 3.x -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(function() {
            // Ensure all jQuery AJAX requests include the CSRF token header
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Also expose CSRF token as a JS constant (fallback and convenience)
            const CSRF_TOKEN = '{{ csrf_token() }}';
            const pricing = @json($pricing);
            const bookingData = {
                seats: [],
                food: {},
                foodTotal: 0,
                seatTotal: 0,
                promotion: {
                    code: null,
                    discount: 0,
                    data: null
                }
            };
            const showtimeId = {{ $showtime->showtime_id }};

            // Progress management
            const $steps = $('.step');
            const $formSteps = $('.form-step');
            const $progressBar = $('#progress-bar');

            function updateProgress() {
                const $activeStep = $('.step.active');
                const stepNum = parseInt($activeStep.data('step'));
                const percentage = ((stepNum - 1) / ($steps.length - 1)) * 100;
                $progressBar.css('width', percentage + '%');
            }

            // Navigation
            $('.btn-next').on('click', function() {
                const $currentStep = $('.form-step.active');
                const stepId = parseInt($currentStep.attr('id').split('-')[1]);

                if (stepId < 3) {
                    goToStep(stepId + 1);
                }
            });

            $('.btn-prev').on('click', function() {
                const $currentStep = $('.form-step.active');
                const stepId = parseInt($currentStep.attr('id').split('-')[1]);

                if (stepId > 1) {
                    goToStep(stepId - 1);
                }
            });

            function goToStep(stepNum) {
                $formSteps.removeClass('active');
                $('#step-' + stepNum).addClass('active');

                $steps.each(function() {
                    const num = parseInt($(this).data('step'));
                    $(this).removeClass('active completed');
                    if (num < stepNum) $(this).addClass('completed');
                    if (num === stepNum) $(this).addClass('active');
                });

                updateProgress();
            }

            // Seat selection
            $('.seat:not(.booked):not(.held)').on('click', function() {
                const seatId = $(this).data('seatId');
                const seatType = $(this).data('type');
                const seatLabel = $(this).data('row') + $(this).data('number');

                if ($(this).hasClass('selected')) {
                    // User is DESELECTING a seat
                    $(this).removeClass('selected');
                    bookingData.seats = bookingData.seats.filter(s => s.id !== seatId);
                    // Broadcast release
                    $.post('{{ route('booking.release-seats') }}', {
                        showtime_id: showtimeId,
                        seat_ids: [seatId],
                        _token: CSRF_TOKEN
                    }).done(function(response) {
                        if (response.success) {
                            console.log('Seat released successfully');
                        }
                    }).fail(function() {
                        console.error('Failed to release seat');
                    });
                } else {
                    // User is SELECTING a seat
                    $(this).addClass('selected');
                    bookingData.seats.push({
                        id: seatId,
                        label: seatLabel,
                        type: seatType,
                        price: pricing[seatType]
                    });
                    // Broadcast hold
                    $.post('{{ route('booking.hold-seats') }}', {
                        showtime_id: showtimeId,
                        seat_ids: [seatId],
                        _token: CSRF_TOKEN
                    }).done(function(response) {
                        if (response.success) {
                            console.log('Seat held successfully');
                        } else {
                            // If seat couldn't be held, remove it from selection
                            $(`.seat[data-seat-id="${seatId}"]`).removeClass('selected');
                            bookingData.seats = bookingData.seats.filter(s => s.id !== seatId);
                            updateSeatsDisplay();
                            alert('Ghế này đã được người khác chọn. Vui lòng chọn ghế khác.');
                        }
                    }).fail(function() {
                        console.error('Failed to hold seat');
                        // Remove from selection on failure
                        $(`.seat[data-seat-id="${seatId}"]`).removeClass('selected');
                        bookingData.seats = bookingData.seats.filter(s => s.id !== seatId);
                        updateSeatsDisplay();
                    });
                }

                updateSeatsDisplay();
            });


            // Real-time event handling
            if (window.Echo) {
                const channel = window.Echo.channel(`showtime.${showtimeId}`);

                // When another user selects seats
                channel.listen('.SeatSelected', (e) => {
                    console.log('SeatSelected event received:', e);
                    if (Array.isArray(e.seatIds)) {
                        e.seatIds.forEach(seatId => {
                            const seatElement = $(`.seat[data-seat-id="${seatId}"]`);
                            // Only mark as held if not selected by current user
                            if (seatElement.length && !seatElement.hasClass('selected')) {
                                seatElement.removeClass('normal vip couple').addClass('held');
                                seatElement.css('pointer-events', 'none');
                                console.log(`Seat ${seatId} marked as held by another user`);
                            }
                        });
                    }
                });

                // When another user releases seats
                channel.listen('.SeatReleased', (e) => {
                    console.log('SeatReleased event received:', e);
                    if (Array.isArray(e.seatIds)) {
                        e.seatIds.forEach(seatId => {
                            const seatElement = $(`.seat[data-seat-id="${seatId}"]`);
                            // Only remove held status if not selected by current user
                            if (seatElement.length && !seatElement.hasClass('selected')) {
                                // Restore original seat type class
                                const seatType = seatElement.data('type').toLowerCase();
                                seatElement.removeClass('held').addClass(seatType);
                                seatElement.css('pointer-events', '');
                                console.log(`Seat ${seatId} released by another user`);
                            }
                        });
                    }
                });

            } else {
                console.error('Echo is not initialized - real-time updates will not work');
            }

            // Di chuyển xử lý sự kiện ghế ra ngoài hàm cập nhật để tránh sự kiện bị đăng ký nhiều lần
            $('#selected-seats-list').on('click', '.seat-remove-btn', function() {
                const seatId = $(this).data('seatId');
                const $seatElement = $(`.seat[data-seat-id="${seatId}"]`);
                if ($seatElement.length) $seatElement.click();
            });

            function updateSeatsDisplay() {
                const $list = $('#selected-seats-list');
                const $count = $('#seats-count');
                const $total = $('#seats-total');

                if (bookingData.seats.length === 0) {
                    $list.html('<p class="text-center text-muted">Chưa có ghế nào được chọn</p>');
                    $count.text('0');
                    $total.text('0đ');
                    bookingData.seatTotal = 0;
                    $('#step-1-next').prop('disabled', true);

                    // Stop countdown timer when no seats are selected
                    stopHoldCountdown();

                    updateOrderSummary();
                    return;
                }

                let html = '';
                bookingData.seatTotal = 0;

                bookingData.seats.forEach(function(seat) {
                    const price = parseFloat(seat.price);
                    bookingData.seatTotal += price;
                    html += `
                                <div class="seat-item">
                                    <div>
                                        <strong>${seat.label}</strong>
                                        <small class="d-block text-muted">${seat.type} - ${formatCurrency(seat.price)}</small>
                                    </div>
                                    <button type="button" class="seat-remove-btn" data-seat-id="${seat.id}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            `;
                });

                $list.html(html);
                $count.text(bookingData.seats.length);
                $total.text(formatCurrency(bookingData.seatTotal));
                $('#step-1-next').prop('disabled', false);

                // Start countdown timer when seats are selected
                startHoldCountdown();

                updateOrderSummary();
            }


            // Food selection
            $('.qty-btn').on('click', function() {
                const itemId = $(this).data('id');
                const $input = $(`.qty-input[data-id="${itemId}"]`);
                let value = parseInt($input.val()) || 0;

                if ($(this).hasClass('plus')) {
                    value++;
                } else if ($(this).hasClass('minus') && value > 0) {
                    value--;
                }

                $input.val(value);
                updateFoodData(itemId, value, $input);
            });

            $('.qty-input').on('change', function() {
                let value = parseInt($(this).val()) || 0;
                if (value < 0) value = 0;
                $(this).val(value);
                updateFoodData($(this).data('id'), value, $(this));
            });

            function updateFoodData(itemId, quantity, $input) {
                const name = $input.data('name');
                const price = parseFloat($input.data('price'));

                if (quantity > 0) {
                    bookingData.food[itemId] = {
                        name: name,
                        price: price,
                        quantity: quantity
                    };
                } else {
                    delete bookingData.food[itemId];
                }

                updateFoodTotal();
            }

            function updateFoodTotal() {
                bookingData.foodTotal = 0;

                $.each(bookingData.food, function() {
                    bookingData.foodTotal += this.price * this.quantity;
                });

                $('#food-total').text(formatCurrency(bookingData.foodTotal));
                updateOrderSummary();
            }

            // Payment method
            $('.payment-option').on('click', function() {
                $('.payment-option').removeClass('selected');
                $(this).addClass('selected');
                $('#payment-method-input').val($(this).data('method'));
            });

            // Update order summary
            function updateOrderSummary() {
                const subtotal = bookingData.seatTotal + bookingData.foodTotal;
                const discount = bookingData.promotion.discount;
                const total = subtotal - discount;

                $('#summary-ticket-price').text(formatCurrency(bookingData.seatTotal));
                $('#summary-food-price').text(formatCurrency(bookingData.foodTotal));
                $('#summary-subtotal').text(formatCurrency(subtotal));
                $('#summary-total-price').text(formatCurrency(total));

                // Show/hide discount row
                if (discount > 0) {
                    $('#discount-row').show();
                    $('#summary-discount').text('-' + formatCurrency(discount));
                } else {
                    $('#discount-row').hide();
                }

                // Render detailed seats list
                const $seatsList = $('#summary-seats-list');
                if (bookingData.seats.length === 0) {
                    $seatsList.html('<p class="text-muted">Chưa có ghế nào được chọn</p>');
                } else {
                    let seatsHtml = '';
                    bookingData.seats.forEach(s => {
                        seatsHtml +=
                            `<div style="display:flex;justify-content:space-between;padding:6px 8px;border-bottom:1px solid rgba(0,0,0,0.04)"><div><strong>${s.label}</strong><div class="text-muted" style="font-size:0.85rem">${s.type}</div></div><div>${formatCurrency(s.price)}</div></div>`;
                    });
                    $seatsList.html(seatsHtml);
                }

                // Render detailed food list
                const $foodList = $('#summary-food-list');
                const foodItems = Object.values(bookingData.food || {});
                if (foodItems.length === 0) {
                    $foodList.html('<p class="text-muted">Chưa có đồ ăn nào được chọn</p>');
                } else {
                    let foodHtml = '';
                    foodItems.forEach(fi => {
                        foodHtml +=
                            `<div style="display:flex;justify-content:space-between;padding:6px 8px;border-bottom:1px solid rgba(0,0,0,0.04)"><div><strong>${fi.name}</strong><div class="text-muted" style="font-size:0.85rem">Số lượng: ${fi.quantity}</div></div><div>${formatCurrency(fi.price * fi.quantity)}</div></div>`;
                    });
                    $foodList.html(foodHtml);
                }

                // Fill hidden inputs for server
                const orderSummary = {
                    seats: bookingData.seats.map(s => ({
                        id: s.id,
                        label: s.label,
                        type: s.type,
                        price: s.price
                    })),
                    food: Object.keys(bookingData.food).map(k => ({
                        id: k,
                        name: bookingData.food[k].name,
                        price: bookingData.food[k].price,
                        quantity: bookingData.food[k].quantity
                    })),
                    promotion: bookingData.promotion.data,
                    totals: {
                        seats: bookingData.seatTotal,
                        food: bookingData.foodTotal,
                        subtotal: subtotal,
                        discount: discount,
                        grand_total: total
                    }
                };

                $('#order-summary-input').val(JSON.stringify(orderSummary));
                $('#order-total-input').val(total);
                $('#promotion-code-hidden').val(bookingData.promotion.code);
            }

            // Format currency
            function formatCurrency(amount) {
                return new Intl.NumberFormat('vi-VN', {
                    style: 'currency',
                    currency: 'VND'
                }).format(amount);
            }

            // Form submission
            $('#booking-form').on('submit', function(e) {
                e.preventDefault();

                if (bookingData.seats.length === 0) {
                    alert('Vui lòng chọn ít nhất một ghế!');
                    return;
                }

                const $submitBtn = $('#submit-btn');
                const originalText = $submitBtn.text();
                $submitBtn.text('Đang xử lý...');
                $submitBtn.prop('disabled', true);

                // Prepare seat IDs
                const seatIds = bookingData.seats.map(s => s.id);
                $('#selected-seats-input').val(JSON.stringify(seatIds));

                // Make sure order summary is current and set hidden inputs again
                updateOrderSummary();

                // Submit form
                this.submit();
            });

            // Promotion functionality
            $('#load-promotions-btn').on('click', function() {
                const totalAmount = bookingData.seatTotal + bookingData.foodTotal;

                $.get('{{ route('booking.available-promotions') }}', {
                    total_amount: totalAmount
                }).done(function(response) {
                    if (response.success) {
                        displayAvailablePromotions(response.data);
                        $('#promotions-list').show();
                    }
                });
            });

            $('#apply-promotion-btn').on('click', function() {
                const promotionCode = $('#promotion-code-input').val().trim();
                if (!promotionCode) {
                    showPromotionMessage('Vui lòng nhập mã giảm giá', 'error');
                    return;
                }

                const totalAmount = bookingData.seatTotal + bookingData.foodTotal;

                $.post('{{ route('booking.validate-promotion') }}', {
                    promotion_code: promotionCode,
                    total_amount: totalAmount,
                    _token: CSRF_TOKEN
                }).done(function(response) {
                    if (response.success) {
                        applyPromotion(response);
                        showPromotionMessage(response.message, 'success');
                        $('#promotion-code-input').val('');
                        $('#remove-promotion-btn').show();
                    } else {
                        showPromotionMessage(response.message, 'error');
                    }
                }).fail(function() {
                    showPromotionMessage('Có lỗi xảy ra khi kiểm tra mã giảm giá', 'error');
                });
            });

            $('#remove-promotion-btn').on('click', function() {
                removePromotion();
                showPromotionMessage('Đã bỏ áp dụng mã giảm giá', 'info');
            });

            // Function to display available promotions
            window.displayAvailablePromotions = function(promotions) {
                let html = '<div class="row">';

                promotions.forEach(function(promo) {
                    const isAutoApplicable = promo.is_auto_applicable;
                    const discountText = promo.discount_type === 'Percentage' ?
                        `${promo.discount_value}%` :
                        formatCurrency(promo.discount_value);

                    html += `
                        <div class="col-md-6 mb-3">
                            <div class="promotion-card p-3 border rounded ${isAutoApplicable ? 'border-success bg-light' : ''}">
                                <h6 class="mb-2">
                                    ${promo.name} - ${promo.code}
                                    ${isAutoApplicable ? '<span class="badge bg-light ms-2">Tự động</span>' : ''}
                                </h6>
                                <p class="text-muted mb-2">${promo.description}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-success fw-bold">-${discountText}</span>
                                    <button type="button" class="btn btn-sm ${isAutoApplicable ? 'btn-success' : 'btn-outline-primary'}"
                                            onclick="applyPromotionCode('${promo.code}')">
                                        ${isAutoApplicable ? 'Đã áp dụng' : 'Áp dụng'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });

                html += '</div>';
                $('#promotions-list').html(html);
            };

            // Function to apply promotion code
            window.applyPromotionCode = function(code) {
                $('#promotion-code-input').val(code);
                $('#apply-promotion-btn').click();
            };

            function applyPromotion(response) {
                bookingData.promotion.code = response.promotion.code;
                bookingData.promotion.discount = response.discount_amount;
                bookingData.promotion.data = response.promotion;

                // Auto-apply WELCOME50K if available
                if (response.promotion.code === 'WELCOME50K') {
                    $('#auto-promotions').show();
                    $('#auto-promotion-text').text(response.message);
                }

                updateOrderSummary();
            }

            function removePromotion() {
                bookingData.promotion.code = null;
                bookingData.promotion.discount = 0;
                bookingData.promotion.data = null;

                $('#remove-promotion-btn').hide();
                $('#auto-promotions').hide();

                updateOrderSummary();
            }

            function showPromotionMessage(message, type) {
                const alertClass = type === 'error' ? 'alert-danger' :
                    type === 'success' ? 'alert-success' : 'alert-info';

                $('#promotion-message').html(`
                    <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `);

                // Auto-hide after 5 seconds
                setTimeout(function() {
                    $('#promotion-message .alert').fadeOut();
                }, 5000);
            }

            // Check for auto-applicable promotions when moving to step 3
            $('.btn-next').on('click', function() {
                const $currentStep = $('.form-step.active');
                const stepId = parseInt($currentStep.attr('id').split('-')[1]);

                if (stepId === 2) { // Moving from step 2 to step 3
                    const totalAmount = bookingData.seatTotal + bookingData.foodTotal;

                    // Check for WELCOME50K
                    $.post('{{ route('booking.validate-promotion') }}', {
                        promotion_code: 'WELCOME50K',
                        total_amount: totalAmount,
                        _token: CSRF_TOKEN
                    }).done(function(response) {
                        if (response.success && !bookingData.promotion.code) {
                            applyPromotion(response);
                            showPromotionMessage(response.message, 'success');
                        }
                    });
                }
            });

            // Initialize
            updateProgress();

            // Seat hold countdown timer
            let holdStartTime = null;
            let holdCountdownInterval = null;

            function startHoldCountdown() {
                if (bookingData.seats.length > 0 && !holdStartTime) {
                    holdStartTime = new Date();
                    $('#seat-hold-timer').show();

                    holdCountdownInterval = setInterval(function() {
                        const now = new Date();
                        const elapsed = now - holdStartTime;
                        const remaining = (3 * 60 * 1000) - elapsed; // 15 minutes in milliseconds

                        if (remaining <= 0) {
                            clearInterval(holdCountdownInterval);
                            $('#hold-countdown').text('00:00');
                            return;
                        }

                        const minutes = Math.floor(remaining / 60000);
                        const seconds = Math.floor((remaining % 60000) / 1000);

                        $('#hold-countdown').text(
                            (minutes < 10 ? '0' : '') + minutes + ':' +
                            (seconds < 10 ? '0' : '') + seconds
                        );

                        // Change color when time is running low
                        if (remaining < 2 * 60 * 1000) { // Less than 2 minutes
                            $('#seat-hold-timer').removeClass('alert-info').addClass('alert-warning');
                        }
                        if (remaining < 1 * 60 * 1000) { // Less than 1 minute
                            $('#seat-hold-timer').removeClass('alert-warning').addClass('alert-danger');
                        }
                    }, 1000);
                }
            }

            function stopHoldCountdown() {
                if (holdCountdownInterval) {
                    clearInterval(holdCountdownInterval);
                    holdCountdownInterval = null;
                }
                holdStartTime = null;
                $('#seat-hold-timer').hide().removeClass('alert-warning alert-danger').addClass('alert-info');
            }

            // Handle page unload - release all held seats
            let seatsReleased = false;

            function releaseAllSeats() {
                if (!seatsReleased && bookingData.seats.length > 0) {
                    seatsReleased = true;
                    const seatIds = bookingData.seats.map(s => s.id);

                    // Use sendBeacon for reliable delivery on page unload
                    if (navigator.sendBeacon) {
                        const formData = new FormData();
                        formData.append('showtime_id', showtimeId);
                        formData.append('seat_ids', JSON.stringify(seatIds));
                        formData.append('_token', CSRF_TOKEN);

                        navigator.sendBeacon('{{ route('booking.release-seats') }}', formData);
                    } else {
                        // Fallback for older browsers
                        $.ajax({
                            url: '{{ route('booking.release-seats') }}',
                            type: 'POST',
                            data: {
                                showtime_id: showtimeId,
                                seat_ids: seatIds,
                                _token: CSRF_TOKEN
                            },
                            async: false // Synchronous request
                        });
                    }
                }
            }

            // Release seats on page unload
            window.addEventListener('beforeunload', releaseAllSeats);
            window.addEventListener('unload', releaseAllSeats);

            // Release seats when navigating away (for SPA-like behavior)
            $(window).on('beforeunload', function() {
                releaseAllSeats();
            });

            // Auto-release seats after 14 minutes (1 minute before session expires)
            setTimeout(function() {
                if (bookingData.seats.length > 0) {
                    alert('Thời gian giữ ghế sắp hết. Vui lòng hoàn tất đặt vé trong 1 phút nữa.');
                }
            }, 1 * 60 * 1000); // 14 minutes

            // Final release after 3 minutes
            setTimeout(function() {
                releaseAllSeats();
                if (bookingData.seats.length > 0) {
                    alert('Thời gian giữ ghế đã hết. Vui lòng chọn lại ghế.');
                    window.location.reload();
                }
            }, 3 * 60 * 1000); // 3 minutes

            // Form submission handler
            window.submitBookingForm = function() {
                const $submitBtn = $('#submit-btn');
                const $spinner = $submitBtn.find('.spinner-border');
                const $buttonText = $submitBtn.find('.button-text');

                // Validate seats selection
                if (bookingData.seats.length === 0) {
                    alert('Vui lòng chọn ít nhất một ghế!');
                    goToStep(1); // Return to seat selection step
                    return;
                }

                // Disable button and show spinner
                $submitBtn.prop('disabled', true);
                $spinner.removeClass('d-none');
                $buttonText.text('Đang xử lý...');

                // Prepare the form data
                const formData = {
                    showtime_id: '{{ $showtime->showtime_id }}',
                    selected_seats: JSON.stringify(bookingData.seats.map(s => s.id)),
                    food_items: JSON.stringify(Object.entries(bookingData.food).map(([id, item]) => ({
                        item_id: id,
                        quantity: item.quantity
                    }))),
                    promotion_code: bookingData.promotion.code,
                    order_summary: JSON.stringify({
                        seats: bookingData.seats,
                        food: Object.values(bookingData.food),
                        promotion: bookingData.promotion.data,
                        totals: {
                            seats: bookingData.seatTotal,
                            food: bookingData.foodTotal,
                            subtotal: bookingData.seatTotal + bookingData.foodTotal,
                            discount: bookingData.promotion.discount,
                            grand_total: (bookingData.seatTotal + bookingData.foodTotal) -
                                bookingData.promotion.discount
                        }
                    }),
                    _token: CSRF_TOKEN
                };

                // Send AJAX request
                $.post('{{ route('booking.select-seats') }}', formData)
                    .done(function(response) {
                        if (response.success) {
                            // Mark seats as released since we're moving to payment
                            seatsReleased = true;
                            // Redirect to payment page
                            window.location.href =
                                '{{ route('booking.payment', ['showtime' => $showtime->showtime_id]) }}';
                        } else {
                            alert(response.message || 'Có lỗi xảy ra, vui lòng thử lại.');
                        }
                    })
                    .fail(function(xhr) {
                        const errorMessage = xhr.responseJSON?.message ||
                            'Có lỗi xảy ra, vui lòng thử lại.';
                        alert(errorMessage);
                    })
                    .always(function() {
                        // Re-enable button and hide spinner
                        $submitBtn.prop('disabled', false);
                        $spinner.addClass('d-none');
                        $buttonText.text('Tiếp tục thanh toán');
                    });
            };
        });
    </script>
@endpush
