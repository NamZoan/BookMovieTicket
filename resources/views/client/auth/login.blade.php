<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập / Đăng Ký - MyShowz</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/as-alert-message.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style-starter.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/sign-in.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/as-alert-message.min.css') }}">
    <style>
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            font-size: 14px;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .text-danger {
            color: #dc3545;
            font-size: 12px;
            display: block;
            margin-top: -10px;
            margin-bottom: 10px;
        }
        .form-container form label {
            display: flex;
            align-items: center;
            margin: 10px 0;
            font-size: 14px;
        }
        .form-container form label input[type="checkbox"] {
            margin-right: 8px;
        }
    </style>
</head>

<body>
    <div class="container_signup_signin" id="container_signup_signin">
        <!-- Form Đăng Ký -->
        <div class="form-container sign-up-container">
            <form method="POST" action="{{ route('auth.register.post') }}">
                @csrf
                <h1>Tạo Tài Khoản</h1>
                <div class="social-container">
                    <a href="{{ route('auth.google.redirect') }}" class="social">
                        <i class="fab fa-google-plus-g"></i>
                    </a>
                </div>
                <span>hoặc sử dụng email để đăng ký</span>

                @if ($errors->has('full_name') || $errors->has('email') || $errors->has('phone') || $errors->has('password'))
                    <div class="alert alert-danger">
                        <ul class="mb-0" style="padding-left: 20px;">
                            @if ($errors->has('full_name'))
                                <li>{{ $errors->first('full_name') }}</li>
                            @endif
                            @if ($errors->has('email'))
                                <li>{{ $errors->first('email') }}</li>
                            @endif
                            @if ($errors->has('phone'))
                                <li>{{ $errors->first('phone') }}</li>
                            @endif
                            @if ($errors->has('password'))
                                <li>{{ $errors->first('password') }}</li>
                            @endif
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <input name="full_name" type="text" placeholder="Họ và tên" value="{{ old('full_name') }}" required />
                @error('full_name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror

                <input name="email" type="email" placeholder="Email" value="{{ old('email') }}" required />
                @error('email')
                    <span class="text-danger">{{ $message }}</span>
                @enderror

                <input name="phone" type="text" placeholder="Số điện thoại (tùy chọn)" value="{{ old('phone') }}" />
                @error('phone')
                    <span class="text-danger">{{ $message }}</span>
                @enderror

                <input name="password" type="password" placeholder="Mật khẩu" required />
                @error('password')
                    <span class="text-danger">{{ $message }}</span>
                @enderror

                <input name="password_confirmation" type="password" placeholder="Xác nhận mật khẩu" required />

                <button type="submit">Đăng Ký</button>
                <p class="mt-3" style="margin-top: 15px; font-size: 14px;">
                    Đã có tài khoản? <a href="{{ route('auth.login') }}" style="color: #0d6efd;">Đăng nhập</a>
                </p>
            </form>
        </div>

        <!-- Form Đăng Nhập -->
        <div class="form-container sign-in-container">
            <form method="POST" action="{{ route('auth.login.post') }}" style="color: var(--theme-title);">
                @csrf
                <h1>Đăng Nhập</h1>
                <div class="social-container">
                    <a href="{{ route('auth.google.redirect') }}" class="social" style="color: var(--theme-title);">
                        <i class="fab fa-google-plus-g"></i>
                    </a>
                </div>
                @if ($errors->has('email') && !$errors->has('full_name') && !$errors->has('password_confirmation'))
                    <div class="alert alert-danger">
                        <ul class="mb-0" style="padding-left: 20px;">
                            @foreach ($errors->get('email') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                            @if ($errors->has('password'))
                                @foreach ($errors->get('password') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                @endif

                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                <input name="email" type="email" placeholder="Email" value="{{ old('email') }}" required />
                <input name="password" type="password" placeholder="Mật khẩu" required />
                <a href="{{ route('auth.forgot-password') }}">Quên mật khẩu?</a>
                <button type="submit">Đăng Nhập</button>
            </form>
        </div>

        <!-- Overlay Container -->
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
    <script src="{{ asset('assets/js/theme-change.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/sign-in.js') }}"></script>

    <script>
        // Xử lý chuyển đổi giữa form đăng nhập và đăng ký
        const signUpButton = document.getElementById('signUp');
        const signInButton = document.getElementById('signIn');
        const container = document.getElementById('container_signup_signin');

        if (signUpButton) {
            signUpButton.addEventListener('click', () => {
                container.classList.add("right-panel-active");
            });
        }

        if (signInButton) {
            signInButton.addEventListener('click', () => {
                container.classList.remove("right-panel-active");
            });
        }

        // Tự động chuyển sang form đăng ký nếu có lỗi từ form đăng ký
        @if ($errors->has('full_name') || $errors->has('email') || ($errors->has('password') && !$errors->has('email')))
            document.addEventListener('DOMContentLoaded', function() {
                container.classList.add("right-panel-active");
            });
        @endif
    </script>

</body>

</html>
