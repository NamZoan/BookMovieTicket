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
                            <form action="{{ route('admin.movies.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label for="title" class="form-label">Tên Phim</label>
                                        <input type="text" class="form-control" id="title" name="title" required>
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label for="original_title" class="form-label">Tên Phim Gốc</label>
                                        <input type="text" class="form-control" id="original_title" name="original_title">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Mô Tả</label>
                                    <input type="text" class="form-control" id="description" name="description">
                                </div>

                                <div class="row">
                                    <div class="mb-3 col-md-4">
                                        <label for="duration" class="form-label">Thời lượng phim (phút)</label>
                                        <input type="number" class="form-control" id="duration" name="duration" required>
                                    </div>

                                    <div class="mb-3 col-md-4">
                                        <label for="release_date" class="form-label">Ngày khởi chiếu</label>
                                        <input type="date" class="form-control" id="release_date" name="release_date"
                                            required>
                                    </div>

                                    <div class="mb-3 col-md-4">
                                        <label for="genre" class="form-label">Thể Loại</label>
                                        <input class="form-control" list="genreOptions" id="genre" name="genre"
                                            placeholder="Type to search...">
                                        <datalist id="genreOptions">
                                            <option value="Hành động"></option>
                                            <option value="Phiêu lưu"></option>
                                            <option value="Hài"></option>
                                            <option value="Chính kịch"></option>
                                            <option value="Tình cảm"></option>
                                            <option value="Kinh dị"></option>
                                            <option value="Giật gân"></option>
                                            <option value="Tâm lý tội phạm"></option>
                                            <option value="Khoa học viễn tưởng"></option>
                                            <option value="Giả tưởng"></option>
                                            <option value="Hoạt hình"></option>
                                            <option value="Gia đình"></option>
                                            <option value="Cổ trang / Lịch sử"></option>
                                            <option value="Chiến tranh"></option>
                                            <option value="Tài liệu"></option>
                                            <option value="Âm nhạc"></option>
                                            <option value="Tiểu sử"></option>
                                            <option value="Hài - Lãng mạn"></option>
                                            <option value="Hành động - Viễn tưởng"></option>
                                            <option value="Kinh dị - Tâm lý"></option>
                                            <option value="Phiêu lưu - Gia đình"></option>
                                        </datalist>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="mb-3 col-md-4">
                                        <label for="director" class="form-label">Đạo Diễn</label>
                                        <input type="text" class="form-control" id="director" name="director">
                                    </div>

                                    <div class="mb-3 col-md-8">
                                        <label for="cast" class="form-label">Diễn viên</label>
                                        <input type="text" class="form-control" id="cast" name="cast">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label for="poster_url" class="form-label">Poster</label>
                                        <input type="file" class="form-control" id="poster_url" name="poster_url"
                                            accept="image/*">
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label for="status" class="form-label">Trạng thái</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="Coming Soon">Coming Soon</option>
                                            <option value="Now Showing">Now Showing</option>
                                            <option value="Ended">Ended</option>
                                        </select>
                                    </div>
                                </div>



                                <button type="submit" class="btn btn-primary me-2">Thêm mới</button>
                                <a href="{{ route('admin.movies.index') }}" class="btn btn-outline-secondary">Quay lại</a>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
