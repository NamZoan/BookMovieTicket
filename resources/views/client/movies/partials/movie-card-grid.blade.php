@props([
    'movies',
    'emptyMessage' => null,
])

@php
    $collection = $movies instanceof \Illuminate\Support\Collection ? $movies : collect($movies);
@endphp

@if($collection->isEmpty())
    <div class="text-center py-5 text-muted">
        <i class="bi bi-camera-reels fs-1 mb-3 d-block"></i>
        <p class="fw-semibold mb-1">{{ $emptyMessage ?? __('No movies match the current filters.') }}</p>
        <p class="small text-muted">{{ __('Try adjusting the filters or searching with a different keyword.') }}</p>
    </div>
@else
    <div class="row g-4">
        @foreach($collection as $movie)
            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                @include('client.components.movie-card', ['movie' => $movie])
            </div>
        @endforeach
    </div>
@endif
