@extends('admin.layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">Danh sách Rạp chiếu phim</h4>
            <a href="{{ route('admin.cinemas.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i>
                Thêm rạp mới
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover align-middle" id="cinemasTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Thông tin rạp</th>
                            <th>Địa chỉ</th>
                            <th>Thành phố</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($cinemas as $cinema)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <strong class="mb-1">{{ $cinema->name }}</strong>
                                        <span class="text-muted small">ID: {{ $cinema->cinema_id }}</span>
                                    </div>
                                </td>
                                <td>{{ $cinema->address }}</td>
                                <td>
                                    <span class="badge bg-label-primary">{{ $cinema->city }}</span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('admin.cinemas.edit', $cinema->cinema_id) }}">
                                                <i class="bx bx-edit-alt me-1"></i> Chỉnh sửa
                                            </a>
                                            <a class="dropdown-item text-danger" href="javascript:void(0);"
                                                onclick="confirmDelete({{ $cinema->cinema_id }})">
                                                <i class="bx bx-trash me-1"></i> Xóa
                                            </a>
                                            <form id="delete-form-{{ $cinema->cinema_id }}"
                                                action="{{ route('admin.cinemas.destroy', $cinema->cinema_id) }}"
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
                                <td colspan="6" class="text-center">
                                    <div class="p-4">
                                        <i class="bx bx-buildings mb-3 text-secondary" style="font-size: 40px;"></i>
                                        <p class="text-muted mb-0">Chưa có rạp chiếu phim nào</p>
                                        <a href="{{ route('admin.cinemas.create') }}" class="btn btn-primary mt-3">
                                            <i class="bx bx-plus me-1"></i>
                                            Thêm rạp mới ngay
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
    // Optional: enable DataTable for better UX
    if (window.DataTable) {
        new DataTable('#cinemasTable');
    }

    function confirmDelete(cinemaId) {
        if (confirm('Bạn có chắc chắn muốn xóa rạp chiếu phim này?')) {
            document.getElementById('delete-form-' + cinemaId).submit();
        }
    }
</script>
@endpush

@endsection
