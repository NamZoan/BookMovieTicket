@extends('client.layouts.app')

@section('title', 'Quên mật khẩu')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h1 class="h4 fw-semibold mb-3">Quên mật khẩu</h1>
                        <p class="text-muted mb-4">
                            Nhập email đã đăng ký để nhận mã OTP đặt lại mật khẩu.
                        </p>

                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('auth.forgot-password.post') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Địa chỉ email</label>
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}"
                                    required
                                />
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                Gửi mã OTP
                            </button>
                        </form>

                        <p class="text-center text-muted mt-3 mb-0">
                            Đã nhớ mật khẩu? <a href="{{ route('auth.login') }}">Đăng nhập</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
