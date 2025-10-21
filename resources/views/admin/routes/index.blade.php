@extends('admin.layouts.app')

@section('title', 'Quản Lý Route')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}">
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Hệ Thống /</span> Quản Lý Route
    </h4>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Danh Sách Route</h5>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#routeStats">
                            <i class="bx bx-stats"></i> Thống Kê
                        </button>
                    </div>
                </div>

                <div class="collapse" id="routeStats">
                    <div class="card-body border-bottom">
                        <div class="row g-3">
                            @foreach($groupedRoutes as $prefix => $routes)
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h6 class="card-title text-white">{{ $prefix ?: 'Root' }}</h6>
                                        <h2 class="card-text mb-0">{{ count($routes) }}</h2>
                                        <small>Routes</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                @foreach($groupedRoutes as $prefix => $routes)
                <div class="card-body">
                    <h6 class="fw-bold mb-3">{{ $prefix ?: 'Root Routes' }}</h6>
                    <div class="table-responsive">
                        <table class="table table-hover datatable-routes">
                            <thead>
                                <tr>
                                    <th>Method</th>
                                    <th>URI</th>
                                    <th>Name</th>
                                    <th>Action</th>
                                    <th>Domain</th>
                                    <th>Middleware</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($routes as $route)
                                <tr>
                                    <td>
                                        @foreach(explode('|', $route['method']) as $method)
                                            <span class="badge bg-label-{{
                                                $method == 'GET' ? 'primary' : (
                                                $method == 'POST' ? 'success' : (
                                                $method == 'PUT' ? 'warning' : (
                                                $method == 'DELETE' ? 'danger' : 'info'
                                                )))
                                            }} me-1">
                                                {{ $method }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td><code>{{ $route['uri'] }}</code></td>
                                    <td>
                                        @if($route['name'])
                                            <span class="badge bg-label-dark">{{ $route['name'] }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $route['action'] }}</small>
                                    </td>
                                    <td>{{ $route['domain'] }}</td>
                                    <td>
                                        @foreach(explode(', ', $route['middleware']) as $middleware)
                                            <span class="badge bg-label-secondary me-1">{{ $middleware }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
@endsection

@section('page-script')
<script>
$(function() {
    $('.datatable-routes').each(function() {
        $(this).DataTable({
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            lengthMenu: [10, 25, 50, 75, 100],
            pageLength: 10,
            responsive: true,
            order: [[1, 'asc']], // Sort by URI by default
        });
    });
});
</script>
@endsection
