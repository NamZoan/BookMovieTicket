@extends('client.layouts.app')

@section('title', 'Thông Tin Cá Nhân')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Thông Tin Cá Nhân</h2>
                    <p class="text-muted mb-0">Quản lý thông tin cá nhân của bạn</p>
                </div>
                <div>
                    <a href="{{ route('user.bookings.index') }}" class="btn btn-outline-primary">
                        <i class="bx bx-movie"></i> Lịch Sử Đặt Vé
                    </a>
                </div>
            </div>

            <!-- Profile Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Cập Nhật Thông Tin</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bx bx-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('user.profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="full_name" class="form-label">Họ và Tên <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('full_name') is-invalid @enderror"
                                           id="full_name"
                                           name="full_name"
                                           value="{{ old('full_name', $user->full_name) }}"
                                           required>
                                    @error('full_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email"
                                           class="form-control"
                                           id="email"
                                           value="{{ $user->email }}"
                                           disabled>
                                    <small class="text-muted">Email không thể thay đổi</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Số Điện Thoại</label>
                                    <input type="text"
                                           class="form-control @error('phone') is-invalid @enderror"
                                           id="phone"
                                           name="phone"
                                           value="{{ old('phone', $user->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date_of_birth" class="form-label">Ngày Sinh</label>
                                    <input type="date"
                                           class="form-control @error('date_of_birth') is-invalid @enderror"
                                           id="date_of_birth"
                                           name="date_of_birth"
                                           value="{{ old('date_of_birth', $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '') }}">
                                    @error('date_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="gender" class="form-label">Giới Tính</label>
                                    <select class="form-select @error('gender') is-invalid @enderror"
                                            id="gender"
                                            name="gender">
                                        <option value="">Chọn giới tính</option>
                                        <option value="Male" {{ old('gender', $user->gender) == 'Male' ? 'selected' : '' }}>Nam</option>
                                        <option value="Female" {{ old('gender', $user->gender) == 'Female' ? 'selected' : '' }}>Nữ</option>
                                        <option value="Other" {{ old('gender', $user->gender) == 'Other' ? 'selected' : '' }}>Khác</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="loyalty_points" class="form-label">Điểm Tích Lũy</label>
                                    <input type="text"
                                           class="form-control"
                                           value="{{ number_format($user->loyalty_points ?? 0, 0, ',', '.') }} điểm"
                                           disabled>
                                    <small class="text-muted">Điểm tích lũy từ các giao dịch</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Địa Chỉ</label>
                            <textarea class="form-control @error('address') is-invalid @enderror"
                                      id="address"
                                      name="address"
                                      rows="3"
                                      placeholder="Nhập địa chỉ của bạn">{{ old('address', $user->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('user.change-password') }}" class="btn btn-outline-warning">
                                <i class="bx bx-key"></i> Đổi Mật Khẩu
                            </a>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save"></i> Lưu Thay Đổi
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Account Information -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Thông Tin Tài Khoản</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Loại Tài Khoản</label>
                                <div>
                                    @if($user->user_type == 'Customer')
                                        <span class="badge bg-primary">Khách Hàng</span>
                                    @elseif($user->user_type == 'Admin')
                                        <span class="badge bg-danger">Quản Trị Viên</span>
                                    @else
                                        <span class="badge bg-info">{{ $user->user_type }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Trạng Thái</label>
                                <div>
                                    @if($user->is_active)
                                        <span class="badge bg-success">Hoạt Động</span>
                                    @else
                                        <span class="badge bg-danger">Bị Khóa</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Ngày Tạo Tài Khoản</label>
                                <div>{{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Cập Nhật Lần Cuối</label>
                                <div>{{ $user->updated_at ? $user->updated_at->format('d/m/Y H:i') : 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
