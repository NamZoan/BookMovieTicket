@extends('client.layouts.app')

@section('title', $cinema->name)

@push('styles')
<style>
.hero-cover{height:320px;background-size:cover;background-position:center;border-radius:12px}
.gallery-img{height:180px;object-fit:cover}
</style>
@endpush

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="hero-cover" style="background-image:url('{{ $cinema->cover_image ? asset('storage/'.$cinema->cover_image) : asset('assets/img/default/cinema.jpg') }}')"></div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-body">
                <h2>{{ $cinema->name }}</h2>
                <p class="muted">{{ $cinema->address }} - {{ $cinema->city }}</p>
                <p>{{ $cinema->description }}</p>

                <h5 class="mt-4">Amenities</h5>
                <div class="mb-3">
                    @if(!empty($cinema->amenities))
                        @foreach($cinema->amenities as $am)
                            <span class="badge bg-light text-dark me-1">{{ $am }}</span>
                        @endforeach
                    @endif
                </div>

                <h5 class="mt-4">Gallery</h5>
                <div id="cinemaGallery" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @php $first=true; @endphp
                        @if(!empty($cinema->gallery))
                            @foreach($cinema->gallery as $img)
                                <div class="carousel-item {{ $first ? 'active' : '' }}">
                                    <img src="{{ asset('storage/'.$img) }}" class="d-block w-100 gallery-img" alt="">
                                </div>
                                @php $first = false; @endphp
                            @endforeach
                        @else
                            <div class="carousel-item active"><img src="{{ asset('assets/img/default/cinema.jpg') }}" class="d-block w-100 gallery-img" alt=""></div>
                        @endif
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#cinemaGallery" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#cinemaGallery" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </button>
                </div>

            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5>Upcoming Showtimes</h5>
                <div id="showtimesArea">Loading showtimes...</div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-body">
                <h5>Contact</h5>
                <p><i class="fa fa-phone me-2"></i>{{ $cinema->phone ?? '-' }}</p>
                <p><i class="fa fa-envelope me-2"></i>{{ $cinema->email ?? '-' }}</p>
                <p><i class="fa fa-globe me-2"></i><a href="{{ $cinema->website_url ?? '#' }}">{{ $cinema->website_url ?? '-' }}</a></p>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5>Map</h5>
                @if($cinema->latitude && $cinema->longitude)
                    <iframe src="https://www.google.com/maps?q={{ $cinema->latitude }},{{ $cinema->longitude }}&output=embed" style="width:100%;height:220px;border:0;border-radius:8px"></iframe>
                @else
                    <img src="https://via.placeholder.com/400x220?text=Map+Unavailable" class="img-fluid rounded">
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function(){
    // Load showtimes via ajax
    $.ajax({
        url: '{{ route('cinemas.showtimes', $cinema->cinema_id) }}',
        dataType: 'json'
    }).done(function(res){
        if(!res.success) { $('#showtimesArea').html('<div class="text-muted">No showtimes found.</div>'); return; }
        // server returns grouped data; render partial via client-side insertion of server-rendered HTML if provided
        if(res.html) {
            $('#showtimesArea').html(res.html);
            return;
        }
        // fallback: build simple html
        var html = '';
        Object.keys(res.data).forEach(function(date){
            html += '<h6 class="mt-3">'+date+'</h6>';
            Object.values(res.data[date]).forEach(function(movieGroup){
                var movie = movieGroup.movie;
                html += '<div class="mb-2"><strong>'+ (movie ? movie.title : 'Movie') +'</strong>: ';
                movieGroup.showtimes.forEach(function(s){ html += '<span class="badge bg-light text-dark me-1">'+s.show_time+'</span>'; });
                html += '</div>';
            });
        });
        $('#showtimesArea').html(html);
    }).fail(function(){ $('#showtimesArea').html('<div class="text-muted">Failed to load showtimes.</div>'); });
});
</script>
@endpush

@endsection
