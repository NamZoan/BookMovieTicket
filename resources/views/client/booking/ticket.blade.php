@extends('client.layouts.app')
@section('title', 'Vé xem phim')
@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Yanone+Kaffeesatz:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: "Yanone Kaffeesatz", sans-serif;
            background-color: #f3ebeb;
        }

        .ticket-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
        }

        .ticket {
            width: 100%;
            max-width: 400px;
            background-color: #f3ebeb;
            margin: 0 auto;
            position: relative;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            border-radius: 12px;
            padding: 20px;
        }

        .holes-top {
            height: 50px;
            width: 50px;
            background-color: #f3ebeb;
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
            background-color: #f3ebeb;
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
            border: 1px dashed #dee2e6;
        }

        .holes-lower:before,
        .holes-lower:after {
            content: "";
            height: 50px;
            width: 50px;
            background-color: #f3ebeb;
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
            color: #6c757d;
            font-size: 1.5rem;
        }

        .movie-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #e61e4d;
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
            color: #6c757d;
        }

        .ticket-table td {
            padding: 5px 0;
            font-size: 26px;
        }

        .bigger {
            font-size: 40px;
        }

        .serial {
            background-color: #f3ebeb;
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
            color: #6c757d;
            padding: 0 2px;
        }

        .ticket-info {
            text-align: center;
            margin-top: 15px;
            color: #6c757d;
        }

        .print-button {
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
        }

        .btn-primary {
            background-color: #e61e4d;
            color: white;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        @media print {
            .print-button {
                display: none;
            }

            .ticket-container {
                margin: 0;
                padding: 0;
            }
        }

        @media (max-width: 768px) {
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
    </style>
@endpush

@section('content')
<div class="ticket-container">
    <div class="ticket">
        <div class="holes-top"></div>
        <div class="title">
            <p class="cinema">{{ $booking->showtime->screen->cinema->name }}</p>
            <p class="movie-title">{{ $booking->showtime->movie->title }}</p>
        </div>

        <div class="info">
            <table class="info-table ticket-table">
                <tr>
                    <th>RẠP</th>
                    <th>HÀNG</th>
                    <th>GHẾ</th>
                </tr>
                <tr>
                    <td class="bigger">{{ $booking->showtime->screen->name }}</td>
                    <td class="bigger">
                        @php
                            $rows = $booking->bookingSeats->pluck('seat.row_name')->unique()->implode(',');
                        @endphp
                        {{ $rows }}
                    </td>
                    <td class="bigger">
                        @php
                            $seats = $booking->bookingSeats->pluck('seat.seat_number')->implode(',');
                        @endphp
                        {{ $seats }}
                    </td>
                </tr>
            </table>

            <table class="info-table ticket-table">
                <tr>
                    <th>GIÁ</th>
                    <th>NGÀY</th>
                    <th>GIỜ</th>
                </tr>
                <tr>
                    <td>{{ number_format($booking->final_amount) }}đ</td>
                    <td>-{{ \Carbon\Carbon::parse($booking->showtime->show_date)->format('d/m/Y') }}</td>
                    <td>-{{ \Carbon\Carbon::parse($booking->showtime->show_time)->format('H:i') }}</td>
                </tr>
            </table>
        </div>

        <div class="holes-lower"></div>

        <div class="serial">
            <table class="barcode ticket-table" id="barcode-table">
                <!-- Barcode will be generated by JavaScript -->
            </table>
            <table class="numbers ticket-table">
                <tr id="ticket-numbers">
                    <!-- Numbers will be generated by JavaScript -->
                </tr>
            </table>
            <div class="ticket-info">
                <p><strong>Mã vé:</strong> {{ $booking->booking_code }}</p>
                <p>Vui lòng trình mã QR này tại quầy vé</p>
            </div>
        </div>
    </div>

    <div class="print-button">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> In vé
        </button>
        <a href="{{ route('account.bookings') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Generate barcode
    const barcodeTable = document.getElementById('barcode-table');
    barcodeTable.innerHTML = '';

    const code = "11010010000100111011001011101111011010001110101110011001101110010010111101110111001011001001000011011000111010110001001110111101101001011010111000101101";
    const row = document.createElement('tr');

    for (let i = 0; i < code.length; i++) {
        const td = document.createElement('td');
        td.style.backgroundColor = code[i] === '1' ? 'black' : 'white';
        td.style.width = '3px';
        td.style.height = '50px';
        row.appendChild(td);
    }

    barcodeTable.appendChild(row);

    // Generate ticket numbers
    const numbersRow = document.getElementById('ticket-numbers');
    numbersRow.innerHTML = '';

    const numbers = "{{ $booking->booking_code }}";

    for (let i = 0; i < numbers.length; i++) {
        const td = document.createElement('td');
        td.textContent = numbers[i];
        numbersRow.appendChild(td);
    }
});
</script>
@endsection
