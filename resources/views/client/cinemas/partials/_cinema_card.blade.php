@props(['cinema'])

<div class="col-md-6 col-lg-4 mb-4">
    <div class="card h-100">
        @if($cinema->cover_image ?? false)
        <img src="{{ asset('storage/' . $cinema->cover_image) }}" class="card-img-top" alt="{{ $cinema->name }}">
        @else
        <img src="{{ asset('assets/img/default/cinema.jpg') }}" class="card-img-top" alt="{{ $cinema->name }}">
        @endif

        <div class="card-body d-flex flex-column">
            <h5 class="card-title">{{ $cinema->name }}</h5>
            <p class="muted mb-2"><i class="fa fa-map-marker me-2"></i>{{ $cinema->address }} - {{ $cinema->city }}</p>
            <p class="mb-2 text-muted small">{{ \Illuminate\Support\Str::limit($cinema->description ?? '', 120) }}</p>

            <div class="mt-auto d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('cinemas.show', $cinema->cinema_id) }}" class="btn btn-sm btn-primary">View Details</a>
                    <a href="{{ route('cinemas.showtimes', $cinema->cinema_id) }}" class="btn btn-sm btn-outline-secondary ms-2">Showtimes</a>
                </div>
                <div>
                    @if(!empty($cinema->amenities))
                        @foreach(array_slice($cinema->amenities,0,3) as $amen)
                            <span class="badge bg-light text-dark ms-1">{{ $amen }}</span>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
