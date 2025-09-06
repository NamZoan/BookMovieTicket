<header id="site-header" class="w3l-header fixed-top">
    <!--/nav-->
    <nav class="navbar navbar-expand-lg navbar-light fill px-lg-0 py-0 px-3">
        <div class="container">
            <h1><a class="navbar-brand" href="{{ route('home') }}"><span class="fa fa-play icon-log" aria-hidden="true"></span>
                    MyShowz</a></h1>
            <!-- if logo is image enable this
						<a class="navbar-brand" href="{{ route('home') }}">
							<img src="image-path" alt="Your logo" title="Your logo" style="height:35px;" />
						</a> -->
            <button class="navbar-toggler collapsed" type="button" data-toggle="collapse"
                data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <!-- <span class="navbar-toggler-icon"></span> -->
                <span class="fa icon-expand fa-bars"></span>
                <span class="fa icon-close fa-times"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item {{ request()->routeIs('home', 'client.home') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('movies.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('movies.index') }}">Movies</a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('cinemas.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('cinemas.index') }}">Cinemas</a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('about') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('about') }}">About</a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('contact') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('contact') }}">Contact</a>
                    </li>
                </ul>

                <!--/search-right-->
                <div class="search-right">
                    <a href="#search" class="btn search-hny mr-lg-3 mt-lg-0 mt-4" title="search">Search <span
                            class="fa fa-search ml-3" aria-hidden="true"></span></a>
                    <!-- search popup -->
                    <div id="search" class="pop-overlay">
                        <div class="popup">
                            <form action="{{ route('movies.search') }}" method="GET" class="search-box">
                                <input type="search" placeholder="Search movies..." name="q" required="required"
                                    autofocus="" value="{{ request('q') }}">
                                <button type="submit" class="btn"><span class="fa fa-search"
                                        aria-hidden="true"></span></button>
                            </form>
                            <div class="browse-items">
                                <h3 class="hny-title two mt-md-5 mt-4">Browse by Genre:</h3>
                                <ul class="search-items">
                                    <li><a href="{{ route('movies.genre', 'action') }}">Action</a></li>
                                    <li><a href="{{ route('movies.genre', 'drama') }}">Drama</a></li>
                                    <li><a href="{{ route('movies.genre', 'family') }}">Family</a></li>
                                    <li><a href="{{ route('movies.genre', 'thriller') }}">Thriller</a></li>
                                    <li><a href="{{ route('movies.genre', 'comedy') }}">Comedy</a></li>
                                    <li><a href="{{ route('movies.genre', 'romantic') }}">Romantic</a></li>
                                    <li><a href="{{ route('movies.genre', 'tv-series') }}">TV-Series</a></li>
                                    <li><a href="{{ route('movies.genre', 'horror') }}">Horror</a></li>
                                    <li><a href="{{ route('movies.genre', 'sci-fi') }}">Sci-Fi</a></li>
                                    <li><a href="{{ route('movies.genre', 'adventure') }}">Adventure</a></li>
                                    <li><a href="{{ route('movies.genre', 'animation') }}">Animation</a></li>
                                    <li><a href="{{ route('movies.genre', 'documentary') }}">Documentary</a></li>
                                    <li><a href="{{ route('movies.genre', 'fantasy') }}">Fantasy</a></li>
                                    <li><a href="{{ route('movies.genre', 'mystery') }}">Mystery</a></li>
                                    <li><a href="{{ route('movies.genre', 'crime') }}">Crime</a></li>
                                    <li><a href="{{ route('movies.genre', 'biography') }}">Biography</a></li>
                                </ul>
                            </div>
                        </div>
                        <a class="close" href="#close">×</a>
                    </div>
                    <!-- /search popup -->
                </div>

                <!-- Authentication Links -->
                <div class="Login_SignUp" id="login" style="font-size: 2rem ; display: inline-block; position: relative;">
                    @guest
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-user-circle-o"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('auth.login') }}">
                                    <i class="fa fa-sign-in mr-2"></i>Login
                                </a>
                                <a class="dropdown-item" href="{{ route('auth.register') }}">
                                    <i class="fa fa-user-plus mr-2"></i>Register
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-user-circle"></i>
                                <span class="ml-1">{{ Auth::user()->name ?? 'User' }}</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('account.dashboard') }}">
                                    <i class="fa fa-dashboard mr-2"></i>Dashboard
                                </a>
                                <a class="dropdown-item" href="{{ route('account.profile') }}">
                                    <i class="fa fa-user mr-2"></i>Profile
                                </a>
                                <a class="dropdown-item" href="{{ route('account.bookings') }}">
                                    <i class="fa fa-ticket mr-2"></i>My Bookings
                                </a>
                                <a class="dropdown-item" href="{{ route('account.favorites') }}">
                                    <i class="fa fa-heart mr-2"></i>Favorites
                                </a>
                                <a class="dropdown-item" href="{{ route('account.loyalty-points') }}">
                                    <i class="fa fa-star mr-2"></i>Loyalty Points
                                </a>
                                <div class="dropdown-divider"></div>
                                <form method="POST" action="{{ route('auth.logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fa fa-sign-out mr-2"></i>Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endguest
                </div>
            </div>
            
            <!-- toggle switch for light and dark theme -->
            <div class="mobile-position">
                <nav class="navigation">
                    <div class="theme-switch-wrapper">
                        <label class="theme-switch" for="checkbox">
                            <input type="checkbox" id="checkbox">
                            <div class="mode-container">
                                <i class="gg-sun"></i>
                                <i class="gg-moon"></i>
                            </div>
                        </label>
                    </div>
                </nav>
            </div>
        </div>
    </nav>
</header>
@section('styles')
<style>
    .dropdown-menu {
    background-color: var(--bg-color);
    border: 1px solid var(--border-color);
}

.dropdown-item:hover {
    background-color: var(--hover-color);
}
</style>
@endsection
