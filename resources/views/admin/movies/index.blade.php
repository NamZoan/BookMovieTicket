@extends('admin.layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4">Danh sách phim</h4>

            <div class="card">
                <div class="table-responsive text-nowrap">
                    <table class="table align-middle" id="myTable">
                        <thead>
                            <tr>
                                <th>Phim</th>
                                <th>Thời lượng</th>
                                <th>Trạng thái</th>
                                <th>Ngày phát hành</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach($movies as $movie)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @php
                                                $poster = $movie->display_image_url ?? ($movie->poster_url ? asset('storage/' . $movie->poster_url) : asset('assets/img/default/cinema.jpg'));
                                            @endphp
                                            <img src="{{ $poster }}" alt="Poster" class="rounded me-3" style="width: 48px; height: 48px; object-fit: cover;">
                                            <div>
                                                <div class="fw-medium">{{ $movie->title }}</div>
                                                <small class="text-muted">{{ $movie->original_title ?: '—' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $movie->duration }} phút</td>
                                    <td>
                                        <span
                                            class="badge bg-label-{{ $movie->status == 'Now Showing' ? 'success' : ($movie->status == 'Coming Soon' ? 'warning' : 'secondary') }}">
                                            {{ $movie->status }}
                                        </span>
                                    </td>
                                    <td>{{ $movie->release_date ? date('d/m/Y', strtotime($movie->release_date)) : 'N/A' }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.movies.edit', $movie->movie_id) }}">
                                                    <i class="bx bx-edit-alt me-1"></i> Chỉnh sửa
                                                </a>
                                                <a class="dropdown-item text-danger" href="javascript:void(0);"
                                                    onclick="event.preventDefault(); if(confirm('Bạn có chắc chắn muốn xóa?')) document.getElementById('delete-form-{{ $movie->movie_id }}').submit();">
                                                    <i class="bx bx-trash me-1"></i> Xóa
                                                </a>
                                            </div>
                                        </div>
                                        <form id="delete-form-{{ $movie->movie_id }}"
                                            action="{{ route('admin.movies.destroy', $movie->movie_id) }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                            @method('DELETE')
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
