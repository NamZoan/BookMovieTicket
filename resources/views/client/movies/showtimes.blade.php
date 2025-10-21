@extends('client.layouts.app')

@push('styles')
<style>
    :root {
        --primary: #e51c23;
        --primary-light: #ff5757;
        --primary-dark: #b71c1c;
        --secondary: #2196f3;
        --success: #4caf50;
        --warning: #ff9800;
        --error: #f44336;
        --background: #fafafa;
        --surface: #ffffff;
        --text-primary: #212121;
        --text-secondary: #757575;
        --text-hint: #9e9e9e;
        --border: #e0e0e0;
        --border-light: #f5f5f5;
        --shadow: rgba(0, 0, 0, 0.1);
        --shadow-light: rgba(0, 0, 0, 0.05);
    }

    * {
        box-sizing: border-box;
    }

    body {
        background-color: var(--background);
        color: var(--text-primary);
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    }

    /* Header Styles */
    .movie-header {
        background: linear-gradient(135deg, var(--surface) 0%, var(--border-light) 100%);
        border-radius: 20px;
        padding: 2rem;
        margin-top: 5rem;
        box-shadow: 0 8px 32px var(--shadow-light);
        border: 1px solid var(--border);
        position: relative;
        overflow: hidden;
    }

    .movie-header::before {
        content: '';
        position: absolute;
        background: linear-gradient(90deg, var(--primary), var(--primary-light));
    }

    .movie-content {
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 2rem;
        align-items: start;
    }

    .movie-poster {
        width: 200px;
        border-radius: 16px;
        box-shadow: 0 12px 24px var(--shadow);
        transition: transform 0.3s ease;
    }

    .movie-poster:hover {
        transform: scale(1.02);
    }

    .movie-info h1 {
        font-size: 2.5rem;
        font-weight: 800;
        margin: 0 0 1rem 0;
        color: var(--text-primary);
        line-height: 1.2;
    }

    .movie-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .meta-icon {
        width: 16px;
        height: 16px;
        opacity: 0.7;
    }

    .movie-genres {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .genre-tag {
        background: var(--primary);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .movie-description {
        color: var(--text-secondary);
        line-height: 1.6;
        font-size: 1.1rem;
    }

    /* Navigation Tabs */
    .nav-tabs {
        display: flex;
        background: var(--surface);
        border-radius: 12px;
        padding: 0.5rem;
        margin: 0.5rem 0;
        box-shadow: 0 4px 16px var(--shadow-light);
        border: 1px solid var(--border);
    }

    .nav-tab {
        flex: 1;
        padding: 1rem 1.5rem;
        border: none;
        background: transparent;
        color: var(--text-secondary);
        font-weight: 600;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .nav-tab:hover {
        background: var(--border-light);
        color: var(--primary);
    }

    .nav-tab.active {
        background: var(--primary);
        color: white;
        box-shadow: 0 4px 12px rgba(229, 28, 35, 0.3);
    }

    /* Content Panels */
    .tab-content {
        margin-top: 0.5rem;
    }

    .tab-panel {
        display: none;
        animation: fadeIn 0.5s ease;
    }

    .tab-panel.active {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Showtimes Panel */
    .showtimes-panel {
        background: var(--surface);
        border-radius: 16px;
        padding: 1rem 2rem;
        box-shadow: 0 8px 32px var(--shadow-light);
        border: 1px solid var(--border);
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .section-icon {
        width: 24px;
        height: 24px;
        color: var(--primary);
    }

    /* Date Selector */
    .date-selector {
        display: flex;
        gap: 1rem;
        overflow-x: auto;
        padding: 1rem 0;
        margin-bottom: 2rem;
        scrollbar-width: thin;
        scrollbar-color: var(--border) transparent;
    }

    .date-selector::-webkit-scrollbar {
        height: 6px;
    }

    .date-selector::-webkit-scrollbar-track {
        background: var(--border-light);
        border-radius: 3px;
    }

    .date-selector::-webkit-scrollbar-thumb {
        background: var(--border);
        border-radius: 3px;
    }

    .date-item {
        min-width: 100px;
        padding: 1rem;
        background: var(--surface);
        border: 2px solid var(--border);
        border-radius: 12px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        flex-shrink: 0;
    }

    .date-item:hover:not(.loading) {
        border-color: var(--primary);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px var(--shadow);
    }

    .date-item.active {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(229, 28, 35, 0.3);
    }

    .date-item.loading {
        opacity: 0.6;
        pointer-events: none;
    }

    .date-item.loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 20px;
        height: 20px;
        border: 2px solid var(--border);
        border-top: 2px solid var(--primary);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    .date-day {
        font-size: 0.875rem;
        font-weight: 500;
        opacity: 0.8;
        margin-bottom: 0.25rem;
    }

    .date-number {
        font-size: 1.5rem;
        font-weight: 700;
    }

    /* Cinema Selector */
    .cinema-selector {
        margin-bottom: 2rem;
    }

    .cinema-select {
        width: 100%;
        padding: 1rem;
        border: 2px solid var(--border);
        border-radius: 12px;
        background: var(--surface);
        color: var(--text-primary);
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 1rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 3rem;
    }

    .cinema-select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(229, 28, 35, 0.1);
    }

    /* Loading States */
    .loading-overlay {
        display: none;
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(4px);
        border-radius: 16px;
        z-index: 10;
    }

    .loading-overlay.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .loading-spinner {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
    }

    .spinner {
        width: 48px;
        height: 48px;
        border: 4px solid var(--border);
        border-top: 4px solid var(--primary);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .loading-text {
        color: var(--text-secondary);
        font-weight: 500;
    }

    /* Showtimes List */
    .showtimes-container {
        position: relative;
        min-height: 300px;
    }

    .cinema-group {
        margin-bottom: 2.5rem;
        animation: slideUp 0.5s ease;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .cinema-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--primary);
    }

    .cinema-name {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .cinema-info {
        font-size: 0.875rem;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .showtimes-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 1rem;
    }

    .showtime-card {
        background: var(--surface);
        border: 2px solid var(--border);
        border-radius: 12px;
        padding: 1rem;
        text-align: center;
        text-decoration: none;
        color: var(--text-primary);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .showtime-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .showtime-card:hover {
        border-color: var(--primary);
        transform: translateY(-2px);
        box-shadow: 0 8px 24px var(--shadow);
    }

    .showtime-card:hover::before {
        left: 100%;
    }

    .showtime-card:active {
        transform: scale(0.98);
    }

    .showtime-time {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 0.5rem;
    }

    .showtime-details {
        font-size: 0.75rem;
        color: var(--text-secondary);
        line-height: 1.4;
    }

    .seats-info {
        color: var(--success);
        font-weight: 600;
    }

    .price-info {
        color: var(--text-hint);
        margin-top: 0.25rem;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        color: var(--text-secondary);
    }

    .empty-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 1.5rem;
        opacity: 0.3;
    }

    .empty-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--text-primary);
    }

    .empty-message {
        font-size: 1rem;
        line-height: 1.5;
    }

    /* Error State */
    .error-state {
        background: #fff5f5;
        border: 2px solid #fed7d7;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        color: var(--error);
    }

    .error-icon {
        width: 48px;
        height: 48px;
        margin: 0 auto 1rem;
        color: var(--error);
    }

    .error-title {
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .retry-button {
        background: var(--error);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 1rem;
        transition: all 0.3s ease;
    }

    .retry-button:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .movie-content {
            grid-template-columns: 1fr;
            text-align: center;
        }

        .movie-poster {
            width: 160px;
            margin: 0 auto;
        }

        .movie-info h1 {
            font-size: 2rem;
        }

        .nav-tabs {
            overflow-x: auto;
            scrollbar-width: none;
        }

        .nav-tabs::-webkit-scrollbar {
            display: none;
        }

        .nav-tab {
            white-space: nowrap;
            flex-shrink: 0;
        }

        .showtimes-panel {
            padding: 1.5rem;
        }

        .showtimes-grid {
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 0.75rem;
        }

        .date-item {
            min-width: 80px;
            padding: 0.75rem;
        }
    }

    @media (max-width: 480px) {
        .movie-header {
            padding: 1.5rem;
            margin: 1rem 0;
        }

        .movie-info h1 {
            font-size: 1.75rem;
        }

        .showtimes-grid {
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        }
    }
</style>
@endpush

@section('content')
<div class="container mx-auto py-6 max-w-6xl">
    <!-- Movie Header -->
    <section class="movie-header">
        <div class="movie-content">
            <img src="{{ $movie->poster_url ? asset('storage/' . $movie->poster_url) : asset('assets/img/default/cinema.jpg') }}"
                 alt="{{ $movie->title }}"
                 class="movie-poster">

            <div class="movie-info">
                <h1>{{ $movie->title }}</h1>

                <div class="movie-meta">
                    <div class="meta-item">
                        <svg class="meta-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $movie->duration }} phút
                    </div>

                    <div class="meta-item">
                        <svg class="meta-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $movie->release_date ? date('d/m/Y', strtotime($movie->release_date)) : 'Đang cập nhật' }}
                    </div>

                    <div class="meta-item">
                        <svg class="meta-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        {{ $movie->rating }}/10
                    </div>

                    <div class="meta-item">
                        <svg class="meta-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $movie->age_rating }}
                    </div>
                </div>

                <div class="movie-genres">
                    @if($movie->genre)
                        @foreach(explode(',', $movie->genre) as $genre)
                            <span class="genre-tag">{{ trim($genre) }}</span>
                        @endforeach
                    @endif
                </div>

                <p class="movie-description">{{ $movie->description }}</p>
            </div>
        </div>
    </section>

    <!-- Navigation Tabs -->
    <div class="nav-tabs">
        <button class="nav-tab active" data-tab="showtimes">

            Lịch chiếu
        </button>
        <button class="nav-tab" data-tab="info">

            Thông tin
        </button>
        @if($movie->trailer_url)
        <button class="nav-tab" data-tab="trailer">

            Trailer
        </button>
        @endif
        <button class="nav-tab" data-tab="reviews">

            Đánh giá
        </button>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Showtimes Panel -->
        <div class="tab-panel active" id="panel-showtimes">
            <div class="showtimes-panel">
                <div class="section-title">
                    <svg class="section-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                    </svg>
                    Chọn ngày và rạp chiếu
                </div>

                <!-- Date Selector -->
                <div class="date-selector" id="date-selector">
                    @foreach($availableDates as $date)
                        <div class="date-item {{ $date['date'] == $selectedDate ? 'active' : '' }}"
                             data-date="{{ $date['date'] }}"
                             onclick="changeDate('{{ $date['date'] }}')">
                            <div class="date-day">{{ $date['day'] }}</div>
                            <div class="date-number">{{ $date['formatted'] }}</div>
                        </div>
                    @endforeach
                </div>

                <!-- Cinema Selector -->
                <div class="cinema-selector">
                    <select class="cinema-select" id="cinema-select" onchange="changeCinema(this.value)">
                        <option value="">Tất cả rạp chiếu</option>
                        @foreach($availableCinemas as $cinema)
                            <option value="{{ $cinema->cinema_id }}" {{ $selectedCinema == $cinema->cinema_id ? 'selected' : '' }}>
                                {{ $cinema->name }} - {{ $cinema->city }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Showtimes Container -->
                <div class="showtimes-container" id="showtimes-container">
                    <div class="loading-overlay" id="loading-overlay">
                        <div class="loading-spinner">
                            <div class="spinner"></div>
                            <div class="loading-text">Đang tải lịch chiếu...</div>
                        </div>
                    </div>

                    <div id="showtimes-content">
                        <!-- Content will be loaded here -->
                    </div>
                </div>

                <!-- Error State -->
                <div class="error-state" id="error-state" style="display: none;">
                    <svg class="error-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-2-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="error-title">Không thể tải lịch chiếu</div>
                    <div class="error-message">Vui lòng kiểm tra kết nối mạng và thử lại</div>
                    <button class="retry-button" onclick="reloadShowtimes()">Thử lại</button>
                </div>
            </div>
        </div>

        <!-- Movie Info Panel -->
        <div class="tab-panel" id="panel-info">
            <div class="showtimes-panel">
                <div class="section-title">
                    <svg class="section-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    Thông tin chi tiết
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold mb-4 text-gray-800">Nội dung phim</h3>
                        <p class="text-gray-600 leading-relaxed">{{ $movie->description }}</p>

                        <h3 class="text-lg font-semibold mb-4 mt-8 text-gray-800">Thông tin sản xuất</h3>
                        <div class="space-y-3">
                            <div><strong>Đạo diễn:</strong> {{ $movie->director }}</div>
                            <div><strong>Diễn viên:</strong> {{ $movie->cast }}</div>
                            <div><strong>Quốc gia:</strong> {{ $movie->country ?? 'Đang cập nhật' }}</div>
                            <div><strong>Ngôn ngữ:</strong> {{ $movie->language }}</div>
                            <div><strong>Thời lượng:</strong> {{ $movie->duration }} phút</div>
                            <div><strong>Phân loại:</strong> {{ $movie->age_rating }}</div>
                        </div>
                    </div>

                    <div>
                        <div class="bg-gradient-to-br from-red-50 to-red-100 p-6 rounded-2xl border border-red-200">
                            <h4 class="font-semibold text-red-800 mb-3">Đặt vé ngay</h4>
                            <p class="text-red-700 text-sm mb-4">Chọn suất chiếu phù hợp và đặt vé dễ dàng</p>
                            <button class="w-full bg-red-600 text-white py-3 px-6 rounded-xl font-semibold hover:bg-red-700 transition-colors" onclick="switchToTab('showtimes')">
                                Xem lịch chiếu
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trailer Panel -->
        @if($movie->trailer_url)
        <div class="tab-panel" id="panel-trailer">
            <div class="showtimes-panel">
                <div class="section-title">
                    <svg class="section-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                    </svg>
                    Trailer phim
                </div>

                <div class="aspect-video rounded-2xl overflow-hidden shadow-lg">
                    <iframe src="{{ $movie->trailer_url }}"
                            title="Trailer {{ $movie->title }}"
                            class="w-full h-full"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen>
                    </iframe>
                </div>
            </div>
        </div>
        @endif

        <!-- Reviews Panel -->
        <div class="tab-panel" id="panel-reviews">
            <div class="showtimes-panel">
                <div class="section-title">
                    <svg class="section-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    Đánh giá từ khán giả
                </div>

                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Chưa có đánh giá</h3>
                    <p class="text-gray-600">Hãy là người đầu tiên chia sẻ cảm nhận của bạn về bộ phim này!</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Global variables
    let currentMovieId = {{ $movie->movie_id }};
    let currentDate = '{{ $selectedDate }}';
    let currentCinema = '{{ $selectedCinema }}';
    let isLoading = false;

    // DOM Elements
    const showtimesContent = document.getElementById('showtimes-content');
    const loadingOverlay = document.getElementById('loading-overlay');
    const errorState = document.getElementById('error-state');
    const cinemaSelect = document.getElementById('cinema-select');

    // Tab functionality
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.nav-tab');
        const panels = document.querySelectorAll('.tab-panel');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const targetTab = tab.dataset.tab;
                switchToTab(targetTab);
            });
        });

        // Load initial showtimes
        loadShowtimes(currentDate, currentCinema);
    });

    function switchToTab(tabName) {
        const tabs = document.querySelectorAll('.nav-tab');
        const panels = document.querySelectorAll('.tab-panel');

        tabs.forEach(tab => {
            tab.classList.remove('active');
            if (tab.dataset.tab === tabName) {
                tab.classList.add('active');
            }
        });

        panels.forEach(panel => {
            panel.classList.remove('active');
            if (panel.id === `panel-${tabName}`) {
                panel.classList.add('active');
            }
        });
    }

    function showLoading() {
        if (isLoading) return;
        isLoading = true;
        loadingOverlay.classList.add('active');
        errorState.style.display = 'none';

        // Add loading state to date items
        document.querySelectorAll('.date-item:not(.active)').forEach(item => {
            item.classList.add('loading');
        });
    }

    function hideLoading() {
        isLoading = false;
        loadingOverlay.classList.remove('active');

        // Remove loading state from date items
        document.querySelectorAll('.date-item').forEach(item => {
            item.classList.remove('loading');
        });
    }

    function showError(message = 'Không thể tải lịch chiếu. Vui lòng thử lại.') {
        hideLoading();
        errorState.querySelector('.error-message').textContent = message;
        errorState.style.display = 'block';
        showtimesContent.innerHTML = '';
    }

    function updateURL(date, cinema = null) {
        const url = new URL(window.location.href);
        url.searchParams.set('date', date);

        if (cinema) {
            url.searchParams.set('cinema', cinema);
        } else {
            url.searchParams.delete('cinema');
        }

        window.history.pushState({}, '', url.toString());
    }

    function loadShowtimes(date = null, cinema = null) {
        if (isLoading) return;

        date = date || currentDate;
        cinema = cinema || currentCinema;

        showLoading();

        const params = new URLSearchParams();
        if (date) params.append('date', date);
        if (cinema) params.append('cinema', cinema);

        const url = `/movies/${currentMovieId}/showtimes-ajax?${params.toString()}`;

        fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            hideLoading();

            if (data.success) {
                showtimesContent.innerHTML = data.html;
                currentDate = date;
                currentCinema = cinema;
                updateURL(date, cinema);
                updateActiveDateItem(date);
                errorState.style.display = 'none';

                console.log(`Loaded ${data.count} showtimes for ${data.date}`);
            } else {
                showError(data.message || 'Không thể tải lịch chiếu.');
            }
        })
        .catch(error => {
            console.error('Error loading showtimes:', error);
            showError('Có lỗi xảy ra khi tải lịch chiếu. Vui lòng kiểm tra kết nối mạng và thử lại.');
        });
    }

    function updateActiveDateItem(date) {
        document.querySelectorAll('.date-item').forEach(item => {
            item.classList.remove('active');
            if (item.dataset.date === date) {
                item.classList.add('active');
            }
        });
    }

    function changeDate(date) {
        if (isLoading || date === currentDate) return;
        loadShowtimes(date, currentCinema);
    }

    function changeCinema(cinemaId) {
        if (isLoading) return;

        const cinema = cinemaId || null;
        currentCinema = cinema;
        cinemaSelect.value = cinemaId;

        loadShowtimes(currentDate, cinema);
    }

    function reloadShowtimes() {
        loadShowtimes(currentDate, currentCinema);
    }

    // Handle browser back/forward buttons
    window.addEventListener('popstate', function(event) {
        const urlParams = new URLSearchParams(window.location.search);
        const date = urlParams.get('date') || currentDate;
        const cinema = urlParams.get('cinema') || '';

        currentDate = date;
        currentCinema = cinema;

        updateActiveDateItem(date);
        cinemaSelect.value = cinema;
        loadShowtimes(date, cinema);
    });

    // Keyboard navigation
    document.addEventListener('keydown', function(event) {
        if (isLoading) return;

        const activePanel = document.querySelector('.tab-panel.active');
        if (!activePanel || activePanel.id !== 'panel-showtimes') return;

        const dateItems = Array.from(document.querySelectorAll('.date-item'));
        const currentIndex = dateItems.findIndex(item => item.classList.contains('active'));

        if (event.key === 'ArrowLeft' && currentIndex > 0) {
            event.preventDefault();
            dateItems[currentIndex - 1].click();
        } else if (event.key === 'ArrowRight' && currentIndex < dateItems.length - 1) {
            event.preventDefault();
            dateItems[currentIndex + 1].click();
        }
    });
</script>
@endpush
