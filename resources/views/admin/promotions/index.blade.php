@extends('admin.layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quản lý Khuyến mãi</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Thêm khuyến mãi
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    <!-- Promotions Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="myTable">
                            <thead>
                                <tr>
                                    <th>Mã</th>
                                    <th>Tên</th>
                                    <th>Loại giảm giá</th>
                                    <th>Giá trị</th>
                                    <th>Thời gian hiệu lực</th>
                                    <th>Trạng thái</th>
                                    <th>Số lần sử dụng</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($promotions as $promotion)
                                <tr>
                                    <td>
                                        <span class="badge bg-info">{{ $promotion->code }}</span>
                                    </td>
                                    <td>{{ $promotion->name }}</td>
                                    <td>
                                        @if($promotion->discount_type == 'Percentage')
                                            <span class="badge bg-success">{{ $promotion->discount_value }}%</span>
                                        @else
                                            <span class="badge bg-warning">{{ number_format($promotion->discount_value) }} VNĐ</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($promotion->discount_type == 'Percentage')
                                            @if($promotion->max_discount)
                                                Tối đa: {{ number_format($promotion->max_discount) }} VNĐ
                                            @endif
                                        @endif
                                        @if($promotion->min_amount)
                                            <br>Tối thiểu: {{ number_format($promotion->min_amount) }} VNĐ
                                        @endif
                                    </td>
                                    <td>
                                        <small>
                                            <strong>Bắt đầu:</strong> {{ $promotion->start_date->format('d/m/Y H:i') }}<br>
                                            <strong>Kết thúc:</strong> {{ $promotion->end_date->format('d/m/Y H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($promotion->is_active)
                                            <span class="badge bg-success">Đang hoạt động</span>
                                        @else
                                            <span class="badge bg-secondary">Không hoạt động</span>
                                        @endif
                                        <br>
                                        @if($promotion->start_date > now())
                                            <span class="badge bg-info">Sắp diễn ra</span>
                                        @elseif($promotion->end_date < now())
                                            <span class="badge bg-danger">Đã hết hạn</span>
                                        @else
                                            <span class="badge bg-success">Đang hiệu lực</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($promotion->usage_limit)
                                            {{ $promotion->used_count ?? 0 }}/{{ $promotion->usage_limit }}
                                        @else
                                            {{ $promotion->used_count ?? 0 }} (Không giới hạn)
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.promotions.show', $promotion->promotion_id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.promotions.edit', $promotion->promotion_id) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.promotions.toggle-status', $promotion->promotion_id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $promotion->is_active ? 'btn-secondary' : 'btn-success' }}">
                                                    <i class="fas {{ $promotion->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.promotions.destroy', $promotion->promotion_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa khuyến mãi này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" {{ $promotion->used_count > 0 ? 'disabled' : '' }}>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Không có khuyến mãi nào</td>
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

@if(session('success'))
    <script>
        toastr.success("{{ session('success') }}");
    </script>
@endif

@if(session('error'))
    <script>
        toastr.error("{{ session('error') }}");
    </script>
@endif
@endsection
@push('scripts')
    <script>

let table = new DataTable('#myTable');
    </script>
@endpush
