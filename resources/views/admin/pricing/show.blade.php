@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom-0 py-3 px-4">
                    <h4 class="card-title mb-0 text-muted">Chi Tiết Giá Vé 🏷️</h4>
                    <div class="card-tools d-flex gap-2">
                        <a href="{{ route('admin.pricing.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                            <i class="fas fa-arrow-left me-1"></i> Quay lại
                        </a>
                        <a href="{{ route('admin.pricing.edit', $pricing->pricing_id) }}" class="btn btn-warning btn-sm rounded-pill">
                            <i class="fas fa-edit me-1"></i> Chỉnh sửa
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0">Thông tin cơ bản</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span class="fw-bold text-muted">ID:</span>
                                            <span>{{ $pricing->pricing_id }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span class="fw-bold text-muted">Loại ghế:</span>
                                            @switch($pricing->seat_type)
                                                @case('Normal')
                                                    <span class="badge bg-secondary">Thường</span>
                                                    @break
                                                @case('VIP')
                                                    <span class="badge bg-warning text-dark">VIP</span>
                                                    @break
                                                @case('Couple')
                                                    <span class="badge bg-success">Đôi</span>
                                                    @break
                                            @endswitch
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span class="fw-bold text-muted">Loại ngày:</span>
                                            @switch($pricing->day_type)
                                                @case('Weekday')
                                                    <span class="badge bg-info text-dark">Ngày thường</span>
                                                    @break
                                                @case('Weekend')
                                                    <span class="badge bg-primary">Cuối tuần</span>
                                                    @break
                                                @case('Holiday')
                                                    <span class="badge bg-danger">Ngày lễ</span>
                                                    @break
                                            @endswitch
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span class="fw-bold text-muted">Khung giờ:</span>
                                            @switch($pricing->time_slot)
                                                @case('Morning')
                                                    <span class="badge bg-light text-dark">Sáng (6:00 - 12:00)</span>
                                                    @break
                                                @case('Afternoon')
                                                    <span class="badge bg-secondary">Chiều (12:00 - 18:00)</span>
                                                    @break
                                                @case('Evening')
                                                    <span class="badge bg-dark">Tối (18:00 - 22:00)</span>
                                                    @break
                                                @case('Late Night')
                                                    <span class="badge bg-dark">Đêm khuya (22:00 - 6:00)</span>
                                                    @break
                                            @endswitch
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span class="fw-bold text-muted">Hệ số giá:</span>
                                            <span class="text-success fw-bold">{{ $pricing->price_multiplier }}x</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0 text-dark">Thông tin bổ sung</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span class="fw-bold text-muted">Ngày tạo:</span>
                                            <span>{{ $pricing->created_at->format('d/m/Y H:i:s') }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span class="fw-bold text-muted">Cập nhật lần cuối:</span>
                                            <span>{{ $pricing->updated_at->format('d/m/Y H:i:s') }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex justify-content-between p-4 border-top-0">
                    <div>
                        <a href="{{ route('admin.pricing.edit', $pricing->pricing_id) }}" class="btn btn-warning rounded-pill me-2">
                            <i class="fas fa-edit me-1"></i> Chỉnh sửa
                        </a>
                        <form action="{{ route('admin.pricing.destroy', $pricing->pricing_id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger rounded-pill" onclick="return confirm('Bạn có chắc chắn muốn xóa pricing này?')">
                                <i class="fas fa-trash-alt me-1"></i> Xóa
                            </button>
                        </form>
                    </div>
                    <a href="{{ route('admin.pricing.index') }}" class="btn btn-secondary rounded-pill">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

