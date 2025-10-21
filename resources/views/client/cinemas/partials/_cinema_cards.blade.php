@if($cinemas->count())
<div class="row" id="cinema-grid">
    @foreach($cinemas as $cinema)
        @include('client.cinemas.partials._cinema_card', ['cinema' => $cinema])
    @endforeach
</div>
<div class="mt-3">
    {{ $cinemas->links('vendor.pagination.bootstrap-5') }}
</div>
@else
<div class="text-center py-5">
    <p class="mb-0">No cinemas found.</p>
</div>
@endif
