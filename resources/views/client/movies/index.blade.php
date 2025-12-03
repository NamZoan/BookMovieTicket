@extends('client.layouts.app')

@section('title', __('Danh sách phim'))

@php
    $activeFilters = $activeFilters ?? [];
    $currentSort = $activeFilters['sort'] ?? 'release_date';
    $currentDirection = $activeFilters['direction'] ?? 'desc';
    $sortValue = "{$currentSort}-{$currentDirection}";
    $meta = $meta ?? ['total' => 0, 'current_page' => 1, 'last_page' => 1, 'has_more' => false];
    $moviesItems =
        isset($movies) && method_exists($movies, 'items')
            ? collect($movies->items())
            : (isset($movies)
                ? collect($movies)
                : collect());
@endphp

@push('styles')
    <style>
        :root {
            --brand: #e51c23;
            --brand-dark: #b5121c;
            --brand-soft: #ffe5e7;
        }

        .movies-hero {
            background: linear-gradient(120deg, var(--brand), var(--brand-dark));
            border-radius: 20px;
            padding: 28px;
            color: #fff;
            box-shadow: 0 20px 40px rgba(229, 28, 35, 0.25);
        }

        .filter-sidebar {
            max-height: calc(100vh - 140px);
            overflow: auto;
            border-radius: 16px;
            border: 1px solid #f2f2f2;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.06);
        }

        .movie-card-hover {
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .movie-card-hover:hover {
            transform: translateY(-6px);
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, .35);
        }

        .skeleton {
            background: linear-gradient(90deg, #eee, #f5f5f5, #eee);
            background-size: 200% 100%;
            animation: shine 1.2s linear infinite;
        }

        @keyframes shine {
            from {
                background-position: 200% 0
            }

            to {
                background-position: -200% 0
            }
        }

        .filter-chips .badge {
            cursor: pointer
        }

        .btn-brand {
            background: linear-gradient(90deg, var(--brand), var(--brand-dark));
            color: #fff;
            border: none;
        }

        .btn-brand:hover {
            color: #fff;
            filter: brightness(1.05);
        }

        .btn-ghost {
            border: 1px solid var(--brand);
            color: var(--brand);
            background: #fff;
        }

        .btn-ghost:hover {
            background: var(--brand-soft);
            color: var(--brand-dark);
        }

        .stat-tile {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 12px 14px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
@endpush

@section('content')
    @include('client.components.trailer-modal')
    <section class="py-4">
        <div class="container">
            <div class="movies-hero mb-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">
                    <div>
                        <p class="text-uppercase fw-semibold mb-1" style="letter-spacing:0.08em;">{{ __('Khám phá phim') }}
                        </p>
                        <h1 class="h3 mb-2 fw-bold">{{ __('Tìm kiếm, lọc và chọn suất chiếu phù hợp') }}</h1>
                        <p class="mb-0" style="color:#ffe5e7;">
                            {{ __('Dữ liệu hiển thị trực tiếp từ cơ sở dữ liệu: trạng thái, thể loại, ngôn ngữ, đánh giá...') }}
                        </p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($statsTiles ?? [] as $tile)
                            <div class="stat-tile text-white">
                                <div class="small text-uppercase opacity-75">{{ $tile['label'] }}</div>
                                <div class="fs-4 fw-bold">{{ $tile['value'] }}{{ $tile['suffix'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="row">
                <aside class="col-lg-3 d-none d-lg-block">
                    <div class="card bg-white text-dark filter-sidebar sticky-top" style="top:90px;">
                        <div class="card-body">
                            <form id="movieFilterForm" data-action="{{ route('movies.index') }}" aria-label="Bộ lọc phim">
                                <input type="hidden" name="sort" value="{{ $currentSort }}">
                                <input type="hidden" name="direction" value="{{ $currentDirection }}">

                                <div class="mb-3">
                                    <label for="searchInput"
                                        class="form-label small text-uppercase">{{ __('Tìm kiếm') }}</label>
                                    <div class="input-group">
                                        <input id="searchInput" type="search" name="search"
                                            value="{{ $activeFilters['search'] ?? '' }}" class="form-control"
                                            placeholder="{{ __('Tên phim, diễn viên, đạo diễn...') }}"
                                            aria-label="Tìm kiếm phim">
                                        <button type="button" id="clearSearch" class="btn btn-outline-secondary"
                                            aria-label="Xóa tìm kiếm">&times;</button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small text-uppercase">{{ __('Trạng thái') }}</label>
                                    @foreach ($filters['statuses'] ?? [] as $option)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="status"
                                                id="status-{{ $loop->index }}" value="{{ $option['value'] ?? '' }}"
                                                {{ isset($activeFilters['status']) && $activeFilters['status'] === ($option['value'] ?? '') ? 'checked' : '' }}>
                                            <label class="form-check-label"
                                                for="status-{{ $loop->index }}">{{ $option['label'] }}</label>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small text-uppercase">{{ __('Thể loại') }}</label>
                                    <select name="genre" class="form-select form-select-sm" aria-label="Chọn thể loại">
                                        <option value="">{{ __('Tất cả thể loại') }}</option>
                                        @foreach ($filters['genres'] ?? [] as $option)
                                            <option value="{{ $option['value'] }}"
                                                {{ isset($activeFilters['genre']) && $activeFilters['genre'] === $option['value'] ? 'selected' : '' }}>
                                                {{ $option['label'] }}
                                                {{ $option['count'] ? '(' . $option['count'] . ')' : '' }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small text-uppercase">{{ __('Ngôn ngữ') }}</label>
                                    <select name="language" class="form-select form-select-sm">
                                        <option value="">{{ __('Tất cả') }}</option>
                                        @foreach ($filters['languages'] ?? [] as $option)
                                            <option value="{{ $option['value'] }}"
                                                {{ isset($activeFilters['language']) && $activeFilters['language'] === $option['value'] ? 'selected' : '' }}>
                                                {{ $option['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small text-uppercase">{{ __('Phân loại tuổi') }}</label>
                                    <select name="age_rating" class="form-select form-select-sm">
                                        <option value="">{{ __('Tất cả') }}</option>
                                        @foreach ($filters['age_ratings'] ?? [] as $option)
                                            <option value="{{ $option['value'] }}" @selected(($activeFilters['age_rating'] ?? null) === $option['value'])>
                                                {{ $option['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label
                                        class="form-label small text-uppercase">{{ __('Điểm đánh giá tối thiểu') }}</label>
                                    <select name="min_rating" class="form-select form-select-sm">
                                        <option value="">{{ __('Không lọc') }}</option>
                                        @foreach ($filters['ratings'] ?? [] as $option)
                                            <option value="{{ $option['value'] }}" @selected(($activeFilters['min_rating'] ?? null) == $option['value'])>
                                                {{ $option['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small text-uppercase">{{ __('Số phim mỗi trang') }}</label>
                                    <select name="per_page" class="form-select form-select-sm">
                                        @foreach ([12, 16, 20, 24] as $size)
                                            <option value="{{ $size }}" @selected(($activeFilters['per_page'] ?? 12) == $size)>
                                                {{ $size }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="d-flex gap-2 mt-3">
                                    <button type="submit" class="btn btn-brand w-100">{{ __('Áp dụng') }}</button>
                                    <button type="reset" id="resetFiltersBtn"
                                        class="btn btn-ghost w-100">{{ __('Xóa bộ lọc') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </aside>

                <div class="col-lg-9">
                    <div class="mb-3 d-flex flex-column flex-md-row justify-content-between align-items-start gap-2">
                        <div>
                            <div class="small text-muted">
                                {{ __('Hiển thị :count phim', ['count' => $meta['total'] ?? 0]) }} @if (!empty($activeFilters['search']))
                                    – "{{ $activeFilters['search'] }}"
                                @endif
                            </div>
                        </div>

                        <div class="d-flex gap-2 align-items-center">
                            <label for="sortSelect" class="small mb-0 me-2">{{ __('Sắp xếp') }}</label>
                            <select id="sortSelect" class="form-select form-select-sm" aria-label="Sắp xếp">
                                @foreach ($sortOptions as $value => $label)
                                    <option value="{{ $value }}" {{ $sortValue === $value ? 'selected' : '' }}>
                                        {{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="moviesGrid" class="row g-3">
                        @include('client.movies.partials.movie-card-grid', [
                            'movies' => $moviesItems,
                            'emptyMessage' => __('Không tìm thấy phim phù hợp với bộ lọc hiện tại.'),
                        ])
                    </div>

                    <div class="mt-4 d-flex justify-content-between align-items-center">
                        <div class="small text-muted" id="paginationInfo">
                            {{ __('Trang :current trên :last', ['current' => $meta['current_page'] ?? 1, 'last' => max($meta['last_page'] ?? 1, 1)]) }}
                        </div>
                        <div>
                            <button class="btn btn-outline-secondary btn-sm" id="loadMoreBtn"
                                data-next="{{ $meta['next_page_url'] ?? '' }}"
                                {{ !($meta['has_more'] ?? false) ? 'disabled' : '' }}>
                                <i class="bi bi-plus-circle me-1"></i> {{ __('Tải thêm') }}
                            </button>
                        </div>
                    </div>

                    <noscript>
                        <div class="mt-4">
                            {{ $movies->withQueryString()->links('pagination::bootstrap-4') }}
                        </div>
                    </noscript>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (function($) {
            'use strict';

            const $filterForm = $('#movieFilterForm');
            const $moviesGrid = $('#moviesGrid');
            const $loadMoreBtn = $('#loadMoreBtn');
            const $paginationInfo = $('#paginationInfo');
            const $filterChips = $('.filter-chips');
            const trailerModalEl = document.getElementById('trailerModal');
            const trailerFrame = document.getElementById('trailerModalFrame');
            const trailerModal = trailerModalEl ? new bootstrap.Modal(trailerModalEl) : null;

            const filterLabels = {
                status: @json(collect($filters['statuses'] ?? [])->pluck('label', 'value')),
                genre: @json(collect($filters['genres'] ?? [])->pluck('label', 'value')),
                language: @json(collect($filters['languages'] ?? [])->pluck('label', 'value')),
                age_rating: @json(collect($filters['age_ratings'] ?? [])->pluck('label', 'value')),
                min_rating: @json(collect($filters['ratings'] ?? [])->pluck('label', 'value')),
            };
            let isLoading = false;
            let searchTimer = null;

            function serializeFilters(extra = {}) {
                const form = $filterForm[0];
                const fd = new FormData(form);
                Object.entries(extra).forEach(([k, v]) => fd.set(k, v));
                const params = new URLSearchParams();
                fd.forEach((v, k) => {
                    if (v !== null && v !== '') params.append(k, v);
                });
                return params;
            }

            function formatChip(key, value) {
                const map = filterLabels[key] || {};
                const label = map[value] || value;
                return {
                    label: label,
                    value: value
                };
            }

            function renderChips(active) {
                $filterChips.empty();
                Object.keys(active || {}).forEach(function(k) {
                    const v = active[k];
                    if (!v) return;
                    const formatted = formatChip(k, v);
                    const chip = $(
                        `<span class="badge bg-secondary me-1" role="listitem"><span class="me-2">${formatted.label}</span><button type="button" class="btn-close btn-close-white btn-sm ms-1" aria-label="Xóa bộ lọc" data-filter="${k}"></button></span>`
                        );
                    $filterChips.append(chip);
                });
            }

            function showLoadingSkeleton(count = 9) {
                const cols = [];
                for (let i = 0; i < count; i++) {
                    cols.push(
                        `<div class="col-6 col-md-4 col-lg-3"><div class="card p-2 skeleton" style="height:320px;border-radius:12px;"></div></div>`
                        );
                }
                $moviesGrid.html(cols.join(''));
            }

            function refreshGrid(params, append = false) {
                if (isLoading) return;
                isLoading = true;
                if (!append) showLoadingSkeleton(8);

                $.ajax({
                    url: $filterForm.data('action'),
                    data: params.toString(),
                    dataType: 'json',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).done(function(res) {
                    if (!res.success) {
                        $moviesGrid.html(
                            '<div class="col-12"><div class="alert alert-danger">{{ __('Không thể tải danh sách phim. Vui lòng thử lại.') }}</div></div>'
                            );
                        return;
                    }

                    if (append) {
                        const $incoming = $(res.html);
                        const $items = $incoming.children();
                        $moviesGrid.append($items);
                    } else {
                        $moviesGrid.html(res.html);
                        const nextUrl = new URL(window.location.href);
                        nextUrl.search = params.toString();
                        window.history.replaceState({}, '', nextUrl);
                    }

                    const meta = res.meta || {};
                    const activeFilters = res.activeFilters || {};
                    $loadMoreBtn.prop('disabled', !meta.has_more);
                    $loadMoreBtn.attr('data-next', meta.next_page_url || '');
                    $paginationInfo.text(`{{ __('Trang :current trên :last') }}`.replace(':current', meta
                        .current_page || 1).replace(':last', Math.max(meta.last_page || 1, 1)));
                    renderChips(activeFilters);
                    bindUI($moviesGrid);
                }).fail(function() {
                    $moviesGrid.html(
                        '<div class="col-12"><div class="alert alert-danger">{{ __('Không thể tải danh sách phim. Vui lòng thử lại.') }}</div></div>'
                        );
                }).always(function() {
                    isLoading = false;
                });
            }

            function bindUI(scope) {
                scope.find('.js-open-trailer').off('click').on('click', function(e) {
                    e.preventDefault();
                    const url = $(this).data('trailer-url');
                    const title = $(this).data('movie-title') || '{{ __('Trailer phim') }}';
                    if (!url) return alert('{{ __('Trailer hiện chưa có sẵn cho phim này.') }}');
                    if (!trailerModal) return window.location.href = url;
                    trailerFrame.src = url;
                    $('#trailerModalLabel').text(title);
                    trailerModal.show();
                });

                scope.find('.js-fav-toggle').off('click').on('click', function() {
                    $(this).toggleClass('active');
                });
            }

            $(function() {
                bindUI($moviesGrid);

                // initial chips
                renderChips(@json($activeFilters));

                $('#searchInput').on('input', function() {
                    clearTimeout(searchTimer);
                    const q = $(this).val();
                    searchTimer = setTimeout(function() {
                        refreshGrid(serializeFilters({
                            search: q
                        }));
                    }, 400);
                });

                $('#clearSearch').on('click', function() {
                    $('#searchInput').val('');
                    refreshGrid(serializeFilters());
                });

                $filterForm.on('change', 'input, select', function() {
                    refreshGrid(serializeFilters());
                });

                $('#resetFiltersBtn').on('click', function(e) {
                    e.preventDefault();
                    $filterForm[0].reset();
                    refreshGrid(serializeFilters());
                });

                $('#sortSelect').on('change', function() {
                    const v = $(this).val().split('-');
                    $filterForm.find('input[name="sort"]').val(v[0]);
                    $filterForm.find('input[name="direction"]').val(v[1] || 'desc');
                    refreshGrid(serializeFilters());
                });

                $loadMoreBtn.on('click', function() {
                    const next = $(this).data('next');
                    if (!next || isLoading) return;
                    const params = new URL(next, window.location.origin).searchParams;
                    refreshGrid(params, true);
                });

                $filterChips.on('click', 'button', function() {
                    const key = $(this).data('filter');
                    if (!key) return;
                    $filterForm.find(`[name="${key}"]`).each(function() {
                        if ($(this).is(':checkbox') || $(this).is(':radio')) $(this).prop(
                            'checked', false);
                        else $(this).val('');
                    });
                    refreshGrid(serializeFilters());
                });
            });
        })(jQuery);
    </script>
@endpush
