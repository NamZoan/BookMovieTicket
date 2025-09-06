@extends('admin.layouts.app')

@section('content')
    <div class="content-wrapper">

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="container-xxl flex-grow-1 container-p-y">


            <!-- Table -->
            @if($items->count() > 0)
                <div class="table-responsive">
                    <table id="foodItemsTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Hình ảnh</th>
                                <th>Tên món</th>
                                <th>Danh mục</th>
                                <th>Giá</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th class="actions-column">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td>{{ $item->item_id }}</td>
                                    <td>
                                        @if($item->image_url)
                                            <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="food-image"
                                                onerror="this.src='/assets/img/default/cinema.png'">
                                        @else
                                            <div class="no-image-placeholder">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $item->name }}</strong>
                                        @if($item->description)
                                            <div class="description">{{ Str::limit($item->description, 50) }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $item->category }}</td>
                                    <td>{{ number_format($item->price, 0, ',', '.') }} VNĐ</td>
                                    <td>
                                        <span class="status-badge status-{{ $item->is_available ? 'available' : 'unavailable' }}">
                                            {{ $item->is_available ? 'Có sẵn' : 'Không có sẵn' }}
                                        </span>
                                    </td>
                                    <td>{{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.food-items.show', $item->item_id) }}"
                                                class="btn btn-sm btn-info">
                                                <i class="fas fa-eye">Show</i>
                                            </a>
                                            <a href="{{ route('admin.food-items.edit', $item->item_id) }}"
                                                class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit">Sửa</i>
                                            </a>
                                            <form action="{{ route('admin.food-items.toggle-status', $item->item_id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-sm {{ $item->is_available ? 'btn-secondary' : 'btn-success' }}">
                                                    <i class="fas fa-{{ $item->is_available ? 'pause' : 'play' }}">Trạng Thái</i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.food-items.destroy', $item->item_id) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa món này?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash">Xóa</i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <h3>Chưa có món ăn nào</h3>
                    <a href="{{ route('admin.food-items.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Thêm món đầu tiên
                    </a>
                </div>
            @endif

        </div>
    </div>

    <style>
        .page-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
        }

        .title-section h1 {
            margin: 0 0 8px 0;
            color: #2c3e50;
            font-size: 32px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .title-section h1 i {
            color: #e74c3c;
        }

        .title-section p {
            margin: 0;
            color: #6c757d;
            font-size: 16px;
        }

        .content-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        /* Filters Section */
        .filters-section {
            padding: 25px;
            border-bottom: 1px solid #e9ecef;
            background: #f8f9fa;
        }

        .filters-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .search-group {
            display: flex;
            gap: 15px;
        }

        .search-input-wrapper {
            position: relative;
            flex: 1;
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .search-input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        .filter-group {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-select {
            padding: 10px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
            min-width: 150px;
        }

        /* Statistics */
        .stats-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            padding: 25px;
            background: #f8f9fa;
        }

        .stat-card {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            background: #007bff;
        }

        .stat-icon.available {
            background: #28a745;
        }

        .stat-icon.unavailable {
            background: #dc3545;
        }

        .stat-number {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
        }

        .stat-label {
            font-size: 14px;
            color: #6c757d;
        }

        /* Table */
        .table-container {
            padding: 25px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .data-table th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
        }

        .data-table td {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }

        .table-row:hover {
            background: #f8f9fa;
        }

        .item-id {
            font-weight: 600;
            color: #6c757d;
        }

        .food-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }

        .no-image-placeholder {
            width: 60px;
            height: 60px;
            background: #e9ecef;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }

        .name-wrapper strong {
            display: block;
            margin-bottom: 4px;
            color: #2c3e50;
        }

        .description {
            font-size: 12px;
            color: #6c757d;
        }

        .category-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .category-popcorn {
            background: #fff3cd;
            color: #856404;
        }

        .category-drinks {
            background: #d1ecf1;
            color: #0c5460;
        }

        .category-snacks {
            background: #d4edda;
            color: #155724;
        }

        .category-combo {
            background: #f8d7da;
            color: #721c24;
        }

        .price {
            font-weight: 600;
            color: #28a745;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-available {
            background: #d4edda;
            color: #155724;
        }

        .status-unavailable {
            background: #f8d7da;
            color: #721c24;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
        }

        .btn-sm {
            padding: 6px 10px;
            font-size: 11px;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background: #0056b3;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #545b62;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-success:hover {
            background: #1e7e34;
        }

        .btn-warning {
            background: #ffc107;
            color: #212529;
        }

        .btn-warning:hover {
            background: #e0a800;
        }

        .btn-info {
            background: #17a2b8;
            color: white;
        }

        .btn-info:hover {
            background: #138496;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .btn-outline {
            background: transparent;
            color: #6c757d;
            border: 2px solid #6c757d;
        }

        .btn-outline:hover {
            background: #6c757d;
            color: white;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-icon {
            font-size: 64px;
            color: #dee2e6;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            margin: 0 0 10px 0;
            color: #6c757d;
            font-size: 24px;
        }

        .empty-state p {
            margin: 0 0 25px 0;
            color: #6c757d;
            font-size: 16px;
        }

        /* Pagination */
        .pagination-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }

        /* Alert */
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert i {
            font-size: 18px;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .header-content {
                flex-direction: column;
                align-items: flex-start;
            }

            .action-section {
                align-self: stretch;
            }

            .action-section .btn {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            .page-container {
                padding: 15px;
            }

            .title-section h1 {
                font-size: 24px;
            }

            .filters-section,
            .stats-section,
            .table-container {
                padding: 20px;
            }

            .filter-group {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-select {
                min-width: auto;
            }

            .stats-section {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-direction: column;
            }

            .data-table {
                font-size: 12px;
            }

            .data-table th,
            .data-table td {
                padding: 10px 8px;
            }
        }
    </style>
@endsection

@push('scripts')
    <!-- DataTables CSS & JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#foodItemsTable').DataTable({
                "language": {
                    "sProcessing": "Đang xử lý...",
                    "sLengthMenu": "Hiển thị _MENU_ dòng",
                    "sZeroRecords": "Không tìm thấy dữ liệu",
                    "sInfo": "Hiển thị từ _START_ đến _END_ của _TOTAL_ dòng",
                    "sInfoEmpty": "Không có dữ liệu",
                    "sInfoFiltered": "(lọc từ _MAX_ dòng)",
                    "sSearch": "Tìm kiếm:",
                    "oPaginate": {
                        "sFirst": "Đầu",
                        "sPrevious": "Trước",
                        "sNext": "Tiếp",
                        "sLast": "Cuối"
                    }
                },
                "pageLength": 10,
                "order": [[0, "desc"]] // sắp xếp theo ID giảm dần
            });
        });
    </script>
@endpush
