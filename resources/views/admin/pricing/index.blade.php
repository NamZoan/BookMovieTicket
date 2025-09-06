@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom-0 p-4">
                    <h3 class="card-title mb-0 text-muted">Quản Lý Giá Vé 🎟️</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.pricing.create') }}" class="btn btn-primary btn-sm rounded-pill">
                            <i class="fas fa-plus me-1"></i> Thêm mới
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="priceTable">
                            <thead class="bg-light">
                                <tr>
                                    <th scope="col" class="text-secondary fw-bold">ID</th>
                                    <th scope="col" class="text-secondary fw-bold">Loại ghế</th>
                                    <th scope="col" class="text-secondary fw-bold">Loại ngày</th>
                                    <th scope="col" class="text-secondary fw-bold">Khung giờ</th>
                                    <th scope="col" class="text-secondary fw-bold">Hệ số giá</th>
                                    <th scope="col" class="text-secondary fw-bold">Ngày tạo</th>
                                    <th scope="col" class="text-secondary fw-bold">Ngày cập nhật</th>
                                    <th scope="col" class="text-secondary fw-bold">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pricings as $pricing)
                                    <tr>
                                        <td>{{ $pricing->pricing_id }}</td>
                                        <td>
                                            @switch($pricing->seat_type)
                                                @case('Normal')
                                                    <span class="badge bg-secondary">Thường</span>
                                                    @break
                                                @case('VIP')
                                                    <span class="badge bg-warning text-dark">VIP</span>
                                                    @break
                                                @case('Couple')
                                                    <span class="badge bg-primary">Đôi</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>
                                            @switch($pricing->day_type)
                                                @case('Weekday')
                                                    <span class="badge bg-info text-dark">Ngày thường</span>
                                                    @break
                                                @case('Weekend')
                                                    <span class="badge bg-success">Cuối tuần</span>
                                                    @break
                                                @case('Holiday')
                                                    <span class="badge bg-danger">Ngày lễ</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>
                                            @switch($pricing->time_slot)
                                                @case('Morning')
                                                    <span class="badge bg-light text-dark">Sáng</span>
                                                    @break
                                                @case('Afternoon')
                                                    <span class="badge bg-secondary">Chiều</span>
                                                    @break
                                                @case('Evening')
                                                    <span class="badge bg-dark">Tối</span>
                                                    @break
                                                @case('Late Night')
                                                    <span class="badge bg-dark">Đêm khuya</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td><span class="text-success fw-bold">{{ $pricing->price_multiplier }}x</span></td>
                                        <td>{{ $pricing->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $pricing->updated_at ? $pricing->updated_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('admin.pricing.show', $pricing->pricing_id) }}" class="btn btn-sm btn-outline-info" title="Xem chi tiết">
                                                    Xem
                                                </a>
                                                <a href="{{ route('admin.pricing.edit', $pricing->pricing_id) }}" class="btn btn-sm btn-outline-primary" title="Chỉnh sửa">
                                                    Sửa
                                                </a>
                                                <form action="{{ route('admin.pricing.destroy', $pricing->pricing_id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa?')" title="Xóa">
                                                        Xóa
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-5">
                                            <i class="fas fa-ticket-alt fa-2x mb-3 text-secondary"></i>
                                            <p class="mb-0">Không có dữ liệu giá vé nào được tìm thấy.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let table = new DataTable('#priceTable', {
            responsive: true,
            order: [[2, 'asc'], [3, 'asc']], // Sắp xếp theo loại ngày và khung giờ
            columnDefs: [
                { orderable: false, targets: 7 } // Cột thao tác không sắp xếp được (index 7)
            ],
        });
    });
</script>
@endpush
