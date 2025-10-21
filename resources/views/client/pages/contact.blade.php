@extends('client.layouts.app')

@section('title', 'Liên hệ - MyShowz')

@section('content')
<section class="py-5 bg-night">
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold text-white">Liên hệ với chúng tôi</h1>
                <p class="text-muted">Mọi thắc mắc, đề xuất hoặc hỗ trợ — hãy gửi cho chúng tôi. Chúng tôi sẽ phản hồi trong vòng 24 giờ làm việc.</p>
            </div>
            <div class="col-md-4 d-none d-md-block">
                <div class="card p-3">
                    <div class="mb-2"><strong>Hotline:</strong> <a href="tel:+840123456789">+84 0123 456 789</a></div>
                    <div class="mb-2"><strong>Email:</strong> <a href="mailto:support@myshowz.example">support@myshowz.example</a></div>
                    <div class="mb-2"><strong>Địa chỉ:</strong> 123 Đường Rạp Chiếu, Quận 1, HCMC</div>
                    <div class="mb-2"><strong>Giờ làm việc:</strong> Thứ 2 - Thứ 6, 9:00 - 18:00</div>
                </div>
            </div>
        </div>

        @if(session('contact_success'))
            <div class="alert alert-success">{{ session('contact_success') }}</div>
        @elseif(session('contact_error'))
            <div class="alert alert-danger">{{ session('contact_error') }}</div>
        @endif

        <div class="row">
            <div class="col-lg-7">
                <div class="card p-4 mb-4">
                    <h5 class="mb-3">Gửi tin nhắn</h5>
                    <form method="POST" action="{{ route('contact.submit') }}" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" id="name" name="name" required aria-required="true" value="{{ old('name') }}">
                            @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required aria-required="true" value="{{ old('email') }}">
                            @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại (tuỳ chọn)</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone') }}">
                            @error('phone')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Chủ đề</label>
                            <input type="text" class="form-control" id="subject" name="subject" required value="{{ old('subject') }}">
                            @error('subject')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Nội dung</label>
                            <textarea class="form-control" id="message" name="message" rows="6" required>{{ old('message') }}</textarea>
                            @error('message')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="btn btn-brand">Gửi liên hệ</button>
                    </form>
                </div>

                <div class="card p-4">
                    <h5>Hỏi đáp (FAQ)</h5>
                    <div class="accordion" id="contactFaq">
                        <div class="accordion-item bg-dark border-dark-subtle">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqOne">Cách đổi vé?</button>
                            </h2>
                            <div id="faqOne" class="accordion-collapse collapse" data-bs-parent="#contactFaq">
                                <div class="accordion-body text-muted">Vui lòng liên hệ hotline để được hướng dẫn đổi vé theo chính sách.</div>
                            </div>
                        </div>
                        <div class="accordion-item bg-dark border-dark-subtle">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqTwo">Làm sao để nhận mã giảm giá?</button>
                            </h2>
                            <div id="faqTwo" class="accordion-collapse collapse" data-bs-parent="#contactFaq">
                                <div class="accordion-body text-muted">Theo dõi trang khuyến mãi hoặc đăng ký nhận bản tin để cập nhật mã khuyến mại.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card p-3 mb-4">
                    <h6>Văn phòng</h6>
                    <p class="text-muted mb-1">123 Đường Rạp Chiếu, Quận 1, TP.HCM</p>
                    <p class="text-muted mb-1">Hotline: <a href="tel:+840123456789">+84 0123 456 789</a></p>
                    <p class="text-muted">Email: <a href="mailto:support@myshowz.example">support@myshowz.example</a></p>
                </div>

                <div class="card p-3 mb-4">
                    <h6>Bản đồ</h6>
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.google.com/maps?q=Ho+Chi+Minh+City&output=embed" aria-label="Bản đồ đến văn phòng" loading="lazy"></iframe>
                    </div>
                </div>

                <div class="card p-3">
                    <h6>Theo dõi chúng tôi</h6>
                    <div class="d-flex gap-2 mt-2">
                        <a href="#" class="btn btn-outline-light btn-sm" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="btn btn-outline-light btn-sm" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="btn btn-outline-light btn-sm" aria-label="Twitter"><i class="bi bi-twitter"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
