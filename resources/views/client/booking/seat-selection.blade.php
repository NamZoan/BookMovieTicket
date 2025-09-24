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
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
            --gray-400: #ced4da;
            --gray-500: #adb5bd;
            --gray-600: #6c757d;
            --gray-700: #495057;
            --gray-800: #343a40;
            --gray-900: #212529;
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
            --theme-bg: #1e1e1e;
            --theme-border: #2d2d2d;
        }

        .light-theme {
            --bg-color: var(--light-bg);
            --text-color: var(--text-dark);
            --card-bg: #ffffff;
            --border-color: var(--border-light);
            --theme-bg: #f3ebeb;
            --theme-border: #dee2e6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: var(--transition);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        /* Header Styles */
        header {
            background-color: var(--card-bg);
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 15px 0;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: var(--primary);
            font-weight: 700;
            font-size: 1.5rem;
        }

        .logo i {
            margin-right: 10px;
            font-size: 1.8rem;
        }

        .user-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .theme-toggle {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.2rem;
            color: var(--text-color);
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--bg-color);
        }

        .user-icon {
            font-size: 1.5rem;
            color: var(--text-color);
            text-decoration: none;
        }

        /* Progress Bar */
        .progress-container {
            margin: 30px 0;
        }

        .progress-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 30px;
            counter-reset: step;
        }

        .progress-steps::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 0;
            width: 100%;
            height: 4px;
            background-color: var(--gray-300);
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
            background-color: var(--gray-300);
            color: var(--gray-700);
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

        /* Form Steps */
        .booking-form {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 30px;
            margin-bottom: 30px;
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
            margin-bottom: 20px;
            color: var(--primary);
            font-weight: 600;
        }

        /* Date Selection */
        .date-carousel {
            display: flex;
            overflow-x: auto;
            gap: 15px;
            padding: 10px 5px;
            margin-bottom: 30px;
            scrollbar-width: thin;
        }

        .date-cell {
            min-width: 80px;
            height: 80px;
            background-color: var(--bg-color);
            border-radius: var(--border-radius);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            border: 2px solid transparent;
            flex-shrink: 0;
        }

        .date-cell.selected {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .date-numeric {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .date-day {
            font-size: 0.9rem;
        }

        /* Show Times */
        .show-times {
            margin-top: 20px;
        }

        .screen-times {
            margin-bottom: 25px;
            padding: 15px;
            background-color: var(--bg-color);
            border-radius: var(--border-radius);
        }

        .screen-title {
            font-size: 1.1rem;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .time-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .time-btn {
            padding: 10px 15px;
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
        }

        .time-btn:hover {
            border-color: var(--primary);
        }

        .time-btn.selected {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        /* Seat Selection */
        .seat-selection-container {
            display: flex;
            flex-direction: column;
        }

        @media (min-width: 992px) {
            .seat-selection-container {
                flex-direction: row;
            }

            .seat-map-container {
                flex: 2;
            }

            .selected-seats-container {
                flex: 1;
            }
        }

        .seat-map {
            background-color: var(--bg-color);
            padding: 10px;
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
            overflow: hidden;
        }

        .screen-indicator:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(to right, #ff0000, #ff5a00, #ff9a00, #ffce00, #ff9a00, #ff5a00, #ff0000);
            border-radius: 0 0 4px 4px;
        }

        .seats-container {
            margin-bottom: 20px;
        }

        .seat-row {
            display: flex;
            justify-content: center;
            margin-bottom: 5px;
        }

        .seat {
            width: 35px;
            height: 35px;
            border-radius: 8px 8px 3px 3px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            margin: 4px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        }

        /* Ghế thường */
        .seat.normal {
            background: linear-gradient(to bottom, #5C9CE5, #4A89DC);
            color: white;
            border: 1px solid #4A89DC;
        }

        .seat.normal:hover {
            background: linear-gradient(to bottom, #4A89DC, #357ABD);
            transform: translateY(-2px);
        }

        /* Ghế VIP */
        .seat.vip {
            background: linear-gradient(to bottom, #FFD700, #FFC200);
            color: #000;
            border: 1px solid #FFC200;
        }

        .seat.vip:hover {
            background: linear-gradient(to bottom, #FFC200, #FFB400);
            transform: translateY(-2px);
        }

        /* Ghế đôi */
        .seat.couple {
            background: linear-gradient(to bottom, #FF7BAC, #FF5B93);
            color: white;
            border: 1px solid #FF5B93;
            width: 70px; /* Ghế đôi rộng gấp đôi */
        }

        .seat.couple:hover {
            background: linear-gradient(to bottom, #FF5B93, #FF3B7A);
            transform: translateY(-2px);
        }

        /* Ghế disabled */
        .seat.disabled {
            background: linear-gradient(to bottom, #E0E0E0, #CCCCCC);
            color: #999;
            cursor: not-allowed;
            border: 1px solid #CCCCCC;
        }

        /* Ghế đã chọn */
        .seat.selected {
            background: linear-gradient(to bottom, #2ECC71, #27AE60);
            color: white;
            border: 1px solid #27AE60;
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(46, 204, 113, 0.3);
        }

        /* Ghế đã được đặt */
        .seat.occupied {
            background: linear-gradient(to bottom, #E74C3C, #C0392B);
            color: white;
            cursor: not-allowed;
            border: 1px solid #C0392B;
            opacity: 0.8;
        }

        /* Hiệu ứng before cho tất cả các ghế */
        .seat:before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 4px;
            right: 4px;
            height: 4px;
            border-radius: 0 0 3px 3px;
            opacity: 0.6;
        }

        /* Màu đổ bóng cho từng loại ghế */
        .seat.normal:before {
            background-color: #357ABD;
        }

        .seat.vip:before {
            background-color: #FFB400;
        }

        .seat.couple:before {
            background-color: #FF3B7A;
        }

        .seat.disabled:before {
            background-color: #BBBBBB;
        }

        .seat.selected:before {
            background-color: #219A55;
        }

        .seat.occupied:before {
            background-color: #962D22;
        }

        /* Cập nhật màu cho chú thích ghế */
        .seat-info {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
        }

        .seat-info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }

        .seat-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .normal-color { background: linear-gradient(to bottom, #5C9CE5, #4A89DC); }
        .vip-color { background: linear-gradient(to bottom, #FFD700, #FFC200); }
        .couple-color { background: linear-gradient(to bottom, #FF7BAC, #FF5B93); }
        .disabled-color { background: linear-gradient(to bottom, #E0E0E0, #CCCCCC); }
        .selected-color { background: linear-gradient(to bottom, #2ECC71, #27AE60); }
        .occupied-color { background: linear-gradient(to bottom, #E74C3C, #C0392B); }

        .selected-seats-container {
            background-color: var(--bg-color);
            border-radius: var(--border-radius);
            padding: 10px;
            box-shadow: var(--shadow);
        }

        .selected-seats-title {
            font-size: 1.2rem;
            margin-bottom: 15px;
            font-weight: 600;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .selected-seats-title i {
            font-size: 1.5rem;
            color: var(--primary);
        }

        .selected-seats-list {
            margin-bottom: 20px;
            max-height: 400px;
            overflow-y: auto;
        }

        .selected-seat-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--card-bg);
            border-radius: 6px;
            margin-bottom: 8px;
        }

        .seat-remove {
            background: none;
            border: none;
            color: var(--danger);
            cursor: pointer;
            font-size: 1.2rem;
        }

        .seat-price {
            font-weight: 600;
            color: var(--primary);
        }

        .selected-seats-total {
            border-top: 2px solid var(--border-color);
            padding-top: 15px;
            margin-top: 15px;
        }

        .total-item {
            display: flex;
            justify-content: space-between;
        }

        .total-final {
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--primary);
        }

        /* Food Selection */
        .snack-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }

        .snack-card {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            padding: 15px;
            text-align: center;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .snack-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .snack-image {
            width: 100%;
            height: 120px;
            object-fit: contain;
            margin-bottom: 10px;
        }

        .snack-name {
            font-size: 1rem;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .snack-price {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 15px;
        }

        .qty-controls {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .qty-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: none;
            background-color: var(--primary);
            color: white;
            cursor: pointer;
            font-weight: bold;
        }

        .qty-input {
            width: 40px;
            text-align: center;
            padding: 5px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
        }

        .snack-summary {
            margin-top: 30px;
            padding: 20px;
            background-color: var(--bg-color);
            border-radius: var(--border-radius);
        }

        .summary-title {
            font-size: 1.2rem;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid var(--border-color);
            font-weight: 600;
            font-size: 1.1rem;
        }

        /* Payment Form */
        .payment-form {
            margin: 20px 0;
        }

        .payment-methods {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .payment-method {
            flex: 1;
            padding: 20px;
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
        }

        .payment-method.selected {
            border-color: var(--primary);
            background-color: rgba(230, 30, 77, 0.05);
        }

        .payment-method i {
            font-size: 2rem;
            margin-bottom: 10px;
            color: var(--gray-600);
        }

        .payment-method.selected i {
            color: var(--primary);
        }

        .payment-method-name {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            background-color: var(--card-bg);
            color: var(--text-color);
            font-size: 1rem;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
        }

        .form-row {
            display: flex;
            gap: 20px;
        }

        .form-col {
            flex: 1;
        }

        /* E-Ticket */
        .ticket-body {
            background-color: var(--theme-bg);
            font-family: "Yanone Kaffeesatz", sans-serif;
        }

        .ticket {
            width: 400px;
            max-width: 100%;
            background-color: var(--theme-bg);
            margin: 25px auto;
            position: relative;
            box-shadow: var(--shadow-lg);
            border-radius: 12px;
            padding: 20px;
        }

        .holes-top {
            height: 50px;
            width: 50px;
            background-color: var(--theme-bg);
            border-radius: 50%;
            position: absolute;
            left: 50%;
            margin-left: -25px;
            top: -25px;
        }

        .holes-top:before,
        .holes-top:after {
            content: "";
            height: 50px;
            width: 50px;
            background-color: var(--theme-bg);
            position: absolute;
            border-radius: 50%;
        }

        .holes-top:before {
            left: -200px;
        }

        .holes-top:after {
            left: 200px;
        }

        .holes-lower {
            position: relative;
            margin: 25px;
            border: 1px dashed var(--theme-border);
        }

        .holes-lower:before,
        .holes-lower:after {
            content: "";
            height: 50px;
            width: 50px;
            background-color: var(--theme-bg);
            position: absolute;
            border-radius: 50%;
        }

        .holes-lower:before {
            top: -25px;
            left: -50px;
        }

        .holes-lower:after {
            top: -25px;
            left: 350px;
        }

        .title {
            padding: 50px 25px 10px;
            text-align: center;
        }

        .cinema {
            color: var(--gray-700);
            font-size: 1.5rem;
        }

        .movie-title {
            font-size: 1.2rem;
            font-weight: 700;
        }

        .info {
            padding: 15px 25px;
        }

        .ticket-table {
            width: 100%;
            font-size: 18px;
            margin-bottom: 15px;
            border-collapse: collapse;
        }

        .ticket-table th {
            text-align: left;
            padding-bottom: 10px;
            color: var(--gray-700);
        }

        .ticket-table td {
            padding: 5px 0;
            font-size: 26px;
        }

        .bigger {
            font-size: 40px;
        }

        .serial {
            background-color: var(--theme-bg);
            padding: 25px;
            margin-bottom: 25px;
            border-radius: 12px;
        }

        .barcode {
            border-collapse: collapse;
            margin: 0 auto;
        }

        .barcode td {
            width: 3px;
            height: 50px;
        }

        .numbers {
            margin-top: 10px;
        }

        .numbers td {
            font-size: 16px;
            text-align: center;
            color: var(--gray-700);
            padding: 0 2px;
        }

        /* Navigation Buttons */
        .form-navigation {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .nav-btn {
            padding: 12px 25px;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-prev {
            background-color: var(--gray-300);
            color: var(--gray-700);
        }

        .btn-prev:hover {
            background-color: var(--gray-400);
        }

        .btn-next {
            background-color: var(--primary);
            color: white;
        }

        .btn-next:hover:not(:disabled) {
            background-color: var(--primary-dark);
        }

        .btn-next:disabled {
            background-color: var(--gray-400);
            cursor: not-allowed;
        }

        .btn-complete {
            background-color: var(--success);
            color: white;
        }

        .btn-complete:hover {
            background-color: #0f6848;
        }

        .btn-cancel {
            background-color: var(--danger);
            color: white;
        }

        .btn-cancel:hover {
            background-color: #bb2d3b;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .progress-steps {
                flex-direction: column;
                align-items: flex-start;
                gap: 25px;
            }

            .progress-steps::before {
                display: none;
            }

            .progress-bar {
                display: none;
            }

            .step {
                display: flex;
                align-items: center;
                width: auto;
            }

            .step::before {
                margin: 0 15px 0 0;
            }

            .date-cell {
                width: 60px;
                height: 60px;
            }

            .date-numeric {
                font-size: 1.2rem;
            }

            .time-buttons {
                gap: 8px;
            }

            .time-btn {
                padding: 8px 12px;
                font-size: 0.9rem;
            }

            .seats-container {
                grid-template-columns: repeat(8, 1fr);
            }

            .form-row {
                flex-direction: column;
                gap: 0;
            }

            .payment-methods {
                flex-direction: column;
            }

            .ticket {
                width: 100%;
                padding: 15px;
            }

            .holes-top:before {
                left: -150px;
            }

            .holes-top:after {
                left: 150px;
            }

            .holes-lower:after {
                left: 280px;
            }
        }

        @media (max-width: 576px) {
            .booking-form {
                padding: 20px;
            }

            .nav-btn {
                padding: 10px 20px;
                font-size: 0.9rem;
            }

            .step-title {
                font-size: 1.3rem;
            }

            .seats-container {
                grid-template-columns: repeat(6, 1fr);
            }

            .snack-grid {
                grid-template-columns: 1fr;
            }

            .seat {
                width: 30px;
                height: 30px;
                font-size: 0.7rem;
            }

            .seat-info {
                gap: 10px;
            }

            .seat-info-item {
                font-size: 0.7rem;
            }

            .seat-color {
                width: 12px;
                height: 12px;
            }
        }
    </style>
@endpush
@section('content')
    <div class="container py-5">
        <!-- Movie Information -->
        <div class="movie-info mb-4">
            <div class="row">
                <div class="col-md-3">
                    <img src="{{ $showtime->movie->poster_url ?? 'https://via.placeholder.com/200x300/f5f5f5/333?text=Movie+Poster' }}"
                         alt="{{ $showtime->movie->title }}" class="img-fluid rounded">
                </div>
                <div class="col-md-9">
                    <h2 class="movie-title">{{ $showtime->movie->title }}</h2>
                    <p class="movie-details">
                        <strong>Rạp:</strong> {{ $showtime->screen->cinema->name }} - {{ $showtime->screen->name }}<br>
                        <strong>Ngày:</strong> {{ \Carbon\Carbon::parse($showtime->show_date)->format('d/m/Y') }}<br>
                        <strong>Giờ:</strong> {{ \Carbon\Carbon::parse($showtime->show_time)->format('H:i') }}<br>
                        <strong>Thời lượng:</strong> {{ $showtime->movie->duration }} phút
                    </p>
                </div>
            </div>
        </div>

        <div class="progress-container">
            <div class="progress-steps" id="progress-steps">
                <div class="step active" data-step="1">
                    <span class="step-label">Chọn ghế</span>
                </div>
                <div class="step" data-step="2">
                    <span class="step-label">Đồ ăn</span>
                </div>
                <div class="step" data-step="3">
                    <span class="step-label">Thanh toán</span>
                </div>
                <div class="step" data-step="4">
                    <span class="step-label">Hoàn thành</span>
                </div>
                <div class="progress-bar" id="progress-bar"></div>
            </div>
        </div>

        <form id="booking-form" method="POST" action="{{ route('booking.select-seats') }}">
            @csrf
            <input type="hidden" name="showtime_id" value="{{ $showtime->showtime_id }}">

            <div class="booking-form">
                <!-- Step 1: Seat Selection -->
                <div class="form-step active" id="step-1">
                    <h2 class="step-title">Chọn Ghế Ngồi</h2>
                    <p>Vui lòng chọn ghế từ sơ đồ dưới đây:</p>

                    <div class="seat-selection-container">
                        <div class="seat-map-container">
                            <div class="seat-map">
                                <div class="screen-indicator">Màn Hình</div>

                                <div class="seats-container" id="seats-container">
                                    @foreach($seatMap as $row => $seats)
                                        <div class="seat-row" data-row="{{ $row }}">
                                            @foreach($seats as $seatNumber => $seat)
                                                <div class="seat {{ $seat['status'] }} {{ strtolower($seat['seat_type']) }}"
                                                     data-seat-id="{{ $seat['seat_id'] }}"
                                                     data-row="{{ $seat['row'] }}"
                                                     data-number="{{ $seat['number'] }}"
                                                     data-type="{{ $seat['seat_type'] }}">
                                                    {{ $seat['row'] }}{{ $seat['number'] }}
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>

                                <div class="seat-info">
                                    <div class="seat-info-item">
                                        <div class="seat-color normal-color"></div>
                                        <span>Ghế thường</span>
                                    </div>
                                    <div class="seat-info-item">
                                        <div class="seat-color vip-color"></div>
                                        <span>Ghế VIP</span>
                                    </div>
                                    <div class="seat-info-item">
                                        <div class="seat-color couple-color"></div>
                                        <span>Ghế đôi</span>
                                    </div>
                                    <div class="seat-info-item">
                                        <div class="seat-color disabled-color"></div>
                                        <span>Ghế không khả dụng</span>
                                    </div>
                                    <div class="seat-info-item">
                                        <div class="seat-color selected-color"></div>
                                        <span>Đã chọn</span>
                                    </div>
                                    <div class="seat-info-item">
                                        <div class="seat-color occupied-color"></div>
                                        <span>Đã đặt</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="selected-seats-container">
                            <h3 class="selected-seats-title">
                                <i class="fas fa-ticket-alt"></i>
                                Ghế đã chọn
                            </h3>

                            <div class="selected-seats-list" id="selected-seats-list">
                                <div class="no-seats-message">Chưa có ghế nào được chọn</div>
                            </div>

                            <div class="selected-seats-total">
                                <div class="total-item">
                                    <span>Số lượng ghế:</span>
                                    <span id="seats-count">0</span>
                                </div>
                                <div class="total-item">
                                    <span>Giá ghế:</span>
                                    <span>
                                        Thường: {{ number_format($pricing['prices']['Normal'] ?? $pricing['base_price']) }}đ |
                                        VIP: {{ number_format($pricing['prices']['VIP'] ?? $pricing['base_price'] * 1.5) }}đ |
                                        Đôi: {{ number_format($pricing['prices']['Couple'] ?? $pricing['base_price'] * 2) }}đ
                                    </span>
                                </div>
                                <div class="total-item total-final">
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

                <!-- Step 2: Food Selection -->
                <div class="form-step" id="step-2">
                    <h2 class="step-title">Đồ Ăn & Thức Uống</h2>
                    <p>Chọn đồ ăn thức uống cho buổi xem phim của bạn:</p>

                    <div class="snack-grid">
                        @if($foodItems && count($foodItems) > 0)
                            @foreach($foodItems as $category => $items)
                                <h4 class="category-title">{{ $category }}</h4>
                                @foreach($items as $item)
                                    <div class="snack-card" data-id="{{ $item->item_id }}" data-name="{{ $item->name }}" data-price="{{ $item->price }}">
                                        <img src="{{ $item->image_url ?? 'https://via.placeholder.com/150x100/f5f5f5/333?text=' . urlencode($item->name) }}"
                                             alt="{{ $item->name }}" class="snack-image">
                                        <div class="snack-name">{{ $item->name }}</div>
                                        <div class="snack-price">{{ number_format($item->price) }}đ</div>
                                        <div class="qty-controls">
                                            <button type="button" class="qty-btn minus">-</button>
                                            <input type="number" class="qty-input" value="0" min="0" data-id="{{ $item->item_id }}" name="food_items[{{ $item->item_id }}]">
                                            <button type="button" class="qty-btn plus">+</button>
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                        @else
                            <div class="no-food-message">
                                <p>Hiện tại không có đồ ăn thức uống nào khả dụng.</p>
                            </div>
                        @endif
                    </div>

                    <div class="snack-summary">
                        <div class="summary-title">Tóm tắt đơn hàng</div>
                        <div id="snack-summary-items">
                            <div class="summary-item">Chưa có món nào được chọn</div>
                        </div>
                        <div class="summary-total">
                            <span>Tổng cộng:</span>
                            <span id="snack-total">0đ</span>
                        </div>
                    </div>

                    <div class="form-navigation">
                        <button type="button" class="nav-btn btn-prev">Trở về</button>
                        <button type="button" class="nav-btn btn-next" id="step-2-next">Tiếp tục</button>
                    </div>
                </div>

                <!-- Step 3: Payment -->
                <div class="form-step" id="step-3">
                    <h2 class="step-title">Thanh Toán</h2>

                    <div class="payment-form">
                        <div class="payment-methods">
                            <div class="payment-method selected" data-method="Cash">
                                <i class="fas fa-money-bill-wave"></i>
                                <div class="payment-method-name">Tiền mặt</div>
                                <div class="payment-method-desc">Thanh toán tại rạp</div>
                            </div>
                            <div class="payment-method" data-method="Credit Card">
                                <i class="fas fa-credit-card"></i>
                                <div class="payment-method-name">Thẻ tín dụng</div>
                                <div class="payment-method-desc">Thanh toán an toàn</div>
                            </div>
                            <div class="payment-method" data-method="Banking">
                                <i class="fas fa-university"></i>
                                <div class="payment-method-name">Chuyển khoản</div>
                                <div class="payment-method-desc">Thanh toán qua ngân hàng</div>
                            </div>
                        </div>

                        <input type="hidden" name="payment_method" value="Cash" id="payment-method-input">

                        <div id="order-summary">
                            <div class="summary-title">Tóm tắt đơn hàng</div>
                            <div class="summary-item">
                                <span>Vé xem phim:</span>
                                <span id="ticket-price">0đ</span>
                            </div>
                            <div class="summary-item">
                                <span>Đồ ăn thức uống:</span>
                                <span id="food-price">0đ</span>
                            </div>
                            <div class="summary-total">
                                <span>Tổng cộng:</span>
                                <span id="total-price">0đ</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-navigation">
                        <button type="button" class="nav-btn btn-prev">Trở về</button>
                        <button type="button" class="nav-btn btn-cancel" onclick="window.location.href='{{ route('home') }}'">Hủy</button>
                        <button type="submit" class="nav-btn btn-next" id="step-3-next">Đặt vé</button>
                    </div>
                </div>

            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Progress bar functionality
            const progressBar = document.getElementById('progress-bar');
            const steps = document.querySelectorAll('.step');
            const formSteps = document.querySelectorAll('.form-step');
            const nextButtons = document.querySelectorAll('.btn-next');
            const prevButtons = document.querySelectorAll('.btn-prev');

            // Initialize progress bar
            updateProgressBar();

            // Booking data object to store user selections
            const bookingData = {
                showtime_id: {{ $showtime->showtime_id }},
                selected_seats: [],
                food_items: {},
                payment_method: 'Cash',
                total: 0
            };

            // Pricing data from backend
            const pricing = @json($pricing);

            // Seat selection functionality
            const seats = document.querySelectorAll('.seat');
            seats.forEach(seat => {
                seat.addEventListener('click', function() {
                    if (this.classList.contains('occupied') || this.classList.contains('held')) {
                        return;
                    }

                    this.classList.toggle('selected');
                    const seatId = this.getAttribute('data-seat-id');

                    if (this.classList.contains('selected')) {
                        if (!bookingData.selected_seats.includes(seatId)) {
                            bookingData.selected_seats.push(seatId);
                        }
                    } else {
                        const index = bookingData.selected_seats.indexOf(seatId);
                        if (index > -1) {
                            bookingData.selected_seats.splice(index, 1);
                        }
                    }

                    updateSelectedSeatsDisplay();
                    updateStep1NextButton();
                });
            });

            function updateStep1NextButton() {
                const step1Next = document.getElementById('step-1-next');
                step1Next.disabled = bookingData.selected_seats.length === 0;
            }

            function updateSelectedSeatsDisplay() {
                const selectedSeatsList = document.getElementById('selected-seats-list');
                const seatsCount = document.getElementById('seats-count');
                const seatsTotal = document.getElementById('seats-total');

                // Clear existing list
                selectedSeatsList.innerHTML = '';

                if (bookingData.selected_seats.length === 0) {
                    selectedSeatsList.innerHTML = '<div class="no-seats-message">Chưa có ghế nào được chọn</div>';
                    seatsCount.textContent = '0';
                    seatsTotal.textContent = '0đ';
                    return;
                }

                // Add each selected seat to the list
                bookingData.selected_seats.forEach(seatId => {
                    const seatElement = document.querySelector(`.seat[data-seat-id="${seatId}"]`);
                    if (seatElement) {
                        const row = seatElement.getAttribute('data-row');
                        const number = seatElement.getAttribute('data-number');
                        const seatLabel = row + number;

                        const seatType = seatElement.getAttribute('data-type');
                        const seatPrice = getSeatPrice(seatType);

                        const seatItem = document.createElement('div');
                        seatItem.className = 'selected-seat-item';
                        seatItem.innerHTML = `
                            <div class="seat-info">
                                <span class="seat-label">Ghế ${seatLabel} (${getSeatTypeName(seatType)})</span>
                            </div>
                            <div class="seat-details">
                                <span class="seat-price">${formatCurrency(seatPrice)}</span>
                                <button type="button" class="seat-remove" data-seat-id="${seatId}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `;

                        // Add event listener to remove button
                        const removeBtn = seatItem.querySelector('.seat-remove');
                        removeBtn.addEventListener('click', function () {
                            const seatId = this.getAttribute('data-seat-id');

                            // Remove from booking data
                            const index = bookingData.selected_seats.indexOf(seatId);
                            if (index > -1) {
                                bookingData.selected_seats.splice(index, 1);
                            }

                            // Update seat visual state
                            const seatElement = document.querySelector(`.seat[data-seat-id="${seatId}"]`);
                            if (seatElement) {
                                seatElement.classList.remove('selected');
                            }

                            // Update display
                            updateSelectedSeatsDisplay();
                            updateStep1NextButton();
                        });

                        selectedSeatsList.appendChild(seatItem);
                    }
                });

                // Update counts
                let total = 0;
                bookingData.selected_seats.forEach(seatId => {
                    const seatElement = document.querySelector(`.seat[data-seat-id="${seatId}"]`);
                    if (seatElement) {
                        const seatType = seatElement.getAttribute('data-type');
                        total += getSeatPrice(seatType);
                    }
                });

                seatsCount.textContent = bookingData.selected_seats.length;
                seatsTotal.textContent = formatCurrency(total);
                updateOrderSummary();
            }

            // Snack selection
            const snackCards = document.querySelectorAll('.snack-card');
            snackCards.forEach(card => {
                const minusBtn = card.querySelector('.minus');
                const plusBtn = card.querySelector('.plus');
                const input = card.querySelector('.qty-input');
                const id = card.getAttribute('data-id');
                const name = card.getAttribute('data-name');
                const price = parseInt(card.getAttribute('data-price'));

                // Initialize snack in booking data
                if (!bookingData.food_items[id]) {
                    bookingData.food_items[id] = {
                        name: name,
                        price: price,
                        quantity: 0
                    };
                } else {
                    input.value = bookingData.food_items[id].quantity;
                }

                minusBtn.addEventListener('click', function () {
                    let value = parseInt(input.value);
                    if (value > 0) {
                        value--;
                        input.value = value;
                        bookingData.food_items[id].quantity = value;
                        updateSnackSummary();
                    }
                });

                plusBtn.addEventListener('click', function () {
                    let value = parseInt(input.value);
                    value++;
                    input.value = value;
                    bookingData.food_items[id].quantity = value;
                    updateSnackSummary();
                });

                input.addEventListener('change', function () {
                    let value = parseInt(this.value);
                    if (isNaN(value) || value < 0) {
                        value = 0;
                        this.value = 0;
                    }
                    bookingData.food_items[id].quantity = value;
                    updateSnackSummary();
                });
            });

            function updateSnackSummary() {
                const summaryContainer = document.getElementById('snack-summary-items');
                const totalElement = document.getElementById('snack-total');
                let total = 0;
                let summaryHTML = '';

                // Calculate total and build summary
                for (const id in bookingData.food_items) {
                    const snack = bookingData.food_items[id];
                    if (snack.quantity > 0) {
                        const itemTotal = snack.price * snack.quantity;
                        total += itemTotal;

                        summaryHTML += `
                            <div class="summary-item">
                                <span>${snack.name} x${snack.quantity}</span>
                                <span>${formatCurrency(itemTotal)}</span>
                            </div>
                        `;
                    }
                }

                // Update summary
                summaryContainer.innerHTML = summaryHTML || '<div class="summary-item">Chưa có món nào được chọn</div>';
                totalElement.textContent = formatCurrency(total);
                updateOrderSummary();
            }

            // Payment method selection
            const paymentMethods = document.querySelectorAll('.payment-method');
            paymentMethods.forEach(method => {
                method.addEventListener('click', function () {
                    paymentMethods.forEach(m => m.classList.remove('selected'));
                    this.classList.add('selected');
                    bookingData.payment_method = this.getAttribute('data-method');
                    document.getElementById('payment-method-input').value = bookingData.payment_method;
                });
            });

            function updateOrderSummary() {
                let ticketPrice = 0;
                bookingData.selected_seats.forEach(seatId => {
                    const seatElement = document.querySelector(`.seat[data-seat-id="${seatId}"]`);
                    if (seatElement) {
                        const seatType = seatElement.getAttribute('data-type');
                        ticketPrice += getSeatPrice(seatType);
                    }
                });

                let foodPrice = 0;
                for (const id in bookingData.food_items) {
                    const snack = bookingData.food_items[id];
                    if (snack.quantity > 0) {
                        foodPrice += snack.price * snack.quantity;
                    }
                }

                const totalPrice = ticketPrice + foodPrice;

                document.getElementById('ticket-price').textContent = formatCurrency(ticketPrice);
                document.getElementById('food-price').textContent = formatCurrency(foodPrice);
                document.getElementById('total-price').textContent = formatCurrency(totalPrice);

                bookingData.total = totalPrice;
            }

            function formatCurrency(amount) {
                return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
            }

            function getSeatPrice(seatType) {
                switch(seatType) {
                    case 'VIP':
                        return pricing.prices.VIP || pricing.base_price * 1.5;
                    case 'Couple':
                        return pricing.prices.Couple || pricing.base_price * 2;
                    case 'Disabled':
                        return pricing.prices.Disabled || pricing.base_price * 0.8;
                    default:
                        return pricing.prices.Normal || pricing.base_price;
                }
            }

            function getSeatTypeName(seatType) {
                switch(seatType) {
                    case 'VIP':
                        return 'VIP';
                    case 'Couple':
                        return 'Đôi';
                    case 'Disabled':
                        return 'Khuyết tật';
                    default:
                        return 'Thường';
                }
            }

            // Next button functionality
            nextButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const currentStep = document.querySelector('.form-step.active');
                    const currentStepId = currentStep.id;
                    const stepNumber = parseInt(currentStepId.split('-')[1]);

                    if (stepNumber < 3) {
                        // Hide current step
                        currentStep.classList.remove('active');

                        // Show next step
                        document.getElementById(`step-${stepNumber + 1}`).classList.add('active');

                        // Update progress
                        steps.forEach(step => {
                            const stepNum = parseInt(step.getAttribute('data-step'));
                            if (stepNum <= stepNumber) {
                                step.classList.add('completed');
                                step.classList.remove('active');
                            } else if (stepNum === stepNumber + 1) {
                                step.classList.add('active');
                            }
                        });

                        updateProgressBar();
                    }
                });
            });

            // Previous button functionality
            prevButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const currentStep = document.querySelector('.form-step.active');
                    const currentStepId = currentStep.id;
                    const stepNumber = parseInt(currentStepId.split('-')[1]);

                    if (stepNumber > 1) {
                        // Hide current step
                        currentStep.classList.remove('active');

                        // Show previous step
                        document.getElementById(`step-${stepNumber - 1}`).classList.add('active');

                        // Update progress
                        steps.forEach(step => {
                            const stepNum = parseInt(step.getAttribute('data-step'));
                            if (stepNum === stepNumber - 1) {
                                step.classList.add('active');
                                step.classList.remove('completed');
                            } else if (stepNum >= stepNumber) {
                                step.classList.remove('active', 'completed');
                            }
                        });

                        updateProgressBar();
                    }
                });
            });

            function updateProgressBar() {
                const activeStep = document.querySelector('.step.active');
                if (activeStep) {
                    const stepNumber = parseInt(activeStep.getAttribute('data-step'));
                    const percentage = ((stepNumber - 1) / (steps.length - 1)) * 100;
                    progressBar.style.width = `${percentage}%`;
                }
            }

            // Form submission
            document.getElementById('booking-form').addEventListener('submit', function(e) {
                e.preventDefault();

                // Prepare form data
                const formData = new FormData();
                formData.append('_token', document.querySelector('input[name="_token"]').value);
                formData.append('showtime_id', bookingData.showtime_id);
                formData.append('selected_seats', JSON.stringify(bookingData.selected_seats));
                formData.append('payment_method', bookingData.payment_method);

                // Add food items
                const foodItems = [];
                for (const id in bookingData.food_items) {
                    const item = bookingData.food_items[id];
                    if (item.quantity > 0) {
                        foodItems.push({
                            item_id: id,
                            quantity: item.quantity
                        });
                    }
                }
                formData.append('food_items', JSON.stringify(foodItems));

                // Show loading
                const submitBtn = document.getElementById('step-3-next');
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Đang xử lý...';
                submitBtn.disabled = true;

                // Submit form
                fetch('{{ route("booking.select-seats") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Redirect to payment page
                        window.location.href = data.data.payment_url;
                    } else {
                        alert(data.message || 'Có lỗi xảy ra. Vui lòng thử lại.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra. Vui lòng thử lại.');
                })
                .finally(() => {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                });
            });

            // Initialize displays
            updateSelectedSeatsDisplay();
            updateSnackSummary();
        });
    </script>
@endsection
