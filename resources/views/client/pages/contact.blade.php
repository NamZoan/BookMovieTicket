@extends('client.layouts.app')

@section('title', 'Liên hệ - MyShowz')

@section('content')
<section class="py-5 bg-night">
    <div class="container">
        

        @if(session('contact_success'))
            <div class="alert alert-success">{{ session('contact_success') }}</div>
        @elseif(session('contact_error'))
            <div class="alert alert-danger">{{ session('contact_error') }}</div>
        @endif

        <div class="row">
            <div class="col-lg-7">
                <div class="card p-4 mb-4">
                    <h5 class="mb-3">Gửi tin nhắn</h5>
                    <form method="POST" action="{{ route('contact.submit') }}" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" id="name" name="name" required aria-required="true" value="{{ old('name') }}">
                            @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required aria-required="true" value="{{ old('email') }}">
                            @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại (tuỳ chọn)</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone') }}">
                            @error('phone')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Chủ đề</label>
                            <input type="text" class="form-control" id="subject" name="subject" required value="{{ old('subject') }}">
                            @error('subject')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Nội dung</label>
                            <textarea class="form-control" id="message" name="message" rows="6" required>{{ old('message') }}</textarea>
                            @error('message')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="btn btn-brand">Gửi liên hệ</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card p-3 mb-4">
                    <h6>Văn phòng</h6>
                    <p class="text-muted mb-1">55 Đường Giải Phóng, Hai Bà Trưng, Hà Nội</p>
                    <p class="text-muted mb-1">Hotline: <a href="tel:+840123456789">+84 0123 456 789</a></p>
                    <p class="text-muted">Email: <a href="mailto:support@myshowz.example">luuquangkhai2002@gmail.com</a></p>
                </div>

                <div class="card p-3 mb-4">
                    <h6>Bản đồ</h6>
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.google.com/maps?q=55+Đường+Giải+Phóng,+Hai+Bà+Trưng,+Hà+Nội&output=embed" aria-label="Bản đồ đến văn phòng" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
