@extends('admin.layouts.app')

@section('content')
<div class="page-container">
    <div class="page-header">
        <div class="breadcrumb">
            <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <span class="breadcrumb-separator">/</span>
            <a href="{{ route('admin.food-items.index') }}" class="breadcrumb-item">
                <i class="fas fa-utensils"></i> Món ăn
            </a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-item active">{{ $item->name }}</span>
        </div>
        
        <div class="header-content">
            <div class="title-section">
                <h1><i class="fas fa-utensils"></i> {{ $item->name }}</h1>
                <p>Chi tiết thông tin món ăn</p>
            </div>
            <div class="action-section">
                <a href="{{ route('admin.food-items.edit', $item->item_id) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Chỉnh sửa
                </a>
                <a href="{{ route('admin.food-items.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="content-grid">
        <!-- Main Information Card -->
        <div class="main-info-card">
            <div class="card-header">
                <h2><i class="fas fa-info-circle"></i> Thông tin cơ bản</h2>
            </div>
            <div class="card-content">
                <div class="info-grid">
                    <div class="info-item">
                        <label class="info-label">ID món ăn</label>
                        <span class="info-value">{{ $item->item_id }}</span>
                    </div>
                    
                    <div class="info-item">
                        <label class="info-label">Tên món</label>
                        <span class="info-value name-value">{{ $item->name }}</span>
                    </div>
                    
                    <div class="info-item">
                        <label class="info-label">Danh mục</label>
                        <span class="category-badge category-{{ Str::slug($item->category) }}">
                            {{ $item->category }}
                        </span>
                    </div>
                    
                    <div class="info-item">
                        <label class="info-label">Giá</label>
                        <span class="price-value">{{ number_format($item->price, 0, ',', '.') }} VNĐ</span>
                    </div>
                    
                    <div class="info-item">
                        <label class="info-label">Trạng thái</label>
                        <span class="status-badge status-{{ $item->is_available ? 'available' : 'unavailable' }}">
                            {{ $item->is_available ? 'Có sẵn để bán' : 'Không có sẵn' }}
                        </span>
                    </div>
                    
                    <div class="info-item">
                        <label class="info-label">Ngày tạo</label>
                        <span class="info-value">{{ $item->created_at ? $item->created_at->format('d/m/Y H:i:s') : 'N/A' }}</span>
                    </div>
                    
                    @if($item->updated_at && $item->updated_at != $item->created_at)
                        <div class="info-item">
                            <label class="info-label">Cập nhật lần cuối</label>
                            <span class="info-value">{{ $item->updated_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Image Card -->
        <div class="image-card">
            <div class="card-header">
                <h2><i class="fas fa-image"></i> Hình ảnh</h2>
            </div>
            <div class="card-content">
                @if($item->image_url)
                    <div class="image-container">
                        <img src="{{ $item->image_url }}" 
                             alt="{{ $item->name }}" 
                             class="food-image"
                             onerror="this.src='/assets/img/no-image.png'">
                    </div>
                    <div class="image-actions">
                        <a href="{{ $item->image_url }}" 
                           target="_blank" 
                           class="btn btn-sm btn-info">
                            <i class="fas fa-external-link-alt"></i> Xem ảnh gốc
                        </a>
                    </div>
                @else
                    <div class="no-image">
                        <div class="no-image-icon">
                            <i class="fas fa-image"></i>
                        </div>
                        <p>Chưa có hình ảnh</p>
                        <a href="{{ route('admin.food-items.edit', $item->item_id) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Thêm hình ảnh
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Description Card -->
        @if($item->description)
            <div class="description-card">
                <div class="card-header">
                    <h2><i class="fas fa-align-left"></i> Mô tả</h2>
                </div>
                <div class="card-content">
                    <div class="description-content">
                        {{ $item->description }}
                    </div>
                </div>
            </div>
        @endif

        <!-- Actions Card -->
        <div class="actions-card">
            <div class="card-header">
                <h2><i class="fas fa-cogs"></i> Thao tác</h2>
            </div>
            <div class="card-content">
                <div class="action-buttons">
                    <a href="{{ route('admin.food-items.edit', $item->item_id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Chỉnh sửa
                    </a>
                    
                    <form action="{{ route('admin.food-items.toggle-status', $item->item_id) }}" 
                          method="POST" 
                          class="d-inline"
                          onsubmit="return confirm('Bạn có chắc muốn thay đổi trạng thái món này?')">
                        @csrf
                        <button type="submit" 
                                class="btn {{ $item->is_available ? 'btn-secondary' : 'btn-success' }}">
                            <i class="fas fa-{{ $item->is_available ? 'pause' : 'play' }}"></i>
                            {{ $item->is_available ? 'Vô hiệu hóa' : 'Kích hoạt' }}
                        </button>
                    </form>
                    
                    <form action="{{ route('admin.food-items.destroy', $item->item_id) }}" 
                          method="POST" 
                          class="d-inline"
                          onsubmit="return confirm('Bạn có chắc muốn xóa món này? Hành động này không thể hoàn tác!')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Xóa món
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.page-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.page-header {
    margin-bottom: 30px;
}

.breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 20px;
    font-size: 14px;
    flex-wrap: wrap;
}

.breadcrumb-item {
    color: #6c757d;
    text-decoration: none;
    transition: color 0.3s ease;
}

.breadcrumb-item:hover {
    color: #007bff;
}

.breadcrumb-item.active {
    color: #495057;
    font-weight: 600;
}

.breadcrumb-separator {
    color: #dee2e6;
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

.action-section {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.content-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 25px;
}

.main-info-card {
    grid-column: 1 / -1;
}

.description-card {
    grid-column: 1 / -1;
}

/* Card Styles */
.main-info-card,
.image-card,
.description-card,
.actions-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.card-header {
    background: #f8f9fa;
    padding: 20px 25px;
    border-bottom: 1px solid #e9ecef;
}

.card-header h2 {
    margin: 0;
    color: #2c3e50;
    font-size: 20px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 12px;
}

.card-header h2 i {
    color: #007bff;
}

.card-content {
    padding: 25px;
}

/* Info Grid */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.info-label {
    font-weight: 600;
    color: #6c757d;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    color: #2c3e50;
    font-size: 16px;
    font-weight: 500;
}

.name-value {
    font-size: 18px;
    font-weight: 600;
    color: #e74c3c;
}

.price-value {
    font-size: 20px;
    font-weight: 700;
    color: #28a745;
}

/* Badges */
.category-badge {
    padding: 8px 16px;
    border-radius: 25px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    display: inline-block;
    width: fit-content;
}

.category-popcorn { background: #fff3cd; color: #856404; }
.category-drinks { background: #d1ecf1; color: #0c5460; }
.category-snacks { background: #d4edda; color: #155724; }
.category-combo { background: #f8d7da; color: #721c24; }

.status-badge {
    padding: 8px 16px;
    border-radius: 25px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
    width: fit-content;
}

.status-available {
    background: #d4edda;
    color: #155724;
}

.status-unavailable {
    background: #f8d7da;
    color: #721c24;
}

/* Image Styles */
.image-container {
    text-align: center;
    margin-bottom: 20px;
}

.food-image {
    max-width: 100%;
    max-height: 300px;
    object-fit: cover;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.image-actions {
    text-align: center;
}

.no-image {
    text-align: center;
    padding: 40px 20px;
}

.no-image-icon {
    font-size: 64px;
    color: #dee2e6;
    margin-bottom: 15px;
}

.no-image p {
    color: #6c757d;
    margin-bottom: 20px;
    font-size: 16px;
}

/* Description */
.description-content {
    line-height: 1.6;
    color: #495057;
    font-size: 16px;
    white-space: pre-line;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.btn {
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
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

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-danger:hover {
    background: #c82333;
}

.btn-info {
    background: #17a2b8;
    color: white;
}

.btn-info:hover {
    background: #138496;
}

.btn-sm {
    padding: 8px 16px;
    font-size: 12px;
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
    .content-grid {
        grid-template-columns: 1fr;
    }
    
    .header-content {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .action-section {
        align-self: stretch;
    }
    
    .action-section .btn {
        flex: 1;
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
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn {
        justify-content: center;
    }
    
    .card-content {
        padding: 20px;
    }
    
    .breadcrumb {
        font-size: 12px;
    }
}
</style>
@endsection
