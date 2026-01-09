<header class="navbar-wrap sticky-top bg-white border-bottom border-danger-subtle">
    <nav class="navbar navbar-expand-lg navbar-light bg-white" aria-label="Main navigation">
        <div class="container py-2">
            <a class="navbar-brand d-flex align-items-center gap-2 text-uppercase fw-bold" href="{{ route('home') }}">
                <span class="brand-icon rounded-circle d-inline-flex align-items-center justify-content-center">
                    <i class="bi bi-film"></i>
                </span>
                <span class="fs-4 text-black">MyShowz</span>
            </a>

            <button class="navbar-toggler border-danger" type="button" data-bs-toggle="offcanvas" data-bs-target="#site-nav" aria-controls="site-nav" aria-label="{{ __('Mở điều hướng') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="offcanvas offcanvas-end" tabindex="-1" id="site-nav" aria-labelledby="site-nav-label">
                <div class="offcanvas-header border-bottom">
                    <h6 class="offcanvas-title text-uppercase small fw-semibold text-black" id="site-nav-label">Menu</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="{{ __('Đóng') }}"></button>
                </div>

                <div class="offcanvas-body d-flex flex-column flex-lg-row align-items-lg-center gap-lg-4 py-4 py-lg-0 bg-white">
                    <ul class="navbar-nav flex-lg-row flex-grow-1 gap-2 gap-lg-3 justify-content-lg-end">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home', 'client.home') ? 'active' : '' }}" href="{{ route('home') }}">{{ __('Trang chủ') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('movies.*') ? 'active' : '' }}" href="{{ route('movies.index') }}">{{ __('Phim') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('cinemas.*') ? 'active' : '' }}" href="{{ route('cinemas.index') }}">{{ __('Rạp') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">{{ __('Giới thiệu') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">{{ __('Liên hệ') }}</a>
                        </li>
                    </ul>

                    <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-3 w-100 w-lg-auto mt-3 mt-lg-0 ms-lg-auto">

                        <div class="dropdown">
                            <button class="btn btn-outline-brand btn-sm rounded-pill d-flex align-items-center gap-2 dropdown-toggle" type="button" id="accountDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle fs-5"></i>
                                <span class="d-none d-lg-inline text-black">{{ Auth::user()->name ?? __('Tài khoản') }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="accountDropdown">
                                @guest
                                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('auth.login') }}"><i class="bi bi-box-arrow-in-right"></i> {{ __('Đăng nhập') }}</a></li>
                                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('auth.register') }}"><i class="bi bi-person-plus"></i> {{ __('Đăng ký') }}</a></li>
                                @else
                                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('user.bookings.index') }}"><i class="bi bi-speedometer2"></i> {{ __('Bảng điều khiển') }}</a></li>
                                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('user.profile.edit') }}"><i class="bi bi-person-lines-fill"></i> {{ __('Thông tin cá nhân') }}</a></li>
                                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('user.bookings.index') }}"><i class="bi bi-ticket-perforated"></i> {{ __('Vé của tôi') }}</a></li>
                                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('user.reviews.index') }}"><i class="bi bi-chat-square-text"></i> {{ __('Review của tôi') }}</a></li>
                                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('user.change-password') }}"><i class="bi bi-key"></i> {{ __('Đổi mật khẩu') }}</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('auth.logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item d-flex align-items-center gap-2">
                                                <i class="bi bi-box-arrow-right"></i> {{ __('Đăng xuất') }}
                                            </button>
                                        </form>
                                    </li>
                                @endguest
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>
