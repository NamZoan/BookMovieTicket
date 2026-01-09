@extends('client.layouts.app')

@section('title', 'Xác thực email')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h1 class="h4 fw-semibold mb-3">Xác thực tài khoản</h1>
                        <p class="text-muted mb-4">
                            Chúng tôi đã gửi mã OTP tới email <strong>{{ $email }}</strong>.
                            Vui lòng nhập mã để kích hoạt tài khoản.
                        </p>

                        @if (session('status') === 'otp-sent')
                            <div class="alert alert-success">
                                Mã OTP mới đã được gửi. Vui lòng kiểm tra email.
                            </div>
                        @elseif (session('status') === 'otp-required')
                            <div class="alert alert-warning">
                                Vui lòng nhập mã OTP để tiếp tục.
                            </div>
                        @endif

                        <form method="POST" action="{{ route('verification.verify') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="otp" class="form-label">Mã OTP</label>
                                <input
                                    id="otp"
                                    name="otp"
                                    type="text"
                                    inputmode="numeric"
                                    autocomplete="one-time-code"
                                    class="form-control @error('otp') is-invalid @enderror"
                                    placeholder="Nhập mã OTP"
                                    required
                                />
                                @error('otp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                Xác thực tài khoản
                            </button>
                        </form>

                        <form method="POST" action="{{ route('verification.send') }}" class="mt-3 text-center">
                            @csrf
                            <button type="submit" class="btn btn-link">
                                Gửi lại mã OTP
                            </button>
                        </form>
                    </div>
                </div>
                <p class="text-center text-muted mt-3 mb-0">
                    Mã OTP chỉ có hiệu lực trong 10 phút.
                </p>
            </div>
        </div>
    </div>
@endsection
