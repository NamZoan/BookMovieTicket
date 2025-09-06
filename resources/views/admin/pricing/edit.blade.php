@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chỉnh sửa Pricing</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.pricing.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.pricing.update', $pricing->pricing_id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="seat_type">Loại ghế <span class="text-danger">*</span></label>
                                    <select name="seat_type" id="seat_type" class="form-control @error('seat_type') is-invalid @enderror" required>
                                        <option value="">Chọn loại ghế</option>
                                        <option value="Regular" {{ old('seat_type', $pricing->seat_type) == 'Regular' ? 'selected' : '' }}>Thường</option>
                                        <option value="VIP" {{ old('seat_type', $pricing->seat_type) == 'VIP' ? 'selected' : '' }}>VIP</option>
                                        <option value="Couple" {{ old('seat_type', $pricing->seat_type) == 'Couple' ? 'selected' : '' }}>Đôi</option>
                                    </select>
                                    @error('seat_type')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="day_type">Loại ngày <span class="text-danger">*</span></label>
                                    <select name="day_type" id="day_type" class="form-control @error('day_type') is-invalid @enderror" required>
                                        <option value="">Chọn loại ngày</option>
                                        <option value="Weekday" {{ old('day_type', $pricing->day_type) == 'Weekday' ? 'selected' : '' }}>Ngày thường</option>
                                        <option value="Weekend" {{ old('day_type', $pricing->day_type) == 'Weekend' ? 'selected' : '' }}>Cuối tuần</option>
                                        <option value="Holiday" {{ old('day_type', $pricing->day_type) == 'Holiday' ? 'selected' : '' }}>Ngày lễ</option>
                                    </select>
                                    @error('day_type')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="time_slot">Khung giờ <span class="text-danger">*</span></label>
                                    <select name="time_slot" id="time_slot" class="form-control @error('time_slot') is-invalid @enderror" required>
                                        <option value="">Chọn khung giờ</option>
                                        <option value="Morning" {{ old('time_slot', $pricing->time_slot) == 'Morning' ? 'selected' : '' }}>Sáng (6:00 - 12:00)</option>
                                        <option value="Afternoon" {{ old('time_slot', $pricing->time_slot) == 'Afternoon' ? 'selected' : '' }}>Chiều (12:00 - 18:00)</option>
                                        <option value="Evening" {{ old('time_slot', $pricing->time_slot) == 'Evening' ? 'selected' : '' }}>Tối (18:00 - 22:00)</option>
                                        <option value="Late Night" {{ old('time_slot', $pricing->time_slot) == 'Late Night' ? 'selected' : '' }}>Đêm khuya (22:00 - 6:00)</option>
                                    </select>
                                    @error('time_slot')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="price_multiplier">Hệ số giá <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="price_multiplier" id="price_multiplier"
                                               class="form-control @error('price_multiplier') is-invalid @enderror"
                                               value="{{ old('price_multiplier', $pricing->price_multiplier) }}"
                                               step="0.01" min="0.01" max="10" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">x</span>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Ví dụ: 1.0 = giá gốc, 1.5 = tăng 50%</small>
                                    @error('price_multiplier')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Cập nhật
                                    </button>
                                    <a href="{{ route('admin.pricing.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Hủy
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
