@extends('admin.layouts.app')

@section('content')
<div class="container-xxl py-4">
    <h4>Chỉnh sửa người dùng: {{ $user->email }}</h4>

    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="mt-3">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Họ tên</label>
                <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $user->full_name) }}" required maxlength="150">
                @error('full_name') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Loại tài khoản</label>
                <select name="user_type" class="form-select" required>
                    @foreach($types as $t)
                        <option value="{{ $t }}" @selected(old('user_type', $user->user_type) == $t)>{{ $t }}</option>
                    @endforeach
                </select>
                @error('user_type') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">Trạng thái</label>
                <select name="is_active" class="form-select">
                    <option value="1" @selected(old('is_active', $user->is_active) == 1)>Active</option>
                    <option value="0" @selected(old('is_active', $user->is_active) == 0)>Inactive</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Điểm thân thiết</label>
                <input type="number" name="loyalty_points" class="form-control" value="{{ old('loyalty_points', $user->loyalty_points ?? 0) }}" min="0">
            </div>

            <div class="col-md-4">
                <label class="form-label">Đổi mật khẩu (tùy chọn)</label>
                <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu mới nếu muốn">
                <input type="password" name="password_confirmation" class="form-control mt-2" placeholder="Xác nhận mật khẩu mới">
                @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mt-4">
            <button class="btn btn-primary">Lưu thay đổi</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>
@endsection