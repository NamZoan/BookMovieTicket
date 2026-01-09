@extends('admin.layouts.app')

@section('content')
<div class="container-xxl py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Quản lý người dùng</h4>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Tạo người dùng</a>
    </div>

    <form class="row g-2 mb-3" method="GET" action="{{ route('admin.users.index') }}">
        <div class="col-auto">
            <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control" placeholder="Tìm tên hoặc email...">
        </div>
        <div class="col-auto">
            <button class="btn btn-outline-secondary">Tìm</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-link">Đặt lại</a>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Loại tài khoản</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <td>{{ $u->id }}</td>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td>{{ $u->user_type ?? 'Customer' }}</td>
                            <td>
                                @if($u->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $u->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-primary">Sửa</a>

                                <form action="{{ route('admin.users.destroy', $u) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Bạn có chắc muốn vô hiệu hóa tài khoản này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Vô hiệu</button>
                                </form>

                                <button class="btn btn-sm btn-outline-secondary" onclick="toggleActive({{ $u->id }}, this)">
                                    {{ $u->is_active ? 'Tắt' : 'Bật' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">Không có người dùng</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleActive(userId, btn) {
    btn.disabled = true;
    fetch("{{ url('admin/users') }}/" + userId + "/toggle-active", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({})
    }).then(r => r.json()).then(data => {
        if (data.ok) {
            location.reload();
        } else {
            alert('Có lỗi xảy ra');
            btn.disabled = false;
        }
    }).catch(()=> {
        alert('Lỗi mạng');
        btn.disabled = false;
    });
}
</script>
@endpush
