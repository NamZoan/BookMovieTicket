@extends('client.layouts.app')

@section('title', __('Hệ thống rạp'))

@section('content')
@php
    $keyword = request('q');
    $selectedCity = request('city');
@endphp

<div class="container py-4">
    <div class="row g-4 align-items-center mb-2">
        <div class="col-lg-8">
            <div class="p-4 rounded-3 bg-light border">
                <p class="text-uppercase text-muted fw-semibold mb-1">{{ __('Hệ thống rạp BookMovieTicket') }}</p>
                <h1 class="h3 mb-3">{{ __('Chọn rạp — xem phim đúng lịch đang có trong hệ thống') }}</h1>
                <div class="d-flex flex-wrap gap-4">
                    <div>
                        <p class="fs-3 fw-bold mb-0">{{ $stats['cinemas'] ?? $cinemas->total() }}</p>
                        <small class="text-muted text-uppercase">{{ __('Rạp trên toàn quốc') }}</small>
                    </div>
                    <div>
                        <p class="fs-3 fw-bold mb-0">{{ $stats['cities'] ?? $cities->count() }}</p>
                        <small class="text-muted text-uppercase">{{ __('Thành phố') }}</small>
                    </div>
                    <div>
                        <p class="fs-3 fw-bold mb-0">{{ $stats['active_movies'] ?? 0 }}</p>
                        <small class="text-muted text-uppercase">{{ __('Phim đang chiếu') }}</small>
                    </div>
                    <div>
                        <p class="fs-3 fw-bold mb-0">{{ number_format($stats['active_showtimes'] ?? 0) }}</p>
                        <small class="text-muted text-uppercase">{{ __('Suất chiếu còn vé') }}</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h5 mb-3">{{ __('Tìm theo địa điểm') }}</h2>
                    <form method="GET" aria-label="{{ __('Lọc rạp') }}">
                        <div class="mb-3">
                            <label for="cinema-search" class="form-label small text-uppercase text-muted">{{ __('Từ khóa') }}</label>
                            <input id="cinema-search" name="q" type="search" class="form-control" value="{{ $keyword }}" placeholder="{{ __('Tên rạp, địa chỉ...') }}">
                        </div>
                        <div class="mb-3">
                            <label for="city" class="form-label small text-uppercase text-muted">{{ __('Thành phố') }}</label>
                            <select id="city" name="city" class="form-select">
                                <option value="">{{ __('Tất cả') }}</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city }}" @selected($selectedCity === $city)>{{ $city }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-brand flex-grow-1">{{ __('Áp dụng') }}</button>
                            <a href="{{ route('cinemas.index') }}" class="btn btn-outline-secondary flex-grow-1">{{ __('Đặt lại') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12">
            @if($cinemas->count())
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <p class="mb-0 text-muted">
                        {{ __('Hiển thị :from–:to trên tổng :total rạp', [
                            'from' => $cinemas->firstItem(),
                            'to' => $cinemas->lastItem(),
                            'total' => $cinemas->total()
                        ]) }}
                    </p>
                    @if($selectedCity)
                        <span class="badge bg-light text-dark px-3 py-2">
                            {{ __('Thành phố: :city', ['city' => $selectedCity]) }}
                        </span>
                    @endif
                </div>

                <div class="vstack gap-3">
                    @foreach($cinemas as $cinema)
                        @include('client.cinemas._cinema_card', ['cinema' => $cinema])
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $cinemas->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <img src="{{ asset('assets/img/default/cinema.jpg') }}" alt="" class="img-fluid mb-3" style="max-width:200px;">
                        <h3 class="h5">{{ __('Chưa tìm thấy rạp phù hợp') }}</h3>
                        <p class="text-muted">{{ __('Thử đổi từ khóa hoặc quay lại danh sách đầy đủ.') }}</p>
                        <a href="{{ route('cinemas.index') }}" class="btn btn-brand">{{ __('Xem tất cả rạp') }}</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
