@extends('admin.layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
                <div class="col-lg-12 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title text-primary">Thêm Phim Mới</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.cinemas.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="name" class="form-label">Tên Rạp</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>

                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label for="address" class="form-label">Địa Chỉ</label>
                                        <input type="text" class="form-control" id="address" name="address">
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label for="city" class="form-label">Thành Phố</label>
                                        <input class="form-control" list="cityOptions" id="city" name="city"
                                            placeholder="Type to search...">
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








                                <button type="submit" class="btn btn-primary me-2">Thêm mới</button>
                                <a href="{{ route('admin.cinemas.index') }}" class="btn btn-outline-secondary">Quay lại</a>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
