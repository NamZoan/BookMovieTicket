@extends('admin.layouts.app')
@section('styles')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Custom CSS */
        #screen-screen {
            background: linear-gradient(to bottom, #e5e7eb, #d1d5db);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .seat {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            font-size: 10px;
            font-weight: bold;
            user-select: none;
        }

        .seat:hover {
            transform: scale(1.1);
            border-color: #3B82F6;
        }

        .seat.selected {
            border-color: #3B82F6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
        }

        .seat.Normal {
            background-color: #e5e7eb;
            color: #374151;
        }

        .seat.VIP {
            background-color: #f59e0b;
            color: white;
        }

        .seat.Couple {
            background-color: #ec4899;
            color: white;
        }

        .seat.Disabled {
            background-color: #ef4444;
            color: white;
        }

        .seat.delete-mode {
            border: 2px dashed #ef4444;
            background-color: #fee2e2 !important;
            cursor: pointer;
        }

        .seat.delete-mode:hover {
            background-color: #ef4444 !important;
            color: white;
            transform: scale(1.1);
        }

        .mode-btn.active {
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
        }

        .seat.empty-space {
            background-color: transparent !important;
            border: 2px dashed #d1d5db;
            color: #9ca3af;
            cursor: pointer;
        }

        .seat.empty-space:hover {
            background-color: #f3f4f6;
            border-color: #6b7280;
        }

        .seat-type-option.active {
            border-color: #3B82F6 !important;
            background-color: #eff6ff;
        }

        .seat-legend {
            width: 20px;
            height: 20px;
            border-radius: 4px;
        }

        .row-label {
            width: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #4B5563;
        }

        .screen-room-container {
            max-height: 500px;
            overflow-y: auto;
        }

        .dragging {
            opacity: 0.5;
        }

        .delete-row-btn {
            position: absolute;
            right: -30px;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0;
            transition: opacity 0.2s;
        }

        .row:hover .delete-row-btn {
            opacity: 1;
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
                <div class="col-lg-12 mb-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title text-primary mb-0">Chỉnh sửa phòng chiếu - {{ $screen->screen_name }}</h5>
                            <div class="text-muted">
                                <small>Cập nhật lần cuối: {{ $screen->updated_at->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <form action="{{ route('admin.screens.update', $screen->screen_id) }}" method="POST" id="screen-form">
                                @csrf
                                @method('PUT')

                                <div class="row mb-3">
                                    <div class="col-md-7 mb-3">
                                        <label for="screen_name" class="form-label">Tên phòng chiếu</label>
                                        <input type="text" class="form-control @error('screen_name') is-invalid @enderror"
                                               id="screen_name" name="screen_name" value="{{ old('screen_name', $screen->screen_name) }}"
                                               placeholder="VD: Phòng 1, Phòng 2, ..." required>
                                        @error('screen_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-5 mb-3">
                                        <label for="cinema_id" class="form-label">Chọn rạp chiếu</label>
                                        <select class="form-select @error('cinema_id') is-invalid @enderror"
                                                id="cinema_id" name="cinema_id" required>
                                            <option value="">Chọn rạp chiếu</option>
                                            @foreach ($cinemas as $cinema)
                                                <option value="{{ $cinema->cinema_id }}"
                                                        {{ old('cinema_id', $screen->cinema_id) == $cinema->cinema_id ? 'selected' : '' }}>
                                                    {{ $cinema->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('cinema_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Thông tin hiện tại -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="bg-info-subtle p-3 rounded">
                                            <h6 class="text-info mb-2">
                                                <i class="fas fa-info-circle me-1"></i> Thông tin hiện tại
                                            </h6>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <small class="text-muted">Tổng ghế hiện tại:</small>
                                                    <div class="fw-bold" id="current-total-seats">{{ $screen->total_seats ?? 0 }}</div>
                                                </div>
                                                <div class="col-md-3">
                                                    <small class="text-muted">Ghế thường:</small>
                                                    <div class="fw-bold text-secondary" id="current-normal-seats">{{ $screen->normal_seats ?? 0 }}</div>
                                                </div>
                                                <div class="col-md-3">
                                                    <small class="text-muted">Ghế VIP:</small>
                                                    <div class="fw-bold text-warning" id="current-vip-seats">{{ $screen->vip_seats ?? 0 }}</div>
                                                </div>
                                                <div class="col-md-3">
                                                    <small class="text-muted">Ghế đôi:</small>
                                                    <div class="fw-bold text-danger" id="current-couple-seats">{{ $screen->couple_seats ?? 0 }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <h2 class="text-lg font-semibold mb-4">Thiết kế ghế phòng chiếu</h2>

                                    <div class="bg-white p-4 rounded-lg shadow">
                                        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                                            <!-- Công cụ -->
                                            <div class="bg-gray-50 rounded-lg shadow p-4">
                                                <h3 class="text-md font-semibold mb-4 border-b pb-2">Công cụ thiết kế</h3>

                                                <!-- Loại ghế -->
                                                <div class="mb-4">
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Loại ghế</label>
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <div class="seat-type-option active border-2 rounded p-2 text-center cursor-pointer hover:bg-gray-100" data-type="Normal">
                                                            <div class="seat-legend bg-gray-300 mx-auto mb-1"></div>
                                                            <span class="text-xs">Thường</span>
                                                        </div>
                                                        <div class="seat-type-option border-2 rounded p-2 text-center cursor-pointer hover:bg-gray-100" data-type="VIP">
                                                            <div class="seat-legend bg-yellow-500 mx-auto mb-1"></div>
                                                            <span class="text-xs">VIP</span>
                                                        </div>
                                                        <div class="seat-type-option border-2 rounded p-2 text-center cursor-pointer hover:bg-gray-100" data-type="Couple">
                                                            <div class="seat-legend bg-pink-500 mx-auto mb-1"></div>
                                                            <span class="text-xs">Ghế đôi</span>
                                                        </div>
                                                        <div class="seat-type-option border-2 rounded p-2 text-center cursor-pointer hover:bg-gray-100" data-type="Disabled">
                                                            <div class="seat-legend bg-red-500 mx-auto mb-1"></div>
                                                            <span class="text-xs">Không sử dụng</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Thêm hàng ghế -->
                                                <div class="mb-4">
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Thêm hàng ghế</label>
                                                    <div class="flex mb-2">
                                                        <input type="number" min="1" max="20" value="10"
                                                               class="border rounded-l px-2 py-1 w-full text-sm" id="row-seats">
                                                        <button type="button" id="add-row"
                                                                class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-r">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                    <small class="text-gray-500">Số ghế mỗi hàng (1-20)</small>
                                                </div>

                                                <!-- Chế độ -->
                                                <div class="mb-4">
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Chế độ</label>
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <button type="button" id="mode-edit"
                                                                class="mode-btn active bg-blue-600 text-white px-3 py-2 rounded text-sm">
                                                            <i class="fas fa-edit me-1"></i> Sửa ghế
                                                        </button>
                                                        <button type="button" id="mode-delete"
                                                                class="mode-btn bg-red-600 text-white px-3 py-2 rounded text-sm">
                                                            <i class="fas fa-trash me-1"></i> Xóa ghế
                                                        </button>
                                                    </div>
                                                    <small class="text-gray-500 mt-1 block">
                                                        <span id="mode-instruction">Click ghế để thay đổi loại</span>
                                                    </small>
                                                </div>

                                                <!-- Thao tác -->
                                                <div class="mb-4">
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Thao tác nhanh</label>
                                                    <div class="space-y-2">
                                                        <button type="button" id="reset-layout"
                                                                class="w-full bg-orange-600 hover:bg-orange-700 text-white px-3 py-2 rounded text-sm">
                                                            <i class="fas fa-undo me-1"></i> Khôi phục ban đầu
                                                        </button>
                                                        <button type="button" id="clear-all"
                                                                class="w-full bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded text-sm">
                                                            <i class="fas fa-trash me-1"></i> Xóa tất cả
                                                        </button>
                                                        <button type="button" id="template-small"
                                                                class="w-full bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded text-sm">
                                                            <i class="fas fa-magic me-1"></i> Mẫu nhỏ (50 ghế)
                                                        </button>
                                                        <button type="button" id="template-large"
                                                                class="w-full bg-purple-600 hover:bg-purple-700 text-white px-3 py-2 rounded text-sm">
                                                            <i class="fas fa-magic me-1"></i> Mẫu lớn (100 ghế)
                                                        </button>
                                                    </div>
                                                </div>

                                                <!-- Thống kê ghế -->
                                                <div class="bg-white rounded p-3 border">
                                                    <h4 class="text-sm font-semibold mb-2">Thống kê ghế mới</h4>
                                                    <div class="text-xs space-y-1">
                                                        <div class="flex justify-between">
                                                            <span>Tổng số ghế:</span>
                                                            <span id="total-seats" class="font-bold">0</span>
                                                        </div>
                                                        <div class="flex justify-between">
                                                            <span>Ghế thường:</span>
                                                            <span id="normal-seats" class="text-gray-600">0</span>
                                                        </div>
                                                        <div class="flex justify-between">
                                                            <span>Ghế VIP:</span>
                                                            <span id="vip-seats" class="text-yellow-600">0</span>
                                                        </div>
                                                        <div class="flex justify-between">
                                                            <span>Ghế đôi:</span>
                                                            <span id="couple-seats" class="text-pink-600">0</span>
                                                        </div>
                                                        <div class="flex justify-between">
                                                            <span>Không sử dụng:</span>
                                                            <span id="disabled-seats" class="text-red-600">0</span>
                                                        </div>
                                                    </div>

                                                    <div class="mt-2 pt-2 border-t">
                                                        <div class="flex justify-between text-xs">
                                                            <span class="text-green-600">Thay đổi:</span>
                                                            <span id="seat-change" class="font-bold text-green-600">0</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Khu vực phòng chiếu -->
                                            <div class="lg:col-span-3">
                                                <div class="bg-white rounded-lg shadow p-4 relative">
                                                    <div id="loading-overlay" class="loading-overlay" style="display: none;">
                                                        <div class="text-center">
                                                            <div class="spinner-border text-primary" role="status">
                                                                <span class="visually-hidden">Đang tải...</span>
                                                            </div>
                                                            <div class="mt-2">Đang tải bố trí ghế...</div>
                                                        </div>
                                                    </div>

                                                    <div class="flex justify-between items-center mb-4">
                                                        <h3 class="text-md font-semibold">Bố trí ghế - <span id="room-name">{{ $screen->screen_name }}</span></h3>
                                                        <div class="text-sm text-gray-500">
                                                            <span id="edit-instruction">Chế độ sửa: Click ghế để thay đổi loại</span>
                                                            <span id="delete-instruction" style="display:none">Chế độ xóa: Click ghế để xóa</span>
                                                        </div>
                                                    </div>

                                                    <!-- Màn hình chiếu -->
                                                    <div id="screen-screen" class="w-full h-8 mb-8 rounded text-center text-sm font-medium flex items-center justify-center">
                                                        <i class="fas fa-tv me-2"></i> MÀN HÌNH CHIẾU PHIM
                                                    </div>

                                                    <!-- Khu vực bố trí ghế -->
                                                    <div class="screen-room-container">
                                                        <div id="screen-room" class="w-full p-4 bg-gray-50 rounded-lg relative min-h-32">
                                                            <div class="text-center text-gray-400 py-8" id="empty-message" style="display: none;">
                                                                <i class="fas fa-chair fa-3x mb-4"></i>
                                                                <p>Chưa có ghế nào. Hãy thêm hàng ghế đầu tiên!</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hidden input để lưu cấu hình ghế -->
                                <input type="hidden" name="seat_configuration" id="seat_configuration">

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="bx bx-save me-1"></i> Cập nhật phòng chiếu
                                    </button>
                                    <a href="{{ route('admin.screens.index') }}" class="btn btn-outline-secondary">
                                        <i class="bx bx-arrow-back me-1"></i> Quay lại
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden data for JavaScript -->
    <script type="application/json" id="existing-seat-data">
        {!! json_encode($screen->seat_configuration ?? []) !!}
    </script>
@endsection

@section('scripts')
y    <script>
        $(document).ready(function() {
            let currentSeatType = 'Normal';
            let currentMode = 'edit';
            let seatCounter = 1;
            let originalSeatConfiguration = null;

            // Seat classes mapping to database enum values
            const seatClasses = {
                Normal: 'Normal',
                VIP: 'VIP',
                Couple: 'Couple',
                Disabled: 'Disabled'
            };

            // Load existing seat configuration
            function loadExistingSeatLayout() {
                $('#loading-overlay').show();
                try {
                    const existingData = JSON.parse($('#existing-seat-data').text());
                    originalSeatConfiguration = JSON.parse(JSON.stringify(existingData)); // Deep copy
                    console.log('Existing seat configuration:', existingData);
                    console.log('Original seat configuration:', originalSeatConfiguration);
                    if (existingData && existingData.length > 0) {
                        renderSeatLayout(existingData);
                        $('#empty-message').hide();
                    } else {
                        $('#empty-message').show();
                    }
                } catch (error) {
                    console.error('Error loading seat configuration:', error);
                    $('#empty-message').show();
                }

                setTimeout(() => {
                    $('#loading-overlay').hide();
                    updateSeatCount();
                    updateSeatConfiguration();
                }, 500);
            }

            // Render seat layout from configuration
            function renderSeatLayout(configuration) {
                $('#screen-room').empty();

                configuration.forEach(rowData => {
                    const rowDiv = $(`<div class="row mb-3 flex justify-center items-center relative" data-row="${rowData.row}"></div>`);

                    // Add row label
                    const rowLabel = $(`<div class="row-label">${rowData.row}</div>`);
                    rowDiv.append(rowLabel);

                    // Find max position to create all seat positions
                    const maxPosition = Math.max(...rowData.seats.map(s => s.position || s.seat_number - 1));

                    // Create seats and empty spaces
                    for (let pos = 0; pos <= maxPosition; pos++) {
                        const existingSeat = rowData.seats.find(s => (s.position !== undefined ? s.position : s.seat_number - 1) === pos);

                        if (existingSeat) {
                            const seat = createSeat(existingSeat.id, existingSeat.type);
                            rowDiv.append(seat);
                        } else {
                            // Create empty space
                            const seatId = `${rowData.row}${pos + 1}`;
                            const emptySpace = createEmptySpace(seatId);
                            rowDiv.append(emptySpace);
                        }
                    }

                    // Add delete row button
                    const deleteBtn = createDeleteRowButton(rowData.row);
                    rowDiv.append(deleteBtn);

                    $('#screen-room').append(rowDiv);
                });
            }

            // Create delete row button
            function createDeleteRowButton(rowLetter) {
                const deleteBtn = $(`<button type="button" class="delete-row-btn bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 text-xs">
                    <i class="fas fa-times"></i>
                </button>`);

                deleteBtn.on('click', function() {
                    if (confirm(`Bạn có chắc muốn xóa hàng ${rowLetter}?`)) {
                        $(this).closest('.row').remove();
                        updateRowLabels();
                        updateSeatCount();
                        updateSeatConfiguration();

                        if ($('#screen-room .row').length === 0) {
                            $('#empty-message').show();
                        }
                    }
                });

                return deleteBtn;
            }

            // Reset to original layout
            $('#reset-layout').on('click', function() {
                if (confirm('Bạn có chắc muốn khôi phục về bố trí ghế ban đầu?')) {
                    $('#loading-overlay').show();

                    setTimeout(() => {
                        if (originalSeatConfiguration && originalSeatConfiguration.length > 0) {
                            renderSeatLayout(originalSeatConfiguration);
                            $('#empty-message').hide();
                        } else {
                            $('#screen-room').empty();
                            $('#empty-message').show();
                        }

                        updateModeUI();
                        updateSeatCount();
                        updateSeatConfiguration();
                        $('#loading-overlay').hide();
                    }, 300);
                }
            });

            // Update seat count with comparison
            function updateSeatCount() {
                const totalSeats = $('.seat:not(.empty-space)').length;
                const normalSeats = $('.seat.Normal').length;
                const vipSeats = $('.seat.VIP').length;
                const coupleSeats = $('.seat.Couple').length;
                const disabledSeats = $('.seat.Disabled').length;

                $('#total-seats').text(totalSeats);
                $('#normal-seats').text(normalSeats);
                $('#vip-seats').text(vipSeats);
                $('#couple-seats').text(coupleSeats);
                $('#disabled-seats').text(disabledSeats);

                // Calculate changes
                const currentTotalSeats = parseInt($('#current-total-seats').text()) || 0;
                const seatChange = totalSeats - currentTotalSeats;

                $('#seat-change').text(seatChange >= 0 ? `+${seatChange}` : seatChange);

                if (seatChange > 0) {
                    $('#seat-change').removeClass('text-red-600').addClass('text-green-600');
                } else if (seatChange < 0) {
                    $('#seat-change').removeClass('text-green-600').addClass('text-red-600');
                } else {
                    $('#seat-change').removeClass('text-green-600 text-red-600').addClass('text-gray-600');
                }
            }

            // Update tên phòng khi người dùng nhập
            $('#screen_name').on('input', function() {
                const name = $(this).val() || '{{ $screen->screen_name }}';
                $('#room-name').text(name);
            });

            // Xử lý chọn loại ghế
            $('.seat-type-option').on('click', function() {
                currentSeatType = $(this).data('type');
                $('.seat-type-option').removeClass('active');
                $(this).addClass('active');
            });

            // Xử lý chuyển đổi chế độ
            $('.mode-btn').on('click', function() {
                const mode = $(this).attr('id').replace('mode-', '');
                currentMode = mode;

                $('.mode-btn').removeClass('active');
                $(this).addClass('active');

                updateModeUI();

                if (mode === 'edit') {
                    $('#mode-instruction').text('Click ghế để thay đổi loại');
                    $('#edit-instruction').show();
                    $('#delete-instruction').hide();
                } else {
                    $('#mode-instruction').text('Click ghế để xóa');
                    $('#edit-instruction').hide();
                    $('#delete-instruction').show();
                }
            });

            // Cập nhật giao diện theo chế độ
            function updateModeUI() {
                if (currentMode === 'delete') {
                    $('.seat').addClass('delete-mode');
                    $('.seat-type-option').parent().parent().fadeOut();
                } else {
                    $('.seat').removeClass('delete-mode');
                    $('.seat-type-option').parent().parent().fadeIn();
                }
            }

            // Thêm hàng ghế
            $('#add-row').on('click', function() {
                const seatCount = parseInt($('#row-seats').val());
                if (seatCount < 1 || seatCount > 20) {
                    alert('Số ghế phải từ 1 đến 20!');
                    return;
                }

                addRow(seatCount);
                $('#empty-message').hide();
                updateSeatCount();
                updateSeatConfiguration();
            });

            // Tạo hàng ghế mới
            function addRow(seatCount) {
                const rows = $('#screen-room .row').length;
                const rowLetter = String.fromCharCode(65 + rows);

                if (rows >= 26) {
                    alert('Không thể thêm quá 26 hàng ghế!');
                    return;
                }

                const rowDiv = $(`<div class="row mb-3 flex justify-center items-center relative" data-row="${rowLetter}"></div>`);

                const rowLabel = $(`<div class="row-label">${rowLetter}</div>`);
                rowDiv.append(rowLabel);

                for (let i = 1; i <= seatCount; i++) {
                    const seatId = `${rowLetter}${i}`;
                    const seat = createSeat(seatId, 'Normal');
                    rowDiv.append(seat);
                }

                const deleteBtn = createDeleteRowButton(rowLetter);
                rowDiv.append(deleteBtn);

                $('#screen-room').append(rowDiv);
            }

            // Tạo ghế
            function createSeat(seatId, type) {
                const isCouple = type === 'Couple';
                const seat = $(`<div class="seat ${seatClasses[type]} mx-1 ${isCouple ? 'w-16' : 'w-8'} h-8 rounded-sm flex items-center justify-center text-xs"
                    data-seat="${seatId}" data-type="${type}">
                    ${isCouple ? 'ĐÔI' : seatId}
                </div>`);

                // Xử lý click
                seat.on('click', function() {
                    if (currentMode === 'delete') {
                        deleteSeat($(this));
                    } else {
                        editSeat($(this));
                    }
                });

                return seat;
            }

            // Xóa ghế đơn lẻ
            function deleteSeat(seatElement) {
                const seatId = seatElement.data('seat');
                const rowElement = seatElement.closest('.row');

                if (confirm(`Bạn có chắc muốn xóa ghế ${seatId}?`)) {
                    const emptySpace = createEmptySpace(seatId);
                    seatElement.replaceWith(emptySpace);

                    updateSeatCount();
                    updateSeatConfiguration();
                    checkEmptyRow(rowElement);
                }
            }

            // Tạo vị trí trống
            function createEmptySpace(originalSeatId) {
                const emptySpace = $(`<div class="seat empty-space mx-1 w-8 h-8 rounded-sm flex items-center justify-center text-xs"
                    data-seat="${originalSeatId}" data-type="empty">
                    <i class="fas fa-plus"></i>
                </div>`);

                emptySpace.on('click', function() {
                    if (currentMode === 'edit') {
                        const newSeat = createSeat(originalSeatId, currentSeatType);
                        $(this).replaceWith(newSeat);
                        updateModeUI();
                        updateSeatCount();
                        updateSeatConfiguration();
                    }
                });

                return emptySpace;
            }

            // Sửa ghế (thay đổi loại)
            function editSeat(seatElement) {
                const oldType = seatElement.data('type');
                const newType = currentSeatType;
                const seatId = seatElement.data('seat');

                if (oldType !== newType && oldType !== 'empty') {
                    seatElement.removeClass(Object.values(seatClasses).join(' '));
                    seatElement.addClass(seatClasses[newType]);
                    seatElement.data('type', newType);

                    if (newType === 'Couple') {
                        seatElement.text('ĐÔI').removeClass('w-8').addClass('w-16');
                    } else {
                        seatElement.text(seatId).removeClass('w-16').addClass('w-8');
                    }

                    updateSeatCount();
                    updateSeatConfiguration();
                }
            }

            // Kiểm tra hàng trống
            function checkEmptyRow(rowElement) {
                const hasRealSeats = rowElement.find('.seat:not(.empty-space)').length > 0;

                if (!hasRealSeats) {
                    if (confirm('Hàng này không còn ghế nào. Bạn có muốn xóa cả hàng không?')) {
                        rowElement.remove();
                        updateRowLabels();
                        updateSeatCount();
                        updateSeatConfiguration();

                        if ($('#screen-room .row').length === 0) {
                            $('#empty-message').show();
                        }
                    }
                }
            }

            // Cập nhật lại label hàng
            function updateRowLabels() {
                $('#screen-room .row').each(function(index) {
                    const newRowLetter = String.fromCharCode(65 + index);
                    $(this).attr('data-row', newRowLetter);
                    $(this).find('.row-label').text(newRowLetter);

                    $(this).find('.seat').each(function(seatIndex) {
                        const newSeatId = `${newRowLetter}${seatIndex + 1}`;
                        $(this).attr('data-seat', newSeatId);
                        if ($(this).data('type') !== 'Couple' && $(this).data('type') !== 'empty') {
                            $(this).text(newSeatId);
                        }
                    });
                });
            }

            // Cập nhật cấu hình ghế
            function updateSeatConfiguration() {
                const configuration = [];

                $('#screen-room .row').each(function() {
                    const rowData = {
                        row: $(this).data('row'),
                        seats: []
                    };

                    $(this).find('.seat').each(function() {
                        const seatType = $(this).data('type');
                        if (seatType !== 'empty') {
                            rowData.seats.push({
                                id: $(this).data('seat'),
                                type: seatType,
                                position: $(this).index() - 1
                            });
                        }
                    });

                    if (rowData.seats.length > 0) {
                        configuration.push(rowData);
                    }
                });

                $('#seat_configuration').val(JSON.stringify(configuration));
            }

            // Xóa tất cả ghế
            $('#clear-all').on('click', function() {
                if (confirm('Bạn có chắc muốn xóa tất cả ghế?')) {
                    $('#screen-room').empty();
                    $('#empty-message').show();
                    updateSeatCount();
                    updateSeatConfiguration();
                }
            });

            // Template phòng nhỏ
            $('#template-small').on('click', function() {
                if ($('#screen-room .row').length > 0 && !confirm('Thao tác này sẽ xóa ghế hiện tại. Bạn có chắc?')) {
                    return;
                }

                $('#screen-room').empty();
                $('#empty-message').hide();

                for (let row = 0; row < 5; row++) {
                    $('#row-seats').val(10);
                    addRow(10);
                }

                $('.row[data-row="C"] .seat:not(.empty-space)').slice(3, 7).each(function() {
                    $(this).removeClass('Normal').addClass('VIP').data('type', 'VIP');
                });

                updateModeUI();
                updateSeatCount();
                updateSeatConfiguration();
            });

            // Template phòng lớn
            $('#template-large').on('click', function() {
                if ($('#screen-room .row').length > 0 && !confirm('Thao tác này sẽ xóa ghế hiện tại. Bạn có chắc?')) {
                    return;
                }

                $('#screen-room').empty();
                $('#empty-message').hide();

                const seatCounts = [12, 14, 14, 14, 14, 14, 14, 12];

                for (let i = 0; i < 8; i++) {
                    $('#row-seats').val(seatCounts[i]);
                    addRow(seatCounts[i]);
                }

                $('.row[data-row="D"] .seat:not(.empty-space), .row[data-row="E"] .seat:not(.empty-space)').slice(4, 10).each(function() {
                    $(this).removeClass('Normal').addClass('VIP').data('type', 'VIP');
                });

                updateModeUI();
                updateSeatCount();
                updateSeatConfiguration();
            });

            // Xử lý form submit
            $('#screen-form').on('submit', function(e) {
                if ($('#screen-room .row').length === 0) {
                    e.preventDefault();
                    alert('Vui lòng thêm ít nhất một hàng ghế!');
                    return false;
                }

                updateSeatConfiguration();

                if (!$('#seat_configuration').val()) {
                    e.preventDefault();
                    alert('Có lỗi xảy ra khi lưu cấu hình ghế!');
                    return false;
                }

                // Show loading when submitting
                $(this).find('button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Đang cập nhật...');
            });

            // Initialize
            loadExistingSeatLayout();
        });
    </script>
@endsection
