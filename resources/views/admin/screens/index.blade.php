@extends('admin.layouts.app')
@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">Danh sách phòng chiếu</h4>

        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table" id="myTable">
                    <thead>
                        <tr>
                            <th>Tên phòng</th>
                            <th>Rạp chiếu</th>
                            <th>Số ghế</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($screens as $screen)
                        <tr>
                            <td>{{ $screen->screen_name }}</td>
                            <td>{{ $screen->cinema->name }}</td>
                            <td>{{ $screen->total_seats }}</td>
                            <td>
                                <a href="{{ route('admin.screens.edit', $screen->screen_id) }}" class="btn btn-sm btn-outline-primary">Chỉnh sửa</a>
                                <form action="{{ route('admin.screens.destroy', $screen->screen_id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
@push('scripts')
<script>
    let table = new DataTable('#myTable');
</script>
@endpush
