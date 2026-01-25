@extends('admin.layouts.app')

@section('content')
<div class="container-xxl py-4">
    <h4>Thay doi mat khau</h4>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.password.update') }}" method="POST" class="mt-3">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Mật khẩu hiện tại</label>
                <input type="password" name="current_password" class="form-control" required>
                @error('current_password') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Mật khẩu mới</label>
                <input type="password" name="password" class="form-control" required>
                @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Xác nhận mật khẩu mới</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
        </div>

        <div class="mt-4">
            <button class="btn btn-primary">Cập nhật</button>
        </div>
    </form>
</div>
@endsection
