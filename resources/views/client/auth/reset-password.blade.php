@extends('client.layouts.app')

@section('title', 'Đặt lại mật khẩu')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h1 class="h4 fw-semibold mb-3">Đặt lại mật khẩu</h1>
                        <p class="text-muted mb-4">
                            Nhập mã OTP đã gửi về email và mật khẩu mới.
                        </p>

                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('auth.reset-password.post') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Địa chỉ email</label>
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $email ?? '') }}"
                                    required
                                />
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="otp" class="form-label">Mã OTP</label>
                                <input
                                    id="otp"
                                    name="otp"
                                    type="text"
                                    inputmode="numeric"
                                    autocomplete="one-time-code"
                                    class="form-control @error('otp') is-invalid @enderror"
                                    value="{{ old('otp') }}"
                                    required
                                />
                                @error('otp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Mật khẩu mới</label>
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    required
                                />
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
                                <input
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    type="password"
                                    class="form-control"
                                    required
                                />
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                Cập nhật mật khẩu
                            </button>
                        </form>

                        <p class="text-center text-muted mt-3 mb-0">
                            Mã OTP chỉ có hiệu lực trong 10 phút.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
