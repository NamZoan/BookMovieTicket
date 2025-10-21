@extends('admin.layouts.app')
@section('content')
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">
            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <i class="bx bx-dollar-circle text-success" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <span class="fw-semibold d-block mb-1">Tổng Doanh Thu</span>
                            <h3 class="card-title mb-2">{{ number_format($stats['total_revenue'], 0, ',', '.') }} VNĐ</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <i class="bx bx-check-circle text-primary" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <span class="fw-semibold d-block mb-1">Đơn Hàng Đã Xác Nhận</span>
                            <h3 class="card-title mb-2">{{ $stats['total_confirmed_bookings'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <i class="bx bx-user-plus text-info" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <span class="fw-semibold d-block mb-1">Khách Hàng Mới (Tháng)</span>
                            <h3 class="card-title mb-2">{{ $stats['new_customers_this_month'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <i class="bx bx-movie text-warning" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <span class="fw-semibold d-block mb-1">Tổng Số Phim</span>
                            <h3 class="card-title mb-2">{{ $stats['total_movies'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Stats Row -->
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <i class="bx bx-building text-secondary" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <span class="fw-semibold d-block mb-1">Tổng Số Rạp</span>
                            <h3 class="card-title mb-2">{{ $stats['total_cinemas'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <i class="bx bx-calendar text-danger" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <span class="fw-semibold d-block mb-1">Đặt Vé Hôm Nay</span>
                            <h3 class="card-title mb-2">{{ $stats['today_bookings'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <i class="bx bx-time text-warning" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <span class="fw-semibold d-block mb-1">Đơn Chờ Xử Lý</span>
                            <h3 class="card-title mb-2">{{ $stats['pending_bookings'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <i class="bx bx-trending-up text-success" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <span class="fw-semibold d-block mb-1">Tỷ Lệ Thành Công</span>
                            <h3 class="card-title mb-2">
                                @if($stats['total_confirmed_bookings'] > 0)
                                    {{ round(($stats['total_confirmed_bookings'] / ($stats['total_confirmed_bookings'] + $stats['pending_bookings'])) * 100, 1) }}%
                                @else
                                    0%
                                @endif
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Revenue Chart and Latest Bookings -->
            <div class="row">
                <!-- Revenue Chart -->
                <div class="col-12 col-lg-8 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title m-0">Biểu Đồ Doanh Thu 30 Ngày Qua</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="revenueChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
                <!--/ Revenue Chart -->

                <!-- Latest Bookings -->
                <div class="col-12 col-lg-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title m-0">10 Đơn Hàng Mới Nhất</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Mã Đơn</th>
                                            <th>Khách Hàng</th>
                                            <th>Phim</th>
                                            <th>Số Tiền</th>
                                            <th>Trạng Thái</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($stats['latest_bookings'] as $booking)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('admin.bookings.show', $booking->booking_id) }}"
                                                       class="text-primary fw-semibold">
                                                        {{ $booking->booking_code }}
                                                    </a>
                                                </td>
                                                <td>{{ $booking->customer_name }}</td>
                                                <td>{{ $booking->showtime->movie->title ?? 'N/A' }}</td>
                                                <td>{{ number_format($booking->final_amount, 0, ',', '.') }} VNĐ</td>
                                                <td>
                                                    @if($booking->booking_status == 'Confirmed')
                                                        <span class="badge bg-success">{{ $booking->booking_status }}</span>
                                                    @elseif($booking->booking_status == 'Pending')
                                                        <span class="badge bg-warning">{{ $booking->booking_status }}</span>
                                                    @else
                                                        <span class="badge bg-danger">{{ $booking->booking_status }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">Không có đơn hàng nào</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/ Latest Bookings -->
            </div>
        </div>
        <!-- / Content -->
        <div class="content-backdrop fade"></div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Revenue Chart
        const revenueData = @json($stats['revenue_data']);
        const ctx = document.getElementById('revenueChart').getContext('2d');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: revenueData.map(item => {
                    const date = new Date(item.date);
                    return date.toLocaleDateString('vi-VN', { month: 'short', day: 'numeric' });
                }),
                datasets: [{
                    label: 'Doanh Thu (VNĐ)',
                    data: revenueData.map(item => item.revenue),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN').format(value) + ' VNĐ';
                            }
                        }
                    }
                }
            }
        });
    </script>
@endsection
