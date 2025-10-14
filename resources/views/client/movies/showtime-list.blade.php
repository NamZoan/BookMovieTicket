@if($showtimes && $showtimes->count() > 0)
    @foreach($showtimes as $cinemaName => $cinemaShowtimes)
        <div class="cinema-group">
            <div class="cinema-header">
                <div>
                    <h3 class="cinema-name">{{ $cinemaName }}</h3>
                    @if($cinemaShowtimes->first() && $cinemaShowtimes->first()->screen && $cinemaShowtimes->first()->screen->cinema)
                        <div class="cinema-info">
                            📍
                            {{ $cinemaShowtimes->first()->screen->cinema->address ?? $cinemaShowtimes->first()->screen->cinema->city }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="showtimes-grid">
                @foreach($cinemaShowtimes as $showtime)
                    <a @auth href="{{ route('booking.seatSelection', ['showtime' => $showtime->showtime_id]) }}" @else
                    href="{{ route('auth.login') }}" @endauth class="showtime-card">
                        <div class="showtime-time">
                            {{ date('H:i', strtotime($showtime->show_time)) }}
                        </div>
                        <div class="showtime-details">
                            <div class="seats-info">
                                {{ $showtime->available_seats }} ghế trống
                            </div>
                            @if($showtime->screen)
                                <div class="screen-info">
                                    {{ $showtime->screen->screen_name }}
                                </div>
                            @endif
                            @if($showtime->base_price)
                                <div class="price-info">
                                    Từ {{ number_format($showtime->base_price, 0, ',', '.') }}đ
                                </div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endforeach
@else
    <div class="empty-state">
        <svg class="empty-icon" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                clip-rule="evenodd"></path>
        </svg>
        <div class="empty-title">Không có lịch chiếu</div>
        <div class="empty-message">
            Không tìm thấy lịch chiếu cho ngày đã chọn.<br>
            Vui lòng chọn ngày khác hoặc rạp chiếu khác.
        </div>
    </div>
@endif

<style>
    .cinema-group {
        margin-bottom: 2.5rem;
        animation: slideUp 0.5s ease;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .cinema-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--primary, #e51c23);
    }

    .cinema-name {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary, #212121);
        margin: 0;
    }

    .cinema-info {
        font-size: 0.875rem;
        color: var(--text-secondary, #757575);
        font-weight: 500;
        margin-top: 0.25rem;
    }

    .showtimes-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 1rem;
    }

    .showtime-card {
        background: var(--surface, #ffffff);
        border: 2px solid var(--border, #e0e0e0);
        border-radius: 12px;
        padding: 1rem;
        text-align: center;
        text-decoration: none;
        color: var(--text-primary, #212121);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        display: block;
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
        border-color: var(--primary, #e51c23);
        transform: translateY(-2px);
        box-shadow: 0 8px 24px var(--shadow, rgba(0, 0, 0, 0.1));
        text-decoration: none;
        color: var(--text-primary, #212121);
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
        color: var(--primary, #e51c23);
        margin-bottom: 0.5rem;
    }

    .showtime-details {
        font-size: 0.75rem;
        color: var(--text-secondary, #757575);
        line-height: 1.4;
    }

    .seats-info {
        color: var(--success, #4caf50);
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .screen-info {
        color: var(--text-hint, #9e9e9e);
        margin-bottom: 0.25rem;
    }

    .price-info {
        color: var(--text-hint, #9e9e9e);
        margin-top: 0.25rem;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        color: var(--text-secondary, #757575);
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
        color: var(--text-primary, #212121);
    }

    .empty-message {
        font-size: 1rem;
        line-height: 1.5;
    }

    @media (max-width: 768px) {
        .showtimes-grid {
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 0.75rem;
        }

        .cinema-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .cinema-name {
            font-size: 1.125rem;
        }

        .showtime-card {
            padding: 0.75rem;
        }

        .showtime-time {
            font-size: 1rem;
        }

        .showtime-details {
            font-size: 0.7rem;
        }
    }

    @media (max-width: 480px) {
        .showtimes-grid {
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        }

        .cinema-group {
            margin-bottom: 2rem;
        }

        .empty-state {
            padding: 2rem 1rem;
        }

        .empty-icon {
            width: 60px;
            height: 60px;
        }
    }
</style>
