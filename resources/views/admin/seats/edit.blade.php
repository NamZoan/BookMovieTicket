@extends('admin.layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
                <div class="col-lg-12 mb-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title text-primary mb-0">Chỉnh sửa thông tin ghế</h5>
                        </div>
                        <div class="card-body">
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <form action="{{ route('admin.seats.update', $seat->seat_id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="screen_id" class="form-label">Phòng chiếu</label>
                                        <select class="form-select @error('screen_id') is-invalid @enderror"
                                                id="screen_id"
                                                name="screen_id"
                                                required>
                                            <option value="">Chọn phòng chiếu</option>
                                            @foreach($screens as $screen)
                                                <option value="{{ $screen->screen_id }}"
                                                    {{ old('screen_id', $seat->screen_id) == $screen->screen_id ? 'selected' : '' }}>
                                                    {{ $screen->screen_name }} - {{ $screen->cinema->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('screen_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="row_name" class="form-label">Hàng ghế</label>
                                        <input type="text"
                                               class="form-control @error('row_name') is-invalid @enderror"
                                               id="row_name"
                                               name="row_name"
                                               value="{{ old('row_name', $seat->row_name) }}"
                                               placeholder="VD: A, B, C,..."
                                               required>
                                        @error('row_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="seat_number" class="form-label">Số ghế</label>
                                        <input type="number"
                                               class="form-control @error('seat_number') is-invalid @enderror"
                                               id="seat_number"
                                               name="seat_number"
                                               value="{{ old('seat_number', $seat->seat_number) }}"
                                               min="1"
                                               placeholder="VD: 1, 2, 3,..."
                                               required>
                                        @error('seat_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="seat_type" class="form-label">Loại ghế</label>
                                        <select class="form-select @error('seat_type') is-invalid @enderror"
                                                id="seat_type"
                                                name="seat_type"
                                                required>
                                            <option value="">Chọn loại ghế</option>
                                            <option value="standard" {{ old('seat_type', $seat->seat_type) == 'standard' ? 'selected' : '' }}>Ghế thường</option>
                                            <option value="vip" {{ old('seat_type', $seat->seat_type) == 'vip' ? 'selected' : '' }}>Ghế VIP</option>
                                            <option value="couple" {{ old('seat_type', $seat->seat_type) == 'couple' ? 'selected' : '' }}>Ghế đôi</option>
                                        </select>
                                        @error('seat_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="bx bx-save me-1"></i> Lưu thay đổi
                                    </button>
                                    <a href="{{ route('admin.seats.index') }}" class="btn btn-outline-secondary">
                                        <i class="bx bx-arrow-back me-1"></i> Quay lại
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
