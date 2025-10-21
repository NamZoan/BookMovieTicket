@if(empty($grouped) || count($grouped) === 0)
    <div class="text-muted">No showtimes available.</div>
@else
    <div class="list-group">
        @foreach($grouped as $date => $movies)
            <div class="mb-3">
                <h6 class="fw-semibold">{{ $date }}</h6>
                @foreach($movies as $movieGroup)
                    @php $movie = $movieGroup['movie'] ?? null; @endphp
                    <div class="mb-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <strong>{{ $movie ? $movie->title : 'Movie' }}</strong>
                                <div class="text-muted small">{{ $movie ? ($movie->duration ? $movie->duration.' min' : '') : '' }}</div>
                            </div>
                            <div>
                                @foreach($movieGroup['showtimes'] as $s)
                                    <a href="{{ route('booking.seatSelection', $s->showtime_id) }}" class="btn btn-outline-light btn-sm me-1">{{ date('H:i', strtotime($s->show_time)) }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
@endif
