<footer class="footer py-5 mt-auto">
    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-4">
                <h4 class="text-uppercase fw-semibold mb-3 text-black">MyShowz</h4>
                <p class="small text-black mb-4">{{ __('Trải nghiệm đặt vé phim trực tuyến với giao diện sáng, gọn gàng và tập trung vào sắc đỏ chủ đạo.') }}</p>
                <div class="d-flex gap-3">
                    <a href="#" class="btn btn-outline-brand btn-icon" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="btn btn-outline-brand btn-icon" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="btn btn-outline-brand btn-icon" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                    <a href="#" class="btn btn-outline-brand btn-icon" aria-label="Tiktok"><i class="bi bi-tiktok"></i></a>
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="text-uppercase fw-semibold small mb-3 text-black">{{ __('Khám phá') }}</h6>
                <ul class="list-unstyled small text-black">
                    <li><a class="link-fade" href="{{ route('movies.index') }}">{{ __('Phim nổi bật') }}</a></li>
                    <li><a class="link-fade" href="{{ route('movies.index', ['status' => 'now-showing']) }}">{{ __('Đang chiếu') }}</a></li>
                    <li><a class="link-fade" href="{{ route('movies.index', ['status' => 'coming-soon']) }}">{{ __('Sắp chiếu') }}</a></li>
                    <li><a class="link-fade" href="{{ route('cinemas.index') }}">{{ __('Rạp gần bạn') }}</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="text-uppercase fw-semibold small mb-3 text-black">{{ __('Hỗ trợ') }}</h6>
                <ul class="list-unstyled small text-black">
                    <li><a class="link-fade" href="{{ route('faq') }}">{{ __('Câu hỏi thường gặp') }}</a></li>
                    <li><a class="link-fade" href="{{ route('terms') }}">{{ __('Điều khoản sử dụng') }}</a></li>
                    <li><a class="link-fade" href="{{ route('privacy') }}">{{ __('Chính sách bảo mật') }}</a></li>
                    <li><a class="link-fade" href="{{ route('contact') }}">{{ __('Liên hệ chúng tôi') }}</a></li>
                </ul>
            </div>
            <div class="col-lg-4">
                <h6 class="text-uppercase fw-semibold small mb-3 text-black">{{ __('Nhận bản tin') }}</h6>
                <p class="small text-black">{{ __('Luôn cập nhật các suất chiếu hot và ưu đãi mới nhất mỗi tuần.') }}</p>
                <form id="newsletter-form" action="{{ route('newsletter.subscribe') }}" method="POST" class="d-flex gap-2 flex-wrap">
                    @csrf
                    <label for="newsletter-email" class="visually-hidden">{{ __('Email của bạn') }}</label>
                    <input id="newsletter-email" type="email" name="email" class="form-control flex-grow-1" placeholder="{{ __('you@example.com') }}" required>
                    <button type="submit" class="btn btn-brand">{{ __('Đăng ký') }}</button>
                </form>
                <div id="newsletter-feedback" class="small mt-2 text-black"></div>
            </div>
        </div>

        <div class="border-top border-danger-subtle mt-5 pt-3 d-flex flex-column flex-lg-row justify-content-between align-items-center gap-2 small text-black">
            <p class="mb-0">&copy; {{ now()->year }} MyShowz. {{ __('Giữ mọi quyền.') }}</p>
            <p class="mb-0">{{ __('Thiết kế bởi đội ngũ MyShowz với niềm đam mê điện ảnh.') }}</p>
        </div>
    </div>
</footer>
