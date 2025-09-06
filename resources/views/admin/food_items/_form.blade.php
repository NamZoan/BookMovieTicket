@php($editing = isset($item))

<form action="{{ $editing ? route('admin.food-items.update', $item->item_id) : route('admin.food-items.store') }}"
    method="POST" enctype="multipart/form-data">

    @csrf
    @if ($editing)
        @method('PUT')
    @endif
    <div class="food-form-container">
        <div class="form-grid">
            <div class="form-group">
                <label for="name" class="form-label">Tên món ăn <span class="required">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $item->name ?? '') }}"
                    maxlength="100" required class="form-input" placeholder="Nhập tên món ăn...">
                @error('name')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Mô tả</label>
                <textarea id="description" name="description" rows="1" class="form-textarea"
                    placeholder="Mô tả chi tiết về món ăn...">{{ old('description', $item->description ?? '') }}</textarea>
                @error('description')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="price" class="form-label">Giá <span class="required">*</span></label>
                    <div class="price-input-wrapper">
                        <input type="number" id="price" name="price" step="0.01" min="0"
                            value="{{ old('price', $item->price ?? '') }}" required class="form-input price-input"
                            placeholder="0.00">
                        <span class="currency">VNĐ</span>
                    </div>
                    @error('price')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="category" class="form-label">Danh mục <span class="required">*</span></label>
                    <select id="category" name="category" required class="form-select">
                        <option value="">-- Chọn danh mục --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat }}" @selected(old('category', $item->category ?? '') === $cat)>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                    @error('category')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="image" class="form-label">Hình ảnh</label>
                <div class="image-upload-container">
                    <input type="file" id="image" name="image" accept="image/*" class="image-input"
                        onchange="previewImage(this)">
                    <div class="image-preview" id="imagePreview">
                        @if (isset($item) && $item->image_url)
                            <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="preview-img">
                        @else
                            <div class="no-image">
                                <i class="fas fa-image"></i>
                                <span>Chưa có hình ảnh</span>
                            </div>
                        @endif
                    </div>
                </div>
                @error('image')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="image_url" class="form-label">Hoặc URL hình ảnh</label>
                <input type="url" id="image_url" name="image_url"
                    value="{{ old('image_url', $item->image_url ?? '') }}" class="form-input"
                    placeholder="https://example.com/image.jpg">
                @error('image_url')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_available" value="1" class="form-checkbox"
                        {{ old('is_available', $item->is_available ?? true) ? 'checked' : '' }}>
                    <span class="checkmark"></span>
                    Có sẵn để bán
                </label>
                @error('is_available')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    {{ $editing ? 'Cập nhật' : 'Tạo mới' }}
                </button>
                <a href="{{ route('admin.food-items.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Hủy
                </a>
            </div>
        </div>
    </div>
</form>
<style>
    .food-form-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .form-grid {
        display: grid;
        gap: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-label {
        font-weight: 600;
        margin-bottom: 8px;
        color: #333;
    }

    .required {
        color: #dc3545;
    }

    .form-input,
    .form-textarea,
    .form-select {
        padding: 12px;
        border: 2px solid #e1e5e9;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .form-input:focus,
    .form-textarea:focus,
    .form-select:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
    }

    .form-textarea {
        resize: vertical;
        min-height: 100px;
    }

    .price-input-wrapper {
        position: relative;
    }

    .price-input {
        padding-right: 50px;
    }

    .currency {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        font-weight: 500;
    }

    .image-upload-container {
        display: grid;
        gap: 15px;
    }

    .image-input {
        padding: 10px;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        background: #f8f9fa;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .image-input:hover {
        border-color: #007bff;
        background: #e7f3ff;
    }

    .image-preview {
        min-height: 150px;
        border: 2px solid #e1e5e9;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background: #f8f9fa;
    }

    .preview-img {
        max-width: 100%;
        max-height: 150px;
        object-fit: cover;
    }

    .no-image {
        display: flex;
        flex-direction: column;
        align-items: center;
        color: #6c757d;
        font-size: 14px;
    }

    .no-image i {
        font-size: 32px;
        margin-bottom: 8px;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        cursor: pointer;
        font-weight: 500;
    }

    .form-checkbox {
        margin-right: 10px;
        transform: scale(1.2);
    }

    .form-actions {
        display: flex;
        gap: 15px;
        margin-top: 10px;
    }

    .btn {
        padding: 12px 24px;
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
        transform: translateY(-1px);
    }

    .error-message {
        color: #dc3545;
        font-size: 12px;
        margin-top: 5px;
        padding: 5px 10px;
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        border-radius: 4px;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn {
            justify-content: center;
        }
    }
</style>

<script>
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="preview-img">`;
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
