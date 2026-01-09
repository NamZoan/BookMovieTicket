@extends('admin.layouts.app')
@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title text-primary mb-0">
                            <i class="bx bx-edit me-2"></i>Chỉnh Sửa Phim
                        </h5>
                        <a href="{{ route('admin.movies.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bx bx-arrow-back me-1"></i>Quay lại
                        </a>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h6 class="alert-heading">Vui lòng sửa các lỗi sau:</h6>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('admin.movies.update', $movie->movie_id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="title" class="form-label">
                                        Tên Phim <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control @error('title') is-invalid @enderror"
                                           id="title"
                                           name="title"
                                           value="{{ old('title', $movie->title) }}"
                                           required
                                           placeholder="Nhập tên phim">
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="original_title" class="form-label">Tên Phim Gốc</label>
                                    <input type="text"
                                           class="form-control @error('original_title') is-invalid @enderror"
                                           id="original_title"
                                           name="original_title"
                                           value="{{ old('original_title', $movie->original_title) }}"
                                           placeholder="Nhập tên phim gốc">
                                    @error('original_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Mô Tả</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description"
                                          name="description"
                                          rows="4"
                                          placeholder="Nhập mô tả phim">{{ old('description', $movie->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-4">
                                    <label for="duration" class="form-label">
                                        Thời lượng phim (phút) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number"
                                           class="form-control @error('duration') is-invalid @enderror"
                                           id="duration"
                                           name="duration"
                                           value="{{ old('duration', $movie->duration) }}"
                                           required
                                           min="1"
                                           max="999"
                                           placeholder="120">
                                    @error('duration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label for="release_date" class="form-label">
                                        Ngày khởi chiếu <span class="text-danger">*</span>
                                    </label>
                                    <input type="date"
                                           class="form-control @error('release_date') is-invalid @enderror"
                                           id="release_date"
                                           name="release_date"
                                           value="{{ old('release_date', $movie->release_date) }}"
                                           required>
                                    @error('release_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label for="genre" class="form-label">Thể Loại</label>
                                    <select class="form-select @error('genre') is-invalid @enderror"
                                            id="genre"
                                            name="genre">
                                        <option value="">Chọn thể loại</option>
                                        <option value="Hành động" {{ old('genre', $movie->genre) == 'Hành động' ? 'selected' : '' }}>Hành động</option>
                                        <option value="Phiêu lưu" {{ old('genre', $movie->genre) == 'Phiêu lưu' ? 'selected' : '' }}>Phiêu lưu</option>
                                        <option value="Hài" {{ old('genre', $movie->genre) == 'Hài' ? 'selected' : '' }}>Hài</option>
                                        <option value="Chính kịch" {{ old('genre', $movie->genre) == 'Chính kịch' ? 'selected' : '' }}>Chính kịch</option>
                                        <option value="Tình cảm" {{ old('genre', $movie->genre) == 'Tình cảm' ? 'selected' : '' }}>Tình cảm</option>
                                        <option value="Kinh dị" {{ old('genre', $movie->genre) == 'Kinh dị' ? 'selected' : '' }}>Kinh dị</option>
                                        <option value="Giật gân" {{ old('genre', $movie->genre) == 'Giật gân' ? 'selected' : '' }}>Giật gân</option>
                                        <option value="Tâm lý tội phạm" {{ old('genre', $movie->genre) == 'Tâm lý tội phạm' ? 'selected' : '' }}>Tâm lý tội phạm</option>
                                        <option value="Khoa học viễn tưởng" {{ old('genre', $movie->genre) == 'Khoa học viễn tưởng' ? 'selected' : '' }}>Khoa học viễn tưởng</option>
                                        <option value="Giả tưởng" {{ old('genre', $movie->genre) == 'Giả tưởng' ? 'selected' : '' }}>Giả tưởng</option>
                                        <option value="Hoạt hình" {{ old('genre', $movie->genre) == 'Hoạt hình' ? 'selected' : '' }}>Hoạt hình</option>
                                        <option value="Gia đình" {{ old('genre', $movie->genre) == 'Gia đình' ? 'selected' : '' }}>Gia đình</option>
                                        <option value="Cổ trang / Lịch sử" {{ old('genre', $movie->genre) == 'Cổ trang / Lịch sử' ? 'selected' : '' }}>Cổ trang / Lịch sử</option>
                                        <option value="Chiến tranh" {{ old('genre', $movie->genre) == 'Chiến tranh' ? 'selected' : '' }}>Chiến tranh</option>
                                        <option value="Tài liệu" {{ old('genre', $movie->genre) == 'Tài liệu' ? 'selected' : '' }}>Tài liệu</option>
                                        <option value="Âm nhạc" {{ old('genre', $movie->genre) == 'Âm nhạc' ? 'selected' : '' }}>Âm nhạc</option>
                                        <option value="Tiểu sử" {{ old('genre', $movie->genre) == 'Tiểu sử' ? 'selected' : '' }}>Tiểu sử</option>
                                        <option value="Hài - Lãng mạn" {{ old('genre', $movie->genre) == 'Hài - Lãng mạn' ? 'selected' : '' }}>Hài - Lãng mạn</option>
                                        <option value="Hành động - Viễn tưởng" {{ old('genre', $movie->genre) == 'Hành động - Viễn tưởng' ? 'selected' : '' }}>Hành động - Viễn tưởng</option>
                                        <option value="Kinh dị - Tâm lý" {{ old('genre', $movie->genre) == 'Kinh dị - Tâm lý' ? 'selected' : '' }}>Kinh dị - Tâm lý</option>
                                        <option value="Phiêu lưu - Gia đình" {{ old('genre', $movie->genre) == 'Phiêu lưu - Gia đình' ? 'selected' : '' }}>Phiêu lưu - Gia đình</option>
                                    </select>
                                    @error('genre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="director" class="form-label">Đạo Diễn</label>
                                    <input type="text"
                                           class="form-control @error('director') is-invalid @enderror"
                                           id="director"
                                           name="director"
                                           value="{{ old('director', $movie->director) }}"
                                           placeholder="Nhập tên đạo diễn">
                                    @error('director')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="cast" class="form-label">Diễn viên</label>
                                    <input type="text"
                                           class="form-control @error('cast') is-invalid @enderror"
                                           id="cast"
                                           name="cast"
                                           value="{{ old('cast', $movie->cast) }}"
                                           placeholder="Nhập tên diễn viên chính">
                                    @error('cast')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="poster_url" class="form-label">Poster</label>
                                        <div id="currentImage" class="mb-3 text-center">
                                            @if($movie->poster_url)
                                                <div class="position-relative d-inline-block">
                                                    <img src="{{ asset('storage/' . $movie->poster_url) }}"
                                                         alt="Current Movie Poster"
                                                         class="img-fluid rounded shadow-sm"
                                                         style="max-height: 300px; max-width: 100%;">
                                                    <div class="position-absolute top-0 end-0 p-2">
                                                        <span class="badge bg-success">Ảnh hiện tại</span>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-muted p-4 border rounded">
                                                    <i class="bx bx-image bx-lg"></i>
                                                    <p class="mb-0">Chưa có poster</p>
                                                </div>
                                            @endif
                                        </div>

                                        <div id="imagePreview" class="mb-3 text-center" style="display: none;">
                                            <h6 class="fw-bold text-primary mb-2">
                                                <i class="bx bx-image-add me-1"></i>Ảnh mới:
                                            </h6>
                                            <div class="position-relative d-inline-block">
                                                <img src="#" alt="New Movie Poster Preview"
                                                     class="img-fluid rounded shadow-sm"
                                                     style="max-height: 300px; max-width: 100%;">
                                                <div class="position-absolute top-0 end-0 p-2">
                                                    <span class="badge bg-primary">Ảnh mới</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="input-group">
                                            <input type="file"
                                                   class="form-control @error('poster_url') is-invalid @enderror"
                                                   id="poster_url"
                                                   name="poster_url"
                                                   accept="image/*"
                                                   onchange="previewImage(this)">
                                            <button class="btn btn-outline-secondary" type="button" onclick="clearImageInput()">
                                                <i class="bx bx-x"></i>
                                            </button>
                                        </div>

                                        <small class="text-muted">
                                            <i class="bx bx-info-circle me-1"></i>
                                            Để trống nếu không muốn thay đổi ảnh. Hỗ trợ: JPG, PNG, GIF (tối đa 5MB)
                                        </small>
                                        @error('poster_url')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Trạng thái</label>
                                        <select class="form-select @error('status') is-invalid @enderror"
                                                id="status"
                                                name="status">
                                            <option value="Coming Soon" {{ old('status', $movie->status) == 'Coming Soon' ? 'selected' : '' }}>
                                                <i class="bx bx-time me-1"></i>Coming Soon
                                            </option>
                                            <option value="Now Showing" {{ old('status', $movie->status) == 'Now Showing' ? 'selected' : '' }}>
                                                <i class="bx bx-play-circle me-1"></i>Now Showing
                                            </option>
                                            <option value="Ended" {{ old('status', $movie->status) == 'Ended' ? 'selected' : '' }}>
                                                <i class="bx bx-check-circle me-1"></i>Ended
                                            </option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Thông tin bổ sung</label>
                                        <div class="d-flex flex-column gap-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $movie->is_featured ?? false) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_featured">
                                                    Phim nổi bật
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="is_trending" name="is_trending" value="1" {{ old('is_trending', $movie->is_trending ?? false) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_trending">
                                                    Phim đang hot
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-top">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-save me-1"></i>Lưu thay đổi
                                    </button>
                                    <a href="{{ route('admin.movies.index') }}" class="btn btn-outline-secondary">
                                        <i class="bx bx-arrow-back me-1"></i>Quay lại
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewImage(input) {
        const currentImage = document.getElementById('currentImage');
        const preview = document.getElementById('imagePreview');
        const img = preview.querySelector('img');
        const file = input.files[0];

        if (file) {
            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('File quá lớn! Vui lòng chọn file nhỏ hơn 5MB.');
                input.value = '';
                return;
            }

            // Validate file type
            if (!file.type.startsWith('image/')) {
                alert('Vui lòng chọn file ảnh hợp lệ!');
                input.value = '';
                return;
            }

            const reader = new FileReader();

            reader.onload = function(e) {
                img.src = e.target.result;
                // Ẩn ảnh cũ và hiển thị ảnh mới
                currentImage.style.display = 'none';
                preview.style.display = 'block';
            }

            reader.readAsDataURL(file);
        } else {
            // Nếu không có file, hiển thị lại ảnh cũ
            img.src = '#';
            preview.style.display = 'none';
            currentImage.style.display = 'block';
        }
    }

    function clearImageInput() {
        const fileInput = document.getElementById('poster_url');
        const currentImage = document.getElementById('currentImage');
        const preview = document.getElementById('imagePreview');

        // Xóa file input
        fileInput.value = '';
        // Ẩn preview ảnh mới
        preview.style.display = 'none';
        // Hiển thị lại ảnh cũ
        currentImage.style.display = 'block';
    }



    // Form validation on submit
    document.querySelector('form').addEventListener('submit', function(e) {
        let isValid = true;
        const requiredFields = this.querySelectorAll('[required]');

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            // Scroll to first error
            const firstError = this.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });

    // Real-time validation
    document.querySelectorAll('input, select, textarea').forEach(element => {
        element.addEventListener('blur', function() {
            if (this.hasAttribute('required') && !this.value.trim()) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });

        element.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                this.classList.remove('is-invalid');
            }
        });
    });

    // Xử lý khi file input thay đổi
    document.getElementById('poster_url').addEventListener('change', function() {
        if (this.files.length === 0) {
            // Nếu không có file nào được chọn, hiển thị lại ảnh cũ
            const currentImage = document.getElementById('currentImage');
            const preview = document.getElementById('imagePreview');
            preview.style.display = 'none';
            currentImage.style.display = 'block';
        }
    });
</script>
@endpush
