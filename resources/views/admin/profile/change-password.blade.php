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
                <label class="form-label">Mat khau hien tai</label>
                <input type="password" name="current_password" class="form-control" required>
                @error('current_password') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Mat khau moi</label>
                <input type="password" name="password" class="form-control" required>
                @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Xac nhan mat khau moi</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
        </div>

        <div class="mt-4">
            <button class="btn btn-primary">Cap nhat</button>
        </div>
    </form>
</div>
@endsection
