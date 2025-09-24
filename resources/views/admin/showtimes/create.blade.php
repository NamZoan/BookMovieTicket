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
                            <span class="text-muted fw-light">Quản lý lịch chiếu /</span> Thêm lịch chiếu mới
                        </h4>
                        <p class="text-muted">Tạo lịch chiếu mới cho phim trong hệ thống</p>
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
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title text-white mb-0">
                            <i class="bx bx-plus-circle me-2"></i>Thông tin lịch chiếu
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

                        <form action="{{ route('admin.showtimes.store') }}" method="POST" id="createShowtimeForm">
                            @csrf

                            <!-- Thông tin phim và phòng chiếu -->
                            <div class="row mb-4">
                        <div class="col-md-6">
                                    <label for="movie_id" class="form-label">
                                        <i class="bx bx-movie me-1"></i>Chọn phim <span class="text-danger">*</span>
                                    </label>
                                    <select name="movie_id" id="movie_id" class="form-select @error('movie_id') is-invalid @enderror" required>
                                <option value="">-- Chọn phim --</option>
                                @foreach($movies as $movie)
                                        <option value="{{ $movie->movie_id }}"
                                                data-duration="{{ $movie->duration }}"
                                                data-status="{{ $movie->status }}">
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
                                    <select name="screen_id" id="screen_id" class="form-select @error('screen_id') is-invalid @enderror" required>
                                <option value="">-- Chọn phòng chiếu --</option>
                                @foreach($screens as $screen)
                                <option value="{{ $screen->screen_id }}"
                                                data-seats="{{ $screen->total_seats }}"
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
                                    <input type="date" name="show_date" id="show_date" class="form-control @error('show_date') is-invalid @enderror"
                                   min="{{ date('Y-m-d') }}" required>
                                    @error('show_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                        </div>

                        <div class="col-md-4">
                                    <label for="show_time" class="form-label">
                                        <i class="bx bx-time me-1"></i>Giờ bắt đầu <span class="text-danger">*</span>
                                    </label>
                                    <input type="time" name="show_time" id="show_time" class="form-control @error('show_time') is-invalid @enderror" required>
                                    @error('show_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                        </div>

                        <div class="col-md-4">
                                    <label for="end_time" class="form-label">
                                        <i class="bx bx-time-five me-1"></i>Giờ kết thúc <span class="text-danger">*</span>
                                    </label>
                                    <input type="time" name="end_time" id="end_time" class="form-control @error('end_time') is-invalid @enderror" required>
                                    @error('end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                        </div>
                    </div>

                            <!-- Thông tin giá vé và ghế -->
                            <div class="row mb-4">
                        <div class="col-md-4">
        <label for="price_seat_normal" class="form-label">
            <i class="bx bx-chair me-1"></i>Giá ghế thường <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <input type="number" name="price_seat_normal" id="price_seat_normal"
                   class="form-control @error('price_seat_normal') is-invalid @enderror"
                   min="10000" max="500000" step="1000"
                   value="{{ old('price_seat_normal') }}" required>
            <span class="input-group-text">VNĐ</span>
        </div>
        @error('price_seat_normal')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="price_seat_vip" class="form-label">
            <i class="bx bx-chair me-1"></i>Giá ghế VIP <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <input type="number" name="price_seat_vip" id="price_seat_vip"
                   class="form-control @error('price_seat_vip') is-invalid @enderror"
                   min="10000" max="500000" step="1000"
                   value="{{ old('price_seat_vip') }}" required>
            <span class="input-group-text">VNĐ</span>
        </div>
        @error('price_seat_vip')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="price_seat_couple" class="form-label">
            <i class="bx bx-chair me-1"></i>Giá ghế đôi <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <input type="number" name="price_seat_couple" id="price_seat_couple"
                   class="form-control @error('price_seat_couple') is-invalid @enderror"
                   min="10000" max="500000" step="1000"
                   value="{{ old('price_seat_couple') }}" required>
            <span class="input-group-text">VNĐ</span>
        </div>
        @error('price_seat_couple')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

                            <!-- Thêm trường ẩn available_seats -->
                            <div class="row mb-4">
                                <input type="hidden" name="available_seats" id="available_seats" value="">
                            </div>

                            <!-- Conflict Warning -->
                            <div id="conflictWarning" class="alert alert-warning d-none" role="alert">
                                <i class="bx bx-error-circle me-2"></i>
                                <span id="conflictMessage"></span>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2 pt-3 border-top">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="bx bx-save me-1"></i>Lưu lịch chiếu
                                </button>
                                <a href="{{ route('admin.showtimes.index') }}" class="btn btn-outline-secondary">
                                    <i class="bx bx-x me-1"></i>Hủy bỏ
                                </a>
                            </div>
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
                                        <small class="text-muted" id="movieDuration">Chưa chọn phim</small>
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
                                        <small class="text-muted" id="showDuration">Chưa nhập thời gian</small>
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
                                        <small class="text-muted" id="cinemaName">Chưa chọn phòng</small>
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
                                        <small class="text-muted" id="totalSeatsInfo">Chưa chọn phòng</small>
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
$(document).ready(function() {
    // Cập nhật thông tin khi chọn phòng chiếu
    $('#screen_id').change(function() {
        updateScreenInfo();
    });

    // Cập nhật thông tin khi chọn phim
    $('#movie_id').change(function() {
        updateMovieInfo();
    });

    // Tính toán thời gian chiếu
    $('#show_time, #end_time').change(function() {
        calculateShowDuration();
    });

    // Tự động tính giờ kết thúc
    $('#autoCalculateBtn').click(function() {
        autoCalculateEndTime();
    });

    // Kiểm tra xung đột
    $('#checkConflictBtn').click(function() {
        checkScheduleConflict();
    });

    // Real-time validation
    $('#createShowtimeForm input, #createShowtimeForm select').on('blur', function() {
        validateField(this);
    });

    // Form submission
    $('#createShowtimeForm').on('submit', function(e) {
        if (!validateForm() || !validatePrices()) {
            e.preventDefault();
        }
    });
});

// Cập nhật thông tin phòng chiếu
function updateScreenInfo() {
    const screenSelect = $('#screen_id');
    const selectedOption = screenSelect.find(':selected');
    const totalSeats = selectedOption.data('seats') || 0;
    const cinemaName = selectedOption.data('cinema') || 'N/A';

    // Cập nhật available_seats bằng total_seats
    $('#available_seats').val(totalSeats);
    $('#totalSeatsInfo').text(totalSeats + ' ghế');
    $('#cinemaName').text(cinemaName);
}

// Cập nhật thông tin phim
function updateMovieInfo() {
    const movieSelect = $('#movie_id');
    const selectedOption = movieSelect.find(':selected');
    const duration = selectedOption.data('duration') || 0;

    $('#movieDuration').text(duration ? duration + ' phút' : 'Chưa chọn phim');
}

// Tính toán thời gian chiếu
function calculateShowDuration() {
    const startTime = $('#show_time').val();
    const endTime = $('#end_time').val();

    if (startTime && endTime) {
        const start = new Date(`2000-01-01T${startTime}`);
        const end = new Date(`2000-01-01T${endTime}`);

        if (end > start) {
            const diffMs = end - start;
            const diffMinutes = Math.floor(diffMs / (1000 * 60));
            $('#showDuration').text(diffMinutes + ' phút');
        } else {
            $('#showDuration').text('Thời gian không hợp lệ');
        }
    } else {
        $('#showDuration').text('Chưa nhập thời gian');
    }
}

// Tự động tính giờ kết thúc
function autoCalculateEndTime() {
    const movieId = $('#movie_id').val();
    const showTime = $('#show_time').val();

    if (!movieId || !showTime) {
        showAlert('Vui lòng chọn phim và giờ bắt đầu trước', 'warning');
        return;
    }

    const selectedOption = $('#movie_id option:selected');
    const duration = selectedOption.data('duration');

    if (duration) {
        const startTime = new Date(`2000-01-01T${showTime}`);
        const endTime = new Date(startTime.getTime() + duration * 60000);
        $('#end_time').val(endTime.toTimeString().slice(0, 5));
        calculateShowDuration();
        showAlert('Đã tự động tính giờ kết thúc', 'success');
    }
}

// Kiểm tra xung đột lịch chiếu
function checkScheduleConflict() {
    const screenId = $('#screen_id').val();
    const showDate = $('#show_date').val();
    const showTime = $('#show_time').val();
    const endTime = $('#end_time').val();

    if (!screenId || !showDate || !showTime || !endTime) {
        showAlert('Vui lòng nhập đầy đủ thông tin trước khi kiểm tra', 'warning');
        return;
    }

    $.ajax({
        url: '/admin/showtimes/check-conflict',
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            screen_id: screenId,
            show_date: showDate,
            show_time: showTime,
            end_time: endTime
        },
        success: function(response) {
            if (response.has_conflict) {
                $('#conflictWarning').removeClass('d-none').addClass('alert-danger');
                $('#conflictMessage').text(response.message);
                showAlert(response.message, 'danger');
            } else {
                $('#conflictWarning').removeClass('d-none alert-danger').addClass('alert-success');
                $('#conflictMessage').text(response.message);
                showAlert(response.message, 'success');
            }
        },
        error: function() {
            showAlert('Có lỗi xảy ra khi kiểm tra xung đột', 'danger');
        }
    });
}

// Validation field
function validateField(field) {
    const $field = $(field);
    const value = $field.val().trim();

    if ($field.attr('required') && !value) {
        $field.addClass('is-invalid');
        return false;
    } else {
        $field.removeClass('is-invalid');
        return true;
    }
}

// Validation form
function validateForm() {
    let isValid = true;
    const requiredFields = $('#createShowtimeForm [required]');

    requiredFields.each(function() {
        if (!validateField(this)) {
            isValid = false;
        }
    });

    // Kiểm tra thời gian
    const startTime = $('#show_time').val();
    const endTime = $('#end_time').val();

    if (startTime && endTime) {
        const start = new Date(`2000-01-01T${startTime}`);
        const end = new Date(`2000-01-01T${endTime}`);

        if (end <= start) {
            $('#end_time').addClass('is-invalid');
            isValid = false;
        }
    }

    // Kiểm tra số ghế
    const availableSeats = parseInt($('#available_seats').val());
    if (!availableSeats || availableSeats <= 0) {
        showAlert('Vui lòng chọn phòng chiếu', 'danger');
        $('#screen_id').addClass('is-invalid');
        isValid = false;
    }

    return isValid;
}

// Validation giá vé
function validatePrices() {
    const normalPrice = parseInt($('#price_seat_normal').val());
    const vipPrice = parseInt($('#price_seat_vip').val());
    const couplePrice = parseInt($('#price_seat_couple').val());

    if (vipPrice <= normalPrice) {
        showAlert('Giá ghế VIP phải cao hơn giá ghế thường', 'danger');
        $('#price_seat_vip').addClass('is-invalid');
        return false;
    }

    if (couplePrice <= vipPrice) {
        showAlert('Giá ghế đôi phải cao hơn giá ghế VIP', 'danger');
        $('#price_seat_couple').addClass('is-invalid');
        return false;
    }

    return true;
}

// Show alert
function showAlert(message, type) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

    // Remove existing alerts
    $('.alert').remove();

    // Add new alert
    $('#createShowtimeForm').prepend(alertHtml);

    // Auto dismiss after 3 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 3000);
}
</script>
@endpush
