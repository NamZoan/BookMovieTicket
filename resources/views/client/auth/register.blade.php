<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký - MyShowz</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/as-alert-message.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style-starter.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/sign-in.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/as-alert-message.min.css') }}">
</head>

<body>
    <div class="container_signup_signin" id="container_signup_signin">
        <div class="form-container sign-up-container">
            <form method="POST" action="{{ route('auth.register.post') }}">
                @csrf
                <h1>Tạo Tài Khoản</h1>
                <div class="social-container">
                    <a href="{{ route('auth.google.redirect') }}" class="social"><i class="fab fa-google-plus-g"></i></a>
                </div>
                <span>hoặc sử dụng email để đăng ký</span>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <input name="full_name" type="text" placeholder="Họ và tên" value="{{ old('full_name') }}" required />
                @error('full_name')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror

                <input name="email" type="email" placeholder="Email" value="{{ old('email') }}" required />
                @error('email')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror

                <input name="phone" type="text" placeholder="Số điện thoại (tùy chọn)" value="{{ old('phone') }}" />
                @error('phone')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror

                <input name="password" type="password" placeholder="Mật khẩu" required />
                @error('password')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror

                <input name="password_confirmation" type="password" placeholder="Xác nhận mật khẩu" required />

                <button type="submit">Đăng Ký</button>
                <p class="mt-3">
                    Đã có tài khoản? <a href="{{ route('auth.login') }}">Đăng nhập</a>
                </p>
            </form>
        </div>
        <div class="form-container sign-in-container">
            <form method="POST" action="{{ route('auth.login.post') }}">
                @csrf
                <h1>Đăng Nhập</h1>
                <div class="social-container">
                    <a href="{{ route('auth.google.redirect') }}" class="social" style="color: var(--theme-title);">
                        <i class="fab fa-google-plus-g"></i>
                    </a>
                </div>
                <span>hoặc sử dụng tài khoản của bạn</span>
                <input name="email" type="email" placeholder="Email" value="{{ old('email') }}" required />
                <input name="password" type="password" placeholder="Mật khẩu" required />
                <label>
                    <input type="checkbox" name="remember"> Ghi nhớ đăng nhập
                </label>
                <a href="{{ route('auth.forgot-password') }}">Quên mật khẩu?</a>
                <button type="submit">Đăng Nhập</button>
                <p class="mt-3">
                    Chưa có tài khoản? <a href="{{ route('auth.register') }}">Đăng ký</a>
                </p>
            </form>
        </div>
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Chào Mừng Trở Lại!</h1>
                    <p>Để tiếp tục kết nối với chúng tôi, vui lòng đăng nhập bằng thông tin của bạn</p>
                    <button class="ghost" id="signIn">Đăng Nhập</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Xin Chào!</h1>
                    <p>Đăng ký và đặt vé ngay bây giờ!!!</p>
                    <button class="ghost" id="signUp">Đăng Ký</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/as-alert-message.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/sign-in.js') }}"></script>
</body>

</html>

