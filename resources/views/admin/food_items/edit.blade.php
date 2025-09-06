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
            <a href="{{ route('admin.food-items.show', $item->item_id) }}" class="breadcrumb-item">
                {{ $item->name }}
            </a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-item active">Chỉnh sửa</span>
        </div>
        
        <div class="page-title">
            <h1><i class="fas fa-edit"></i> Chỉnh sửa món ăn</h1>
            <p>Cập nhật thông tin cho món "{{ $item->name }}"</p>
        </div>
    </div>

    <div class="content-card">
        @include('admin.food_items._form', [
            'item' => $item,
            'categories' => $categories,
        ])
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

.page-title h1 {
    margin: 0 0 8px 0;
    color: #2c3e50;
    font-size: 28px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 12px;
}

.page-title h1 i {
    color: #ffc107;
}

.page-title p {
    margin: 0;
    color: #6c757d;
    font-size: 16px;
}

.content-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 30px;
}

@media (max-width: 768px) {
    .page-container {
        padding: 15px;
    }
    
    .page-title h1 {
        font-size: 24px;
    }
    
    .content-card {
        padding: 20px;
    }
    
    .breadcrumb {
        font-size: 12px;
    }
}
</style>
@endsection
