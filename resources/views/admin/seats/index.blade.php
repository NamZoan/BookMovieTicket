@extends('admin.layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">Danh sách Ghế Ngồi</h4>
            <div>
                <a href="{{ route('admin.seats.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i>
                    Thêm ghế mới
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Vị trí ghế</th>
                            <th>Phòng chiếu</th>
                            <th>Rạp</th>
                            <th>Loại ghế</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($seats as $seat)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>Hàng {{ $seat->row_name }} - Số {{ $seat->seat_number }}</strong>
                                </td>
                                <td>{{ $seat->screen->screen_name }}</td>
                                <td>{{ $seat->screen->cinema->name }}</td>
                                <td>
                                    @if($seat->seat_type == 'VIP')
                                        <span class="badge bg-label-warning">VIP</span>
                                    @elseif($seat->seat_type == 'Couple')
                                        <span class="badge bg-label-info">Couple</span>
                                    @elseif ($seat->seat_type == 'Disabled')
                                        <span class="badge bg-label-secondary">Disabled</span>
                                    @else
                                        <span class="badge bg-label-primary">Standard</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('admin.seats.edit', $seat->seat_id) }}">
                                                <i class="bx bx-edit-alt me-1"></i> Chỉnh sửa
                                            </a>
                                            <a class="dropdown-item text-danger" href="javascript:void(0);"
                                               onclick="confirmDelete({{ $seat->seat_id }})">
                                                <i class="bx bx-trash me-1"></i> Xóa
                                            </a>
                                            <form id="delete-form-{{ $seat->seat_id }}"
                                                  action="{{ route('admin.seats.destroy', $seat->seat_id) }}"
                                                  method="POST"
                                                  style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="p-4">
                                        <i class="bx bx-chair mb-3 text-secondary" style="font-size: 40px;"></i>
                                        <p class="text-muted mb-0">Chưa có ghế nào</p>
                                        <a href="{{ route('admin.seats.create') }}" class="btn btn-primary mt-3">
                                            <i class="bx bx-plus me-1"></i>
                                            Thêm ghế mới ngay
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(seatId) {
        if (confirm('Bạn có chắc chắn muốn xóa ghế này?')) {
            document.getElementById('delete-form-' + seatId).submit();
        }
    }
</script>
@endpush

@endsection
