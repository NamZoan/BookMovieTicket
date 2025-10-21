@extends('client.layouts.app')

@section('title', 'Giới thiệu - MyShowz')

@section('content')
<section class="py-5 bg-night">
    <div class="container">
        <div class="row align-items-center mb-4">
            <div class="col-md-7">
                <h1 class="display-5 fw-bold text-white">MyShowz — Kết nối người yêu điện ảnh</h1>
                <p class="lead text-muted">Sứ mệnh của chúng tôi là giúp mọi người tìm thấy những trải nghiệm điện ảnh tuyệt vời — nhanh chóng, an toàn và đáng tin cậy.</p>
                <p class="text-muted">Chúng tôi hợp tác với các rạp hàng đầu để mang lại lịch chiếu cập nhật, khuyến mãi và quyền lợi cho người xem.</p>
                <a href="{{ route('movies.index') }}" class="btn btn-brand mt-3" aria-label="Xem danh sách phim">Khám phá phim</a>
            </div>
            <div class="col-md-5 d-none d-md-block">
                <img src="{{ asset('assets/img/default/cinema.jpg') }}" alt="MyShowz" class="img-fluid rounded shadow-sm">
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-12">
                <h3 class="section-title text-white">Hành trình của chúng tôi</h3>
                <div class="timeline mt-3"> <!-- simple vertical timeline -->
                    <div class="d-flex flex-column gap-4">
                        <div class="d-flex gap-3">
                            <div class="flex-shrink-0 text-brand fw-bold">2018</div>
                            <div>
                                <h6 class="mb-1 text-white">Khởi nghiệp</h6>
                                <p class="text-muted mb-0">Bắt đầu với 1 rạp đối tác và tầm nhìn số hoá trải nghiệm đặt vé.</p>
                            </div>
                        </div>

                        <div class="d-flex gap-3">
                            <div class="flex-shrink-0 text-brand fw-bold">2020</div>
                            <div>
                                <h6 class="mb-1 text-white">Phát triển nhanh</h6>
                                <p class="text-muted mb-0">Mở rộng hợp tác với chuỗi rạp lớn, ra mắt ứng dụng di động và tính năng ưu đãi.</p>
                            </div>
                        </div>

                        <div class="d-flex gap-3">
                            <div class="flex-shrink-0 text-brand fw-bold">2023</div>
                            <div>
                                <h6 class="mb-1 text-white">Trải nghiệm hiện đại</h6>
                                <p class="text-muted mb-0">Cải tiến UI, streaming trailer, và tích hợp nhiều phương thức thanh toán.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-12">
                <h3 class="section-title text-white">Đội ngũ</h3>
            </div>

            <div class="col-md-4 mt-3">
                <div class="card movie-card p-3 h-100">
                    <div class="d-flex gap-3 align-items-center">
                        <img src="{{ asset('assets/img/default/cinema.jpg') }}" alt="Founder" width="64" height="64" class="rounded-circle">
                        <div>
                            <h6 class="mb-0">Nguyễn Văn A</h6>
                            <small class="text-muted">CEO & Người sáng lập</small>
                        </div>
                    </div>
                    <p class="text-muted mt-3">Đam mê điện ảnh và công nghệ, dẫn dắt đội ngũ xây dựng trải nghiệm đặt vé thông minh.</p>
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-outline-light btn-sm" aria-label="Twitter"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="btn btn-outline-light btn-sm" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mt-3">
                <div class="card movie-card p-3 h-100">
                    <div class="d-flex gap-3 align-items-center">
                        <img src="{{ asset('assets/img/default/cinema.jpg') }}" alt="CTO" width="64" height="64" class="rounded-circle">
                        <div>
                            <h6 class="mb-0">Trần Thị B</h6>
                            <small class="text-muted">CTO</small>
                        </div>
                    </div>
                    <p class="text-muted mt-3">Chịu trách nhiệm kỹ thuật, hạ tầng và kiến trúc hệ thống.</p>
                </div>
            </div>

            <div class="col-md-4 mt-3">
                <div class="card movie-card p-3 h-100">
                    <div class="d-flex gap-3 align-items-center">
                        <img src="{{ asset('assets/img/default/cinema.jpg') }}" alt="CMO" width="64" height="64" class="rounded-circle">
                        <div>
                            <h6 class="mb-0">Lê Văn C</h6>
                            <small class="text-muted">CMO</small>
                        </div>
                    </div>
                    <p class="text-muted mt-3">Phát triển thương hiệu và hợp tác chiến lược với đối tác rạp chiếu.</p>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-12">
                <h3 class="section-title text-white">Thống kê</h3>
                <div class="row text-center mt-3">
                    <div class="col-4">
                        <div class="stats-tile p-3">
                            <div class="h2 text-white">1,234,567+</div>
                            <div class="text-muted">Vé đã bán</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="stats-tile p-3">
                            <div class="h2 text-white">152</div>
                            <div class="text-muted">Rạp đối tác</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="stats-tile p-3">
                            <div class="h2 text-white">98,456</div>
                            <div class="text-muted">Người dùng hoạt động</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-12">
                <h3 class="section-title text-white">Đối tác & Cảm nhận</h3>
                <div class="mt-3">
                    @include('client.components.testimonial-slider', ['testimonials' => $testimonials ?? []])
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card p-4 bg-translucent">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                        <div>
                            <h4 class="text-white mb-1">Sẵn sàng xem phim?</h4>
                            <p class="text-muted mb-0">Khám phá lịch chiếu và đặt vé ngay hôm nay.</p>
                        </div>
                        <div>
                            <a href="{{ route('movies.index') }}" class="btn btn-brand me-2">Danh sách phim</a>
                            <a href="{{ route('booking.create', ['showtime' => 0]) }}" class="btn btn-outline-light">Đặt vé</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
