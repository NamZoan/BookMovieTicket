@props(['testimonials'])

@if(!empty($testimonials))
    <div id="testimonialCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-inner">
            @foreach($testimonials as $index => $testimonial)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                    <div class="testimonial-card card border-0 shadow-sm">
                        <div class="card-body p-4 p-lg-5 d-flex flex-column gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ $testimonial['avatar'] }}" alt="{{ $testimonial['name'] }}" class="rounded-circle border border-2 border-danger" width="56" height="56">
                                <div>
                                    <div class="fw-semibold text-black">{{ $testimonial['name'] }}</div>
                                    <div class="small text-black text-uppercase">{{ $testimonial['role'] }}</div>
                                </div>
                            </div>
                            <p class="lead mb-0 text-black">
                                <i class="bi bi-quote fs-3 text-brand me-2"></i>{{ $testimonial['quote'] }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">{{ __('Trước') }}</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">{{ __('Sau') }}</span>
        </button>
    </div>
@endif