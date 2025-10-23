<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="index.html" class="app-brand-link">
            <span class="app-brand-logo demo">
                <svg width="25" viewBox="0 0 25 42" version="1.1" xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink">
                    <defs>
                        <path
                            d="M13.7918663,0.358365126 L3.39788168,7.44174259 C0.566865006,9.69408886 -0.379795268,12.4788597 0.557900856,15.7960551 C0.68998853,16.2305145 1.09562888,17.7872135 3.12357076,19.2293357 C3.8146334,19.7207684 5.32369333,20.3834223 7.65075054,21.2172976 L7.59773219,21.2525164 L2.63468769,24.5493413 C0.445452254,26.3002124 0.0884951797,28.5083815 1.56381646,31.1738486 C2.83770406,32.8170431 5.20850219,33.2640127 7.09180128,32.5391577 C8.347334,32.0559211 11.4559176,30.0011079 16.4175519,26.3747182 C18.0338572,24.4997857 18.6973423,22.4544883 18.4080071,20.2388261 C17.963753,17.5346866 16.1776345,15.5799961 13.0496516,14.3747546 L10.9194936,13.4715819 L18.6192054,7.984237 L13.7918663,0.358365126 Z"
                            id="path-1"></path>
                        <path
                            d="M5.47320593,6.00457225 C4.05321814,8.216144 4.36334763,10.0722806 6.40359441,11.5729822 C8.61520715,12.571656 10.0999176,13.2171421 10.8577257,13.5094407 L15.5088241,14.433041 L18.6192054,7.984237 C15.5364148,3.11535317 13.9273018,0.573395879 13.7918663,0.358365126 C13.5790555,0.511491653 10.8061687,2.3935607 5.47320593,6.00457225 Z"
                            id="path-3"></path>
                        <path
                            d="M7.50063644,21.2294429 L12.3234468,23.3159332 C14.1688022,24.7579751 14.397098,26.4880487 13.008334,28.506154 C11.6195701,30.5242593 10.3099883,31.790241 9.07958868,32.3040991 C5.78142938,33.4346997 4.13234973,34 4.13234973,34 C4.13234973,34 2.75489982,33.0538207 2.37032616e-14,31.1614621 C-0.55822714,27.8186216 -0.55822714,26.0572515 -4.05231404e-15,25.8773518 C0.83734071,25.6075023 2.77988457,22.8248993 3.3049379,22.52991 C3.65497346,22.3332504 5.05353963,21.8997614 7.50063644,21.2294429 Z"
                            id="path-4"></path>
                        <path
                            d="M20.6,7.13333333 L25.6,13.8 C26.2627417,14.6836556 26.0836556,15.9372583 25.2,16.6 C24.8538077,16.8596443 24.4327404,17 24,17 L14,17 C12.8954305,17 12,16.1045695 12,15 C12,14.5672596 12.1403557,14.1461923 12.4,13.8 L17.4,7.13333333 C18.0627417,6.24967773 19.3163444,6.07059163 20.2,6.73333333 C20.3516113,6.84704183 20.4862915,6.981722 20.6,7.13333333 Z"
                            id="path-5"></path>
                    </defs>
                    <g id="g-app-brand" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <g id="Brand-Logo" transform="translate(-27.000000, -15.000000)">
                            <g id="Icon" transform="translate(27.000000, 15.000000)">
                                <g id="Mask" transform="translate(0.000000, 8.000000)">
                                    <mask id="mask-2" fill="white">
                                        <use xlink:href="#path-1"></use>
                                    </mask>
                                    <use fill="#696cff" xlink:href="#path-1"></use>
                                    <g id="Path-3" mask="url(#mask-2)">
                                        <use fill="#696cff" xlink:href="#path-3"></use>
                                        <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-3"></use>
                                    </g>
                                    <g id="Path-4" mask="url(#mask-2)">
                                        <use fill="#696cff" xlink:href="#path-4"></use>
                                        <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-4"></use>
                                    </g>
                                </g>
                                <g id="Triangle"
                                    transform="translate(19.000000, 11.000000) rotate(-300.000000) translate(-19.000000, -11.000000) ">
                                    <use fill="#696cff" xlink:href="#path-5"></use>
                                    <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-5"></use>
                                </g>
                            </g>
                        </g>
                    </g>
                </svg>
            </span>
            <span class="app-brand-text demo menu-text fw-bolder ms-2">Sneat</span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>
    <div class="menu-inner-shadow"></div>

    <!-- Search -->
    <div class="px-3 py-2">
        <div class="nav-item d-flex align-items-center">
            <i class="bx bx-search fs-4 lh-0"></i>
            <input type="text" id="menu-search" class="form-control border-0 shadow-none" placeholder="Search..."
                aria-label="Search..." />
        </div>
    </div>
    <!-- /Search -->

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Quản Lý</span>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.movies.*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-film"></i>
                <div data-i18n="Movies">Phim</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.movies.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.movies.index') }}" class="menu-link">
                        <div data-i18n="List">Danh sách</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.movies.create') ? 'active' : '' }}">
                    <a href="{{ route('admin.movies.create') }}" class="menu-link">
                        <div data-i18n="Create">Thêm mới</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.cinemas.*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-building-house"></i>
                <div data-i18n="Cinemas">Rạp Chiếu</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.cinemas.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.cinemas.index') }}" class="menu-link">
                        <div data-i18n="List">Danh sách</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.cinemas.create') ? 'active' : '' }}">
                    <a href="{{ route('admin.cinemas.create') }}" class="menu-link">
                        <div data-i18n="Create">Thêm mới</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.screens.*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-desktop"></i>
                <div data-i18n="Screens">Phòng Chiếu</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.screens.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.screens.index') }}" class="menu-link">
                        <div data-i18n="List">Danh sách</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.screens.create') ? 'active' : '' }}">
                    <a href="{{ route('admin.screens.create') }}" class="menu-link">
                        <div data-i18n="Create">Thêm mới</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.seats.*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-chair"></i>
                <div data-i18n="Seats">Ghế Ngồi</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.seats.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.seats.index') }}" class="menu-link">
                        <div data-i18n="List">Danh sách</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.seats.create') ? 'active' : '' }}">
                    <a href="{{ route('admin.seats.create') }}" class="menu-link">
                        <div data-i18n="Create">Thêm mới</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.showtimes.*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-calendar"></i>
                <div data-i18n="Showtimes">Lịch Chiếu</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.showtimes.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.showtimes.index') }}" class="menu-link">
                        <div data-i18n="List">Danh sách</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.showtimes.create') ? 'active' : '' }}">
                    <a href="{{ route('admin.showtimes.create') }}" class="menu-link">
                        <div data-i18n="Create">Thêm mới</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.pricing.*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-purchase-tag"></i>
                <div data-i18n="Pricing">Giá Vé</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.pricing.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.pricing.index') }}" class="menu-link">
                        <div data-i18n="List">Danh sách</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.pricing.create') ? 'active' : '' }}">
                    <a href="{{ route('admin.pricing.create') }}" class="menu-link">
                        <div data-i18n="Create">Thêm mới</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.food-items.*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-food-menu"></i>
                <div data-i18n="Food">Món Ăn</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.food-items.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.food-items.index') }}" class="menu-link">
                        <div data-i18n="List">Danh sách</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.food-items.create') ? 'active' : '' }}">
                    <a href="{{ route('admin.food-items.create') }}" class="menu-link">
                        <div data-i18n="Create">Thêm mới</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.promotions.*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-gift"></i>
                <div data-i18n="Promotions">Khuyến Mãi</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.promotions.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.promotions.index') }}" class="menu-link">
                        <div data-i18n="List">Danh sách</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.promotions.create') ? 'active' : '' }}">
                    <a href="{{ route('admin.promotions.create') }}" class="menu-link">
                        <div data-i18n="Create">Thêm mới</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.bookings.*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-gift"></i>
                <div data-i18n="Promotions">Đơn Hàng</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.bookings.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.bookings.index') }}" class="menu-link">
                        <div data-i18n="List">Danh sách</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Hệ Thống</span>
        </li>

        <!-- System Management -->
        <li class="menu-item {{ request()->routeIs('admin.system.*') ? 'active open' : '' }}">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-cog"></i>
                <div data-i18n="System">Cài đặt hệ thống</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.system.routes') ? 'active' : '' }}">
                    <a href="{{ route('admin.system.routes') }}" class="menu-link">
                        <div data-i18n="Routes">Quản Lý Route</div>
                    </a>
                </li>
            </ul>
            </a>
        </li>
    </ul>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('menu-search');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const filter = searchInput.value.toLowerCase();
                const menuItems = document.querySelectorAll('.menu-inner .menu-item');

                menuItems.forEach(function(item) {
                    // Ignore headers and items in submenus for top-level filtering
                    if (item.classList.contains('menu-header') || item.closest('.menu-sub') !== null) {
                        return;
                    }

                    const text = item.textContent || item.innerText;
                    if (text.toLowerCase().indexOf(filter) > -1) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }
    });
</script>
