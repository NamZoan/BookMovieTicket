<div class="card cinema-card h-100 shadow-sm" data-cinema-id="{{ $cinema->cinema_id ?? $cinema->id ?? '' }}">
    <div class="row g-0 h-100">
        <div class="col-12 col-md-4">
            <div class="card-img-top bg-light" style="height:160px;background-size:cover;background-position:center;border-top-left-radius:.375rem;border-bottom-left-radius:.375rem;background-image:url('{{ $cinema->cover_image ? asset('storage/'.$cinema->cover_image) : asset('assets/img/default/cinema.jpg') }}')" aria-hidden="true"></div>
        </div>
        <div class="col-12 col-md-8">
            <div class="card-body d-flex flex-column h-100">
                <h5 class="card-title mb-1">{{ $cinema->name }}</h5>
                <p class="card-text text-muted small mb-2">{{ $cinema->address }} · {{ $cinema->city }}</p>

                <div class="mb-2">
                    @if(!empty($cinema->amenities) && is_array($cinema->amenities))
                        @foreach(array_slice($cinema->amenities,0,4) as $am)
                            <span class="badge bg-light text-dark me-1 small">{{ $am }}</span>
                        @endforeach
                    @endif
                </div>

                <div class="mt-auto d-flex align-items-center justify-content-between">
                    <div>
                        @if(isset($cinema->rating))
                            <span class="badge bg-danger text-white me-2" title="Rating">{{ number_format($cinema->rating,1) }}</span>
                        @endif
                        <small class="text-muted">{{ $cinema->status ?? 'Đang hoạt động' }}</small>
                    </div>

                    <div class="btn-group">
                        <a href="{{ route('cinemas.show', $cinema->cinema_id ?? $cinema->id) }}" class="btn btn-sm btn-outline-dark">Xem chi tiết</a>
                        <button class="btn btn-sm btn-danger btn-showtimes" data-cinema-id="{{ $cinema->cinema_id ?? $cinema->id }}">Lịch chiếu</button>
                        <a href="mailto:{{ $cinema->email ?? '' }}" class="btn btn-sm btn-outline-secondary">Liên hệ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
