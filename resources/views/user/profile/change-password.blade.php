@extends('client.layouts.app')

@section('title', 'Đổi Mật Khẩu')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <!-- Header -->
            <div class="text-center mb-4">
                <h2 class="mb-1">Đổi Mật Khẩu</h2>
                <p class="text-muted mb-0">Bảo mật tài khoản của bạn</p>
            </div>

            <!-- Change Password Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Thay Đổi Mật Khẩu</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bx bx-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('user.change-password') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Mật Khẩu Hiện Tại <span class="text-danger">*</span></label>
                            <input type="password"
                                   class="form-control @error('current_password') is-invalid @enderror"
                                   id="current_password"
                                   name="current_password"
                                   required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mật Khẩu Mới <span class="text-danger">*</span></label>
                            <input type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   id="password"
                                   name="password"
                                   required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Mật khẩu phải có ít nhất 8 ký tự</small>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Xác Nhận Mật Khẩu Mới <span class="text-danger">*</span></label>
                            <input type="password"
                                   class="form-control"
                                   id="password_confirmation"
                                   name="password_confirmation"
                                   required>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('user.profile.edit') }}" class="btn btn-outline-secondary">
                                <i class="bx bx-arrow-back"></i> Quay Lại
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-key"></i> Đổi Mật Khẩu
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Tips -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bx bx-shield-alt-2"></i> Mẹo Bảo Mật
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bx bx-check text-success me-2"></i>
                            Sử dụng mật khẩu có ít nhất 8 ký tự
                        </li>
                        <li class="mb-2">
                            <i class="bx bx-check text-success me-2"></i>
                            Kết hợp chữ hoa, chữ thường, số và ký tự đặc biệt
                        </li>
                        <li class="mb-2">
                            <i class="bx bx-check text-success me-2"></i>
                            Không sử dụng thông tin cá nhân trong mật khẩu
                        </li>
                        <li class="mb-0">
                            <i class="bx bx-check text-success me-2"></i>
                            Thay đổi mật khẩu định kỳ để bảo mật tài khoản
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
