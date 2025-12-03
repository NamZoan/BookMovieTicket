@extends('client.layouts.app')

@section('title', 'Xác Thực Email')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">Xác Thực Email</h4>
                </div>
                <div class="card-body text-center py-5">
                    @if (session('status') == 'verification-link-sent')
                        <div class="alert alert-success mb-4">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            Link xác thực mới đã được gửi đến email của bạn!
                        </div>
                    @endif

                    <div class="mb-4">
                        <i class="bi bi-envelope-check" style="font-size: 4rem; color: #0d6efd;"></i>
                    </div>

                    <h5 class="mb-3">Cảm ơn bạn đã đăng ký!</h5>
                    <p class="text-muted mb-4">
                        Trước khi tiếp tục, vui lòng kiểm tra email của bạn và nhấp vào link xác thực.
                        Nếu bạn không nhận được email, chúng tôi có thể gửi lại cho bạn.
                    </p>

                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-send me-2"></i>
                            Gửi Lại Email Xác Thực
                        </button>
                    </form>

                    <div class="mt-4">
                        <form method="POST" action="{{ route('auth.logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-link text-decoration-none">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Đăng Xuất
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <p class="text-muted">
                    <small>
                        <i class="bi bi-info-circle me-1"></i>
                        Nếu bạn không thấy email trong hộp thư đến, vui lòng kiểm tra thư mục spam.
                    </small>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

