@php
    use Carbon\Carbon;
@endphp

@if(empty($grouped) || count($grouped) === 0)
    <div class="text-muted">{{ __('Chưa có suất chiếu phù hợp.') }}</div>
@else
    @once
        @push('styles')
            <style>
                .showtime-date {
                    background: linear-gradient(120deg, #e51c23, #b5121c);
                    color: #fff;
                    padding: 10px 14px;
                    border-radius: 12px;
                }
                .showtime-card {
                    border: 1px solid #f2f2f2;
                    border-radius: 14px;
                    padding: 14px;
                    box-shadow: 0 10px 24px rgba(0,0,0,0.05);
                    background: #fff;
                }
                .slot-chip {
                    border: 1px solid #e51c23;
                    color: #b5121c;
                    background: #ffe5e7;
                    padding: 6px 10px;
                    border-radius: 10px;
                    display: inline-flex;
                    gap: 6px;
                    align-items: center;
                    font-weight: 600;
                }
            </style>
        @endpush
    @endonce

    <div class="vstack gap-3">
        @foreach($grouped as $date => $movies)
            <div class="showtime-card">
                <div class="showtime-date mb-3 fw-semibold">
                    {{ Carbon::parse($date)->translatedFormat('d/m/Y (l)') }}
                </div>
                @foreach($movies as $movieId => $movieGroup)
                    @php $movie = $movieGroup['movie'] ?? null; @endphp
                    <div class="d-flex flex-column flex-md-row gap-3 align-items-start align-items-md-center mb-3">
                        <div class="d-flex gap-3 flex-grow-1 align-items-center">
                            <div class="flex-shrink-0">
                                <img src="{{ $movie?->poster_url ? asset('storage/' . $movie->poster_url) : asset('assets/img/default/cinema.jpg') }}"
                                     alt="{{ $movie?->title }}" style="width: 70px; height: 100px; object-fit: cover; border-radius: 10px;">
                            </div>
                            <div>
                                <p class="mb-1 fw-semibold">{{ $movie?->title ?? __('Phim chưa xác định') }}</p>
                                <p class="text-muted small mb-0">
                                    {{ __('Thời lượng: :minutes phút', ['minutes' => $movie?->duration ?? '—']) }}
                                    @if(!empty($movie?->rating))
                                        • <i class="bi bi-star-fill text-warning"></i> {{ number_format($movie->rating, 1) }}/10
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex-grow-1 text-md-end">
                            @foreach($movieGroup['showtimes'] as $slot)
                                <a href="{{ route('booking.seatSelection', $slot['id']) }}"
                                   class="slot-chip text-decoration-none mb-1">
                                    <span>{{ $slot['time'] }}</span>
                                    <span>{{ $slot['screen'] }}</span>
                                    <span>{{ number_format($slot['price'] ?? 0, 0, ',', '.') }}đ</span>
                                    <span>{{ __('Còn :n', ['n' => $slot['available_seats'] ?? '']) }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
@endif
