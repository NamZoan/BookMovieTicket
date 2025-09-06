@extends('admin.layouts.app')
@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-bold py-3 mb-2">
                            <span class="text-muted fw-light">Quản lý lịch chiếu /</span> Chỉnh sửa lịch chiếu
                        </h4>
                        <p class="text-muted">Cập nhật thông tin lịch chiếu trong hệ thống</p>
                    </div>
                    <a href="{{ route('admin.showtimes.index') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-arrow-back me-1"></i>Quay lại
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title text-dark mb-0">
                            <i class="bx bx-edit me-2"></i>Chỉnh sửa lịch chiếu
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h6 class="alert-heading">
                                    <i class="bx bx-error-circle me-2"></i>Vui lòng sửa các lỗi sau:
                                </h6>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bx bx-error-circle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Thông tin hiện tại -->
                        <div class="alert alert-info mb-4">
                            <h6 class="alert-heading">
                                <i class="bx bx-info-circle me-2"></i>Thông tin lịch chiếu hiện tại
                            </h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Phim:</strong> {{ $showtime->movie->title ?? 'N/A' }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Phòng chiếu:</strong> {{ $showtime->screen->screen_name ?? 'N/A' }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Ngày chiếu:</strong> {{ $showtime->show_date ? date('d/m/Y', strtotime($showtime->show_date)) : 'N/A' }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Giờ chiếu:</strong> {{ $showtime->show_time ?? 'N/A' }} - {{ $showtime->end_time ?? 'N/A' }}
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('admin.showtimes.update', $showtime->showtime_id) }}" method="POST" id="editShowtimeForm">
                            @csrf
                            @method('PUT')

                            <!-- Thông tin phim và phòng chiếu -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="movie_id" class="form-label">
                                        <i class="bx bx-movie me-1"></i>Chọn phim <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('movie_id') is-invalid @enderror"
                                            id="movie_id"
                                            name="movie_id"
                                            required>
                                        <option value="">Chọn phim</option>
                                        @foreach($movies as $movie)
                                            <option value="{{ $movie->movie_id }}"
                                                    {{ old('movie_id', $showtime->movie_id) == $movie->movie_id ? 'selected' : '' }}
                                                    data-duration="{{ $movie->duration }}">
                                                {{ $movie->title }} ({{ $movie->duration }} phút) - {{ $movie->status }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('movie_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="screen_id" class="form-label">
                                        <i class="bx bx-tv me-1"></i>Chọn phòng chiếu <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('screen_id') is-invalid @enderror"
                                            id="screen_id"
                                            name="screen_id"
                                            required>
                                        <option value="">Chọn phòng chiếu</option>
                                        @foreach($screens as $screen)
                                            <option value="{{ $screen->screen_id }}"
                                                    {{ old('screen_id', $showtime->screen_id) == $screen->screen_id ? 'selected' : '' }}
                                                    data-total-seats="{{ $screen->total_seats }}"
                                                    data-cinema="{{ $screen->cinema->name ?? 'N/A' }}">
                                                {{ $screen->screen_name }} - {{ $screen->cinema->name ?? 'N/A' }} ({{ $screen->total_seats }} ghế)
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('screen_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Thông tin thời gian -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label for="show_date" class="form-label">
                                        <i class="bx bx-calendar me-1"></i>Ngày chiếu <span class="text-danger">*</span>
                                    </label>
                                    <input type="date"
                                           class="form-control @error('show_date') is-invalid @enderror"
                                           id="show_date"
                                           name="show_date"
                                           value="{{ old('show_date', $showtime->show_date) }}"
                                           required>
                                    @error('show_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="show_time" class="form-label">
                                        <i class="bx bx-time me-1"></i>Giờ bắt đầu <span class="text-danger">*</span>
                                    </label>
                                    <input type="time"
                                           class="form-control @error('show_time') is-invalid @enderror"
                                           id="show_time"
                                           name="show_time"
                                           value="{{ old('show_time', $showtime->show_time) }}"
                                           required>
                                    @error('show_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="end_time" class="form-label">
                                        <i class="bx bx-time-five me-1"></i>Giờ kết thúc <span class="text-danger">*</span>
                                    </label>
                                    <input type="time"
                                           class="form-control @error('end_time') is-invalid @enderror"
                                           id="end_time"
                                           name="end_time"
                                           value="{{ old('end_time', $showtime->end_time) }}"
                                           required>
                                    @error('end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Thông tin giá vé và ghế -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="base_price" class="form-label">
                                        <i class="bx bx-money me-1"></i>Giá vé (VNĐ) <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number"
                                               class="form-control @error('base_price') is-invalid @enderror"
                                               id="base_price"
                                               name="base_price"
                                               value="{{ old('base_price', $showtime->base_price) }}"
                                               min="10000"
                                               max="500000"
                                               step="1000"
                                               required>
                                        <span class="input-group-text">VNĐ</span>
                                    </div>
                                    <small class="text-muted">Giá vé từ 10,000 - 500,000 VNĐ</small>
                                    @error('base_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="available_seats" class="form-label">
                                        <i class="bx bx-chair me-1"></i>Số ghế trống <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number"
                                               class="form-control @error('available_seats') is-invalid @enderror"
                                               id="available_seats"
                                               name="available_seats"
                                               value="{{ old('available_seats', $showtime->available_seats) }}"
                                               min="1"
                                               required>
                                        <span class="input-group-text">ghế</span>
                                    </div>
                                    <small class="text-muted" id="seatsInfo">Tổng số ghế: <span id="totalSeats">0</span></small>
                                    @error('available_seats')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Conflict Warning -->
                            <div id="conflictWarning" class="alert alert-warning d-none" role="alert">
                                <i class="bx bx-error-circle me-2"></i>
                                <span id="conflictMessage"></span>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2 pt-3 border-top">
                                <button type="submit" class="btn btn-warning" id="submitBtn">
                                    <i class="bx bx-save me-1"></i>Lưu thay đổi
                                </button>
                                <a href="{{ route('admin.showtimes.index') }}" class="btn btn-outline-secondary">
                                    <i class="bx bx-arrow-back me-1"></i>Quay lại
                                </a>
                                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                                    <i class="bx bx-trash me-1"></i>Xóa lịch chiếu
                                </button>
                            </div>
                        </form>

                        <!-- Form ẩn để xóa -->
                        <form id="deleteForm" action="{{ route('admin.showtimes.destroy', $showtime->showtime_id) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h6 class="card-title text-white mb-0">
                            <i class="bx bx-info-circle me-2"></i>Thông tin bổ sung
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial rounded bg-label-primary">
                                            <i class="bx bx-movie"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Thời lượng phim</h6>
                                        <small class="text-muted" id="movieDuration">0</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial rounded bg-label-success">
                                            <i class="bx bx-time"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Thời gian chiếu</h6>
                                        <small class="text-muted" id="showDuration">0</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial rounded bg-label-warning">
                                            <i class="bx bx-building"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Rạp chiếu</h6>
                                        <small class="text-muted" id="cinemaName">N/A</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial rounded bg-label-danger">
                                            <i class="bx bx-chair"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Tổng số ghế</h6>
                                        <small class="text-muted" id="totalSeatsInfo">0</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial rounded bg-label-{{ $showtime->status == 'active' ? 'success' : 'secondary' }}">
                                            <i class="bx bx-check-circle"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Trạng thái</h6>
                                        <small class="text-muted">
                                            <span class="badge bg-label-{{ $showtime->status == 'active' ? 'success' : 'secondary' }}">
                                                {{ $showtime->status == 'active' ? 'Hoạt động' : 'Không hoạt động' }}
                                            </span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="mt-4 pt-3 border-top">
                            <h6 class="mb-3">Thao tác nhanh</h6>
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-outline-primary btn-sm" id="autoCalculateBtn">
                                    <i class="bx bx-calculator me-1"></i>Tự động tính giờ kết thúc
                                </button>
                                <button type="button" class="btn btn-outline-info btn-sm" id="checkConflictBtn">
                                    <i class="bx bx-check-circle me-1"></i>Kiểm tra xung đột
                                </button>
                                <button type="button" class="btn btn-outline-success btn-sm" id="resetFormBtn">
                                    <i class="bx bx-refresh me-1"></i>Khôi phục ban đầu
                                </button>
                            </div>
                        </div>

                        <!-- Change History -->
                        <div class="mt-4 pt-3 border-top">
                            <h6 class="mb-3">Lịch sử thay đổi</h6>
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">Tạo lịch chiếu</h6>
                                        <p class="timeline-text text-muted">
                                            {{ $showtime->created_at ? $showtime->created_at->format('d/m/Y H:i') : 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                                @if($showtime->updated_at && $showtime->updated_at != $showtime->created_at)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-warning"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">Cập nhật lần cuối</h6>
                                        <p class="timeline-text text-muted">
                                            {{ $showtime->updated_at->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Khởi tạo giá trị ban đầu
document.addEventListener('DOMContentLoaded', function() {
    updateMovieInfo();
    updateScreenInfo();
    calculateShowDuration();
    
    // Lưu giá trị ban đầu để reset
    saveInitialValues();
});

// Lưu giá trị ban đầu
function saveInitialValues() {
    const form = document.getElementById('editShowtimeForm');
    const initialValues = {};
    
    form.querySelectorAll('input, select').forEach(field => {
        initialValues[field.name] = field.value;
    });
    
    window.initialFormValues = initialValues;
}

// Cập nhật thông tin phim khi chọn
document.getElementById('movie_id').addEventListener('change', function() {
    updateMovieInfo();
    calculateShowDuration();
});

// Cập nhật thông tin phòng chiếu khi chọn
document.getElementById('screen_id').addEventListener('change', function() {
    updateScreenInfo();
});

// Tính toán thời gian chiếu khi thay đổi giờ
document.getElementById('show_time').addEventListener('change', calculateShowDuration);
document.getElementById('end_time').addEventListener('change', calculateShowDuration);

// Tự động tính giờ kết thúc
document.getElementById('autoCalculateBtn').addEventListener('click', function() {
    autoCalculateEndTime();
});

// Kiểm tra xung đột
document.getElementById('checkConflictBtn').addEventListener('click', function() {
    checkScheduleConflict();
});

// Reset form
document.getElementById('resetFormBtn').addEventListener('click', function() {
    resetForm();
});

// Cập nhật thông tin phim
function updateMovieInfo() {
    const movieSelect = document.getElementById('movie_id');
    const selectedOption = movieSelect.options[movieSelect.selectedIndex];
    const duration = selectedOption ? selectedOption.dataset.duration : 0;

    document.getElementById('movieDuration').textContent = duration ? duration + ' phút' : '0';
}

// Cập nhật thông tin phòng chiếu
function updateScreenInfo() {
    const screenSelect = document.getElementById('screen_id');
    const selectedOption = screenSelect.options[screenSelect.selectedIndex];
    const totalSeats = selectedOption ? selectedOption.dataset.totalSeats : 0;
    const cinemaName = selectedOption ? selectedOption.dataset.cinema : 'N/A';

    document.getElementById('totalSeats').textContent = totalSeats;
    document.getElementById('totalSeatsInfo').textContent = totalSeats + ' ghế';
    document.getElementById('cinemaName').textContent = cinemaName;

    // Cập nhật max value cho available_seats
    const availableSeatsInput = document.getElementById('available_seats');
    availableSeatsInput.max = totalSeats;
}

// Tính toán thời gian chiếu
function calculateShowDuration() {
    const startTime = document.getElementById('show_time').value;
    const endTime = document.getElementById('end_time').value;

    if (startTime && endTime) {
        const start = new Date(`2000-01-01T${startTime}`);
        const end = new Date(`2000-01-01T${endTime}`);

        if (end > start) {
            const diffMs = end - start;
            const diffMinutes = Math.floor(diffMs / (1000 * 60));
            document.getElementById('showDuration').textContent = diffMinutes + ' phút';
        } else {
            document.getElementById('showDuration').textContent = 'Thời gian không hợp lệ';
        }
    } else {
        document.getElementById('showDuration').textContent = '0';
    }
}

// Tự động tính giờ kết thúc
function autoCalculateEndTime() {
    const movieId = document.getElementById('movie_id').value;
    const showTime = document.getElementById('show_time').value;

    if (!movieId || !showTime) {
        showAlert('Vui lòng chọn phim và giờ bắt đầu trước', 'warning');
        return;
    }

    const selectedOption = document.getElementById('movie_id').options[document.getElementById('movie_id').selectedIndex];
    const duration = selectedOption ? selectedOption.dataset.duration : 0;

    if (duration) {
        const startTime = new Date(`2000-01-01T${showTime}`);
        const endTime = new Date(startTime.getTime() + duration * 60000);
        document.getElementById('end_time').value = endTime.toTimeString().slice(0, 5);
        calculateShowDuration();
        showAlert('Đã tự động tính giờ kết thúc', 'success');
    }
}

// Kiểm tra xung đột lịch chiếu
function checkScheduleConflict() {
    const screenId = document.getElementById('screen_id').value;
    const showDate = document.getElementById('show_date').value;
    const showTime = document.getElementById('show_time').value;
    const endTime = document.getElementById('end_time').value;

    if (!screenId || !showDate || !showTime || !endTime) {
        showAlert('Vui lòng nhập đầy đủ thông tin trước khi kiểm tra', 'warning');
        return;
    }

    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    formData.append('screen_id', screenId);
    formData.append('show_date', showDate);
    formData.append('show_time', showTime);
    formData.append('end_time', endTime);
    formData.append('exclude_id', '{{ $showtime->showtime_id }}');

    fetch('/admin/showtimes/check-conflict', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const conflictWarning = document.getElementById('conflictWarning');
        const conflictMessage = document.getElementById('conflictMessage');
        
        if (data.has_conflict) {
            conflictWarning.className = 'alert alert-danger';
            conflictMessage.textContent = data.message;
            conflictWarning.classList.remove('d-none');
            showAlert(data.message, 'danger');
        } else {
            conflictWarning.className = 'alert alert-success';
            conflictMessage.textContent = data.message;
            conflictWarning.classList.remove('d-none');
            showAlert(data.message, 'success');
        }
    })
    .catch(error => {
        showAlert('Có lỗi xảy ra khi kiểm tra xung đột', 'danger');
    });
}

// Reset form về giá trị ban đầu
function resetForm() {
    if (confirm('Bạn có chắc chắn muốn khôi phục về giá trị ban đầu?')) {
        const form = document.getElementById('editShowtimeForm');
        const initialValues = window.initialFormValues;
        
        if (initialValues) {
            Object.keys(initialValues).forEach(fieldName => {
                const field = form.querySelector(`[name="${fieldName}"]`);
                if (field) {
                    field.value = initialValues[fieldName];
                }
            });
            
            updateMovieInfo();
            updateScreenInfo();
            calculateShowDuration();
            showAlert('Đã khôi phục về giá trị ban đầu', 'info');
        }
    }
}

// Validation form
document.getElementById('editShowtimeForm').addEventListener('submit', function(e) {
    let isValid = true;
    const requiredFields = this.querySelectorAll('[required]');

    // Kiểm tra các trường bắt buộc
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });

    // Kiểm tra thời gian
    const startTime = document.getElementById('show_time').value;
    const endTime = document.getElementById('end_time').value;

    if (startTime && endTime) {
        const start = new Date(`2000-01-01T${startTime}`);
        const end = new Date(`2000-01-01T${endTime}`);

        if (end <= start) {
            document.getElementById('end_time').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('end_time').classList.remove('is-invalid');
        }
    }

    // Kiểm tra số ghế
    const availableSeats = parseInt(document.getElementById('available_seats').value);
    const totalSeats = parseInt(document.getElementById('totalSeats').textContent);

    if (availableSeats > totalSeats) {
        document.getElementById('available_seats').classList.add('is-invalid');
        isValid = false;
    }

    if (!isValid) {
        e.preventDefault();
        // Scroll to first error
        const firstError = this.querySelector('.is-invalid');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
});

// Xác nhận xóa
function confirmDelete() {
    const movieTitle = document.getElementById('movie_id').options[document.getElementById('movie_id').selectedIndex]?.text || 'N/A';
    const showDate = document.getElementById('show_date').value;
    const showTime = document.getElementById('show_time').value;

    if (confirm(`Bạn có chắc chắn muốn xóa lịch chiếu phim "${movieTitle}" vào ngày ${showDate} lúc ${showTime}?`)) {
        document.getElementById('deleteForm').submit();
    }
}

// Real-time validation
document.querySelectorAll('input, select').forEach(element => {
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

// Show alert
function showAlert(message, type) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    // Remove existing alerts
    document.querySelectorAll('.alert:not(.alert-info)').forEach(alert => alert.remove());
    
    // Add new alert
    const form = document.getElementById('editShowtimeForm');
    form.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto dismiss after 3 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert:not(.alert-info)');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 3000);
}
</script>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-content {
    background: #f8f9fa;
    padding: 10px 15px;
    border-radius: 8px;
    border-left: 3px solid #dee2e6;
}

.timeline-title {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 5px;
    color: #495057;
}

.timeline-text {
    font-size: 12px;
    margin: 0;
}
</style>
@endpush