@extends('admin.layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chỉnh sửa Khuyến mãi: {{ $promotion->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.promotions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.promotions.update', $promotion->promotion_id) }}" method="POST" id="promotionForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">Mã khuyến mãi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                           id="code" name="code" value="{{ old('code', $promotion->code) }}" 
                                           placeholder="VD: SUMMER2024" maxlength="20" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Mã duy nhất để khách hàng sử dụng</small>
                                </div>

                                <div class="form-group">
                                    <label for="name">Tên khuyến mãi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $promotion->name) }}" 
                                           placeholder="VD: Giảm giá mùa hè" maxlength="100" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="description">Mô tả</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3" 
                                              placeholder="Mô tả chi tiết về khuyến mãi">{{ old('description', $promotion->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="discount_type">Loại giảm giá <span class="text-danger">*</span></label>
                                    <select class="form-control @error('discount_type') is-invalid @enderror" 
                                            id="discount_type" name="discount_type" required>
                                        <option value="">Chọn loại giảm giá</option>
                                        <option value="Percentage" {{ old('discount_type', $promotion->discount_type) == 'Percentage' ? 'selected' : '' }}>Phần trăm (%)</option>
                                        <option value="Fixed Amount" {{ old('discount_type', $promotion->discount_type) == 'Fixed Amount' ? 'selected' : '' }}>Số tiền cố định (VNĐ)</option>
                                    </select>
                                    @error('discount_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="discount_value">Giá trị giảm <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('discount_value') is-invalid @enderror" 
                                               id="discount_value" name="discount_value" value="{{ old('discount_value', $promotion->discount_value) }}" 
                                               step="0.01" min="0" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="discount_unit">
                                                {{ $promotion->discount_type == 'Percentage' ? '%' : 'VNĐ' }}
                                            </span>
                                        </div>
                                    </div>
                                    @error('discount_value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="min_amount">Số tiền tối thiểu (VNĐ)</label>
                                    <input type="number" class="form-control @error('min_amount') is-invalid @enderror" 
                                           id="min_amount" name="min_amount" value="{{ old('min_amount', $promotion->min_amount) }}" 
                                           step="1000" min="0" placeholder="0">
                                    @error('min_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Để trống nếu không có yêu cầu tối thiểu</small>
                                </div>

                                <div class="form-group" id="max_discount_group" style="display: {{ $promotion->discount_type == 'Percentage' ? 'block' : 'none' }};">
                                    <label for="max_discount">Giảm giá tối đa (VNĐ)</label>
                                    <input type="number" class="form-control @error('max_discount') is-invalid @enderror" 
                                           id="max_discount" name="max_discount" value="{{ old('max_discount', $promotion->max_discount) }}" 
                                           step="1000" min="0" placeholder="0">
                                    @error('max_discount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Chỉ áp dụng cho giảm giá theo phần trăm</small>
                                </div>

                                <div class="form-group">
                                    <label for="usage_limit">Giới hạn sử dụng</label>
                                    <input type="number" class="form-control @error('usage_limit') is-invalid @enderror" 
                                           id="usage_limit" name="usage_limit" value="{{ old('usage_limit', $promotion->usage_limit) }}" 
                                           min="1" placeholder="0">
                                    @error('usage_limit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Để trống nếu không giới hạn số lần sử dụng</small>
                                </div>

                                <div class="form-group">
                                    <label for="start_date">Ngày bắt đầu <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" name="start_date" 
                                           value="{{ old('start_date', $promotion->start_date->format('Y-m-d\TH:i')) }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="end_date">Ngày kết thúc <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" name="end_date" 
                                           value="{{ old('end_date', $promotion->end_date->format('Y-m-d\TH:i')) }}" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" 
                                               {{ old('is_active', $promotion->is_active) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">Kích hoạt khuyến mãi</label>
                                    </div>
                                </div>

                                <!-- Usage Statistics -->
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle"></i> Thống kê sử dụng</h6>
                                    <small>
                                        <strong>Số lần đã sử dụng:</strong> {{ $promotion->used_count ?? 0 }}<br>
                                        @if($promotion->usage_limit)
                                            <strong>Còn lại:</strong> {{ $promotion->usage_limit - ($promotion->used_count ?? 0) }}<br>
                                        @endif
                                        <strong>Ngày tạo:</strong> {{ $promotion->created_at->format('d/m/Y H:i') }}<br>
                                        <strong>Cập nhật lần cuối:</strong> {{ $promotion->updated_at->format('d/m/Y H:i') }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Cập nhật khuyến mãi
                                </button>
                                <a href="{{ route('admin.promotions.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Hủy
                                </a>
                                <a href="{{ route('admin.promotions.show', $promotion->promotion_id) }}" class="btn btn-info">
                                    <i class="fas fa-eye"></i> Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Handle discount type change
    $('#discount_type').change(function() {
        const type = $(this).val();
        if (type === 'Percentage') {
            $('#discount_unit').text('%');
            $('#max_discount_group').show();
            $('#discount_value').attr('max', '100');
        } else if (type === 'Fixed Amount') {
            $('#discount_unit').text('VNĐ');
            $('#max_discount_group').hide();
            $('#discount_value').removeAttr('max');
        }
    });

    // Form validation
    $('#promotionForm').submit(function(e) {
        const startDate = new Date($('#start_date').val());
        const endDate = new Date($('#end_date').val());
        
        if (endDate <= startDate) {
            e.preventDefault();
            alert('Ngày kết thúc phải sau ngày bắt đầu!');
            return false;
        }
        
        // Check if promotion has been used
        const usedCount = {{ $promotion->used_count ?? 0 }};
        if (usedCount > 0) {
            if (!confirm('Khuyến mãi này đã được sử dụng ' + usedCount + ' lần. Bạn có chắc chắn muốn cập nhật?')) {
                e.preventDefault();
                return false;
            }
        }
    });
});
</script>
@endpush
@endsection