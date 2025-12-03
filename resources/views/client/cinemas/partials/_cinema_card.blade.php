@props(['cinema'])

<div class="col-12 col-md-6 col-xl-4">
    @include('client.cinemas._cinema_card', ['cinema' => $cinema])
</div>
