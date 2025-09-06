@extends('admin.layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
                <div class="col-lg-12 mb-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title text-primary mb-0">Chỉnh sửa rạp chiếu phim</h5>
                        </div>
                        <div class="card-body">
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <form action="{{ route('admin.cinemas.update', $cinema->cinema_id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label for="name" class="form-label">Tên Rạp</label>
                                    <input type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           id="name"
                                           name="name"
                                           value="{{ old('name', $cinema->name) }}"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label for="address" class="form-label">Địa Chỉ</label>
                                        <input type="text"
                                               class="form-control @error('address') is-invalid @enderror"
                                               id="address"
                                               name="address"
                                               value="{{ old('address', $cinema->address) }}"
                                               required>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label for="city" class="form-label">Thành Phố</label>
                                        <input class="form-control @error('city') is-invalid @enderror"
                                               list="cityOptions"
                                               id="city"
                                               name="city"
                                               value="{{ old('city', $cinema->city) }}"
                                               placeholder="Chọn thành phố..."
                                               required>
                                        <datalist id="cityOptions">
                                            <option value="Hà Nội"></option>
                                            <option value="Hồ Chí Minh"></option>
                                            <option value="Đà Nẵng"></option>
                                            <option value="Cần Thơ"></option>
                                            <option value="Hải Phòng"></option>
                                            <option value="Hải Dương"></option>
                                            <option value="Quảng Ninh"></option>
                                            <option value="Bình Dương"></option>
                                            <option value="Bình Thuận"></option>
                                            <option value="Vũng Tàu"></option>
                                            <option value="Bắc Ninh"></option>
                                            <option value="Nghệ An"></option>
                                            <option value="Thừa Thiên Huế"></option>
                                            <option value="Lâm Đồng"></option>
                                            <option value="Lạng Sơn"></option>
                                            <option value="Kiên Giang"></option>
                                            <option value="Thái Nguyên"></option>
                                            <option value="Phú Thọ"></option>
                                            <option value="Tây Ninh"></option>
                                            <option value="Thanh Hóa"></option>
                                            <option value="Sơn La"></option>
                                            <option value="Hưng Yên"></option>
                                            <option value="Quảng Nam"></option>
                                            <option value="Quảng Ngãi"></option>
                                            <option value="Ninh Bình"></option>
                                            <option value="Bà Rịa - Vũng Tàu"></option>
                                            <option value="Cao Bằng"></option>
                                            <option value="Hòa Bình"></option>
                                            <option value="Gia Lai"></option>
                                            <option value="Kon Tum"></option>
                                            <option value="An Giang"></option>
                                            <option value="Cà Mau"></option>
                                            <option value="Bạc Liêu"></option>
                                            <option value="Bến Tre"></option>
                                            <option value="Vĩnh Long"></option>
                                            <option value="Trà Vinh"></option>
                                            <option value="Long An"></option>
                                            <option value="Tây Ninh"></option>
                                            <option value="Lào Cai"></option>
                                            <option value="Tuyên Quang"></option>
                                            <option value="Yên Bái"></option>
                                            <option value="Quảng Trị"></option>
                                            <option value="Bắc Giang"></option>
                                            <option value="Đắk Lắk"></option>
                                            <option value="Đắk Nông"></option>
                                            <option value="Hà Giang"></option>
                                            <option value="Cao Bằng"></option>
                                            <option value="Hà Nam"></option>
                                            <option value="Hải Phòng"></option>
                                            <option value="Hưng Yên"></option>
                                            <option value="Bình Phước"></option>
                                            <option value="Vĩnh Phúc"></option>
                                            <option value="Hòa Bình"></option>
                                            <option value="Quảng Bình"></option>
                                            <option value="Quảng Trị"></option>
                                            <option value="Bắc Kạn"></option>
                                            <option value="Cần Thơ"></option>
                                            <option value="Bình Dương"></option>
                                            <option value="Đồng Nai"></option>
                                            <option value="Khánh Hòa"></option>
                                            <option value="Lâm Đồng"></option>
                                        </datalist>


                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="bx bx-save me-1"></i> Lưu thay đổi
                                    </button>
                                    <a href="{{ route('admin.cinemas.index') }}" class="btn btn-outline-secondary">
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

