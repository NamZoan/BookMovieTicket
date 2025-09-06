@extends('admin.layouts.app')
@section('styles')
<script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Thêm CSS trực tiếp vào đây */
        #cinema-screen {
            background: linear-gradient(to bottom, #e5e7eb, #d1d5db);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .seat {
            cursor: move;
            transition: all 0.2s;
        }

        .seat.selected {
            background-color: #3B82F6;
            color: white;
        }

        .seat.vip {
            background-color: #f59e0b;
            color: white;
        }

        .seat.disabled {
            background-color: #ef4444;
            color: white;
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
                            <h5 class="card-title text-primary mb-0">Thêm phòng chiếu mới</h5>
                        </div>
                        <div class="card-body">
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <form action="{{ route('admin.screens.store') }}" method="POST">
                                @csrf

                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3">
                                        <label for="screen_name" class="form-label">Tên phòng chiếu</label>
                                        <input type="text"
                                               class="form-control @error('screen_name') is-invalid @enderror"
                                               id="screen_name"
                                               name="screen_name"
                                               value="{{ old('screen_name') }}"
                                               placeholder="VD: Phòng 1, Phòng 2, ..."
                                               required>
                                        @error('screen_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="screen_id" class="form-label">Chọn rạp chiếu</label>
                                        <select class="form-select @error('screen_id') is-invalid @enderror"
                                                id="screen_id"
                                                name="screen_id"
                                                required>
                                            <option value="">Chọn rạp chiếu</option>
                                            @foreach($screens as $screen)
                                                <option value="{{ $screen->screen_id }}"
                                                    {{ old('screen_id') == $screen->screen_id ? 'selected' : '' }}>
                                                    {{ $screen->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('screen_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3">
                                        <label for="total_seats" class="form-label">Tổng số ghế</label>
                                        <input type="number"
                                               class="form-control @error('total_seats') is-invalid @enderror"
                                               id="total_seats"
                                               name="total_seats"
                                               value="{{ old('total_seats') }}"
                                               min="1"
                                               placeholder="VD: 50, 100, ..."
                                               required>
                                        @error('total_seats')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <h2 class="text-lg font-semibold">Thiết kế ghế phòng chiếu</h2>

                                    <div class="bg-white p-6 rounded-lg shadow">
                                        <!-- Công cụ và cài đặt -->
                                        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                                            <div class="bg-white rounded-lg shadow p-6">
                                                <h2 class="text-lg font-semibold mb-4 border-b pb-2">Công cụ</h2>

                                                <div class="mb-6">
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Chọn phòng chiếu</label>
                                                    <select class="w-full border rounded px-3 py-2">
                                                        <option>Phòng 1 - 2D (50 chỗ)</option>
                                                        <option>Phòng 2 - 3D (80 chỗ)</option>
                                                        <option>Phòng 3 - IMAX (120 chỗ)</option>
                                                        <option>Phòng 4 - VIP (30 chỗ)</option>
                                                    </select>
                                                </div>

                                                <div class="mb-6">
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Loại ghế</label>
                                                    <div class="grid grid-cols-3 gap-2">
                                                        <div class="seat-type-option border rounded p-2 text-center cursor-pointer hover:bg-gray-100" data-type="normal">
                                                            <div class="seat-legend bg-gray-200 mx-auto mb-1"></div>
                                                            <span class="text-xs">Thường</span>
                                                        </div>
                                                        <div class="seat-type-option border rounded p-2 text-center cursor-pointer hover:bg-gray-100" data-type="vip">
                                                            <div class="seat-legend bg-yellow-500 mx-auto mb-1"></div>
                                                            <span class="text-xs">VIP</span>
                                                        </div>
                                                        <div class="seat-type-option border rounded p-2 text-center cursor-pointer hover:bg-gray-100" data-type="disabled">
                                                            <div class="seat-legend bg-red-500 mx-auto mb-1"></div>
                                                            <span class="text-xs">Vô hiệu</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mb-6">
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Thêm hàng ghế</label>
                                                    <div class="flex">
                                                        <input type="number" min="1" max="20" value="5" class="border rounded-l px-3 py-2 w-full" id="row-seats">
                                                        <button id="add-row" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-r">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Khu vực phòng chiếu -->
                                            <div class="lg:col-span-3">
                                                <div class="bg-white rounded-lg shadow p-6">
                                                    <div class="flex justify-between items-center mb-4">
                                                        <h2 class="text-lg font-semibold">Thiết kế bố trí ghế - Phòng 1</h2>
                                                        <div class="text-sm text-gray-500">
                                                            <span id="total-seats">0</span> ghế |
                                                            <span id="normal-seats">0</span> thường |
                                                            <span id="vip-seats">0</span> VIP |
                                                            <span id="disabled-seats">0</span> hỏng
                                                        </div>
                                                    </div>

                                                    <!-- Màn hình chiếu phim -->
                                                    <div id="screen-screen" class="w-full h-8 mb-8 rounded text-center text-sm font-medium flex items-center justify-center">
                                                        MÀN HÌNH CHIẾU
                                                    </div>

                                                    <!-- Khu vực bố trí ghế -->
                                                    <div id="screen-room" class="w-full p-4 rounded-lg relative">
                                                        <!-- Các hàng ghế mẫu -->
                                                        <div class="row mb-3 flex justify-center" data-row="A">
                                                            <div class="seat bg-gray-200 w-8 h-8 rounded-sm flex items-center justify-center mx-1" data-seat="A1">A1</div>
                                                            <div class="seat bg-gray-200 w-8 h-8 rounded-sm flex items-center justify-center mx-1" data-seat="A2">A2</div>
                                                            <div class="seat bg-gray-200 w-8 h-8 rounded-sm flex items-center justify-center mx-1" data-seat="A3">A3</div>
                                                            <div class="seat bg-gray-200 w-8 h-8 rounded-sm flex items-center justify-center mx-1" data-seat="A4">A4</div>
                                                            <div class="seat vip bg-yellow-500 w-8 h-8 rounded-sm flex items-center justify-center mx-1" data-seat="A5" data-vip="true">VIP</div>
                                                        </div>

                                                        <div class="row mb-3 flex justify-center" data-row="B">
                                                            <div class="seat bg-gray-200 w-8 h-8 rounded-sm flex items-center justify-center mx-1" data-seat="B1">B1</div>
                                                            <div class="seat vip bg-yellow-500 w-8 h-8 rounded-sm flex items-center justify-center mx-1" data-seat="B2" data-vip="true">VIP</div>
                                                            <div class="seat vip bg-yellow-500 w-8 h-8 rounded-sm flex items-center justify-center mx-1" data-seat="B3" data-vip="true">VIP</div>
                                                            <div class="seat bg-gray-200 w-8 h-8 rounded-sm flex items-center justify-center mx-1" data-seat="B4">B4</div>
                                                            <div class="seat bg-gray-200 w-8 h-8 rounded-sm flex items-center justify-center mx-1" data-seat="B5">B5</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="bx bx-save me-1"></i> Lưu
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
@endsection

@section('scripts')
    <script>
        // JavaScript trực tiếp
        document.addEventListener('DOMContentLoaded', function () {
            let currentSeatType = 'normal';
            const colors = {
                normal: 'bg-gray-200',
                vip: 'bg-yellow-500',
                disabled: 'bg-red-500'
            };

            // Xử lý sự kiện chọn loại ghế
            document.querySelectorAll('.seat-type-option').forEach(option => {
                option.addEventListener('click', function () {
                    currentSeatType = this.getAttribute('data-type');
                    document.querySelectorAll('.seat-type-option').forEach(el => el.classList.remove('border-blue-500'));
                    this.classList.add('border-blue-500');
                });
            });

            // Thêm hàng ghế
            document.getElementById('add-row').addEventListener('click', function () {
                const seatCount = parseInt(document.getElementById('row-seats').value);
                const rows = document.querySelectorAll('#cinema-room .row');
                const lastRow = rows.length > 0 ? rows[rows.length - 1].getAttribute('data-row') : '@';
                const newRow = String.fromCharCode(lastRow.charCodeAt(0) + 1);

                const rowDiv = document.createElement('div');
                rowDiv.className = 'row mb-3 flex justify-center';
                rowDiv.setAttribute('data-row', newRow);

                for (let i = 1; i <= seatCount; i++) {
                    const seat = document.createElement('div');
                    seat.className = `seat ${colors[currentSeatType]} w-8 h-8 rounded-sm flex items-center justify-center mx-1`;
                    seat.setAttribute('data-seat', `${newRow}${i}`);
                    seat.innerHTML = document.getElementById('toggle-names').checked ? `${newRow}${i}` : '';
                    rowDiv.appendChild(seat);
                }

                document.getElementById('cinema-room').appendChild(rowDiv);
                updateSeatCount();
            });

            // Cập nhật số ghế
            function updateSeatCount() {
                const seats = document.querySelectorAll('.seat');
                document.getElementById('total-seats').textContent = seats.length;
            }
        });
    </script>
@endsection
