@extends('client.layouts.app')

@section('title', 'Rạp chiếu')

@push('styles')
<style>
/* Theme colors: primary red */
:root{--primary:#C62828;--primary-dark:#D32F2F;--muted:#6c757d}
.cinema-hero{background:linear-gradient(90deg,rgba(198,40,40,0.06),rgba(198,40,40,0.02));padding:28px;border-radius:12px}
.cinema-card{border-radius:8px;overflow:hidden}
.cinema-card .card-img-top{object-fit:cover}
.filter-sidebar{position:sticky;top:88px}
.skeleton {background:linear-gradient(90deg,#f0f0f0,#e8e8e8,#f0f0f0);background-size:200% 100%;animation:shimmer 1.2s linear infinite;border-radius:6px}
@keyframes shimmer{0%{background-position:-200% 0}100%{background-position:200% 0}}
.badge-active-filter{background:var(--primary);color:#fff}
.btn-focus-outline:focus{outline:3px solid rgba(198,40,40,0.25);outline-offset:2px}

/* Responsive tweaks */
@media(min-width:992px){
  .filter-sidebar{top:96px}
}
</style>
@endpush

@section('content')
<div class="container">
    <div class="row mb-4 align-items-center">
        <div class="col-lg-8">
            <div class="cinema-hero">
                <h1 class="mb-1" style="color:#111">Danh sách rạp</h1>
                <p class="mb-2 text-muted">Tìm rạp gần bạn, xem tiện nghi và lịch chiếu mới nhất.</p>

                <div class="d-flex gap-3">
                    <div>
                        <h4 class="mb-0">{{ $stats['cinemas'] ?? ($cinemas->total() ?? 0) }}</h4>
                        <small class="text-muted">Rạp</small>
                    </div>
                    <div>
                        <h4 class="mb-0">{{ $stats['cities'] ?? ($cities->count() ?? 0) }}</h4>
                        <small class="text-muted">Thành phố</small>
                    </div>
                    <div>
                        <h4 class="mb-0">{{ $stats['showtimes'] ?? '-' }}</h4>
                        <small class="text-muted">Lịch chiếu</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <button class="btn btn-outline-secondary d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#filtersOffcanvas" aria-controls="filtersOffcanvas">Bộ lọc</button>
            <a href="{{ route('cinemas.index') }}" class="btn btn-outline-dark ms-2">Đặt lại</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 d-none d-lg-block">
            <aside class="filter-sidebar">
                <form id="cinema-filter-form" role="search" aria-label="Bộ lọc rạp">
                    <div class="card mb-3">
                        <div class="card-body">
                            <label for="q" class="form-label">Tìm kiếm</label>
                            <div class="input-group">
                                <input id="q" name="q" value="{{ request('q') }}" class="form-control" placeholder="Tên, địa chỉ, thành phố" aria-label="Tìm kiếm rạp">
                                <button class="btn btn-outline-secondary" type="submit" aria-label="Tìm">Tìm</button>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body">
                            <label for="city" class="form-label">Thành phố</label>
                            <select id="city" name="city" class="form-select" aria-label="Chọn thành phố">
                                <option value="">Tất cả</option>
                                @foreach($cities as $c)
                                    <option value="{{ $c->name ?? $c }}" @if(request('city')==($c->name ?? $c)) selected @endif>{{ $c->name ?? $c }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body">
                            <label class="form-label">Tiện nghi</label>
                            <div class="d-flex flex-column">
                                @php $amenityList = $amenities ?? ['Parking','IMAX','3D','F&B','VIP']; @endphp
                                @foreach($amenityList as $am)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="amenities[]" value="{{ $am }}" id="am-{{ Illuminate\Support\Str::slug($am) }}" @if(is_array(request('amenities')) && in_array($am, request('amenities'))) checked @endif>
                                        <label class="form-check-label small" for="am-{{ Illuminate\Support\Str::slug($am) }}">{{ $am }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body">
                            <label class="form-label">Sắp xếp</label>
                            <select name="sort" class="form-select" aria-label="Sắp xếp kết quả">
                                <option value="">Mặc định</option>
                                <option value="name_asc" @if(request('sort')=='name_asc') selected @endif>Tên A–Z</option>
                                <option value="rating_desc" @if(request('sort')=='rating_desc') selected @endif>Đánh giá cao</option>
                                <option value="newest" @if(request('sort')=='newest') selected @endif>Mới nhất</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-danger">Áp dụng</button>
                        <button type="button" id="clear-filters" class="btn btn-outline-secondary">Xóa bộ lọc</button>
                    </div>
                </form>
            </aside>
        </div>

        <div class="col-lg-9">
            <div class="mb-3 d-flex align-items-center justify-content-between">
                <div>
                    <div id="active-filters" class="d-flex flex-wrap gap-2">
                        {{-- Active filter badges inserted here via JS --}}
                    </div>
                </div>
                <div>
                    <small class="text-muted">Hiển thị {{ $cinemas->firstItem() ?? 0 }}–{{ $cinemas->lastItem() ?? 0 }} / {{ $cinemas->total() ?? 0 }}</small>
                </div>
            </div>

            <div id="cinemas-grid">
                @if($cinemas->count())
                    <div class="row g-3">
                        @foreach($cinemas as $cinema)
                            <div class="col-12">
                                @include('client.cinemas._cinema_card', ['cinema'=>$cinema])
                            </div>
                        @endforeach
                    </div>

                    <div id="pagination" class="mt-4">
                        {{ $cinemas->links('vendor.pagination.bootstrap-5') }}
                    </div>
                @else
                    <div class="card py-5 text-center">
                        <div class="card-body">
                            <h5>Không tìm thấy kết quả</h5>
                            <p class="text-muted">Thử xóa bộ lọc hoặc tìm kiếm khác.</p>
                            <a href="{{ route('cinemas.index') }}" class="btn btn-danger">Xem tất cả rạp</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Mobile offcanvas filters -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="filtersOffcanvas" aria-labelledby="filtersOffcanvasLabel">
  <div class="offcanvas-header">
    <h5 id="filtersOffcanvasLabel">Bộ lọc</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Đóng"></button>
  </div>
  <div class="offcanvas-body">
    {{-- Duplicate minimal filter form for mobile (keeps ids unique where necessary) --}}
    <form id="cinema-filter-form-mobile">
        <div class="mb-3">
            <label class="form-label">Tìm kiếm</label>
            <input name="q" class="form-control" placeholder="Tên, địa chỉ, thành phố" value="{{ request('q') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Thành phố</label>
            <select name="city" class="form-select">
                <option value="">Tất cả</option>
                @foreach($cities as $c)
                    <option value="{{ $c->name ?? $c }}" @if(request('city')==($c->name ?? $c)) selected @endif>{{ $c->name ?? $c }}</option>
                @endforeach
            </select>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-danger">Áp dụng</button>
            <button type="button" id="clear-filters-mobile" class="btn btn-outline-secondary">Xóa</button>
        </div>
    </form>
  </div>
</div>

<!-- Showtimes modal -->
<div class="modal fade" id="showtimesModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Lịch chiếu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body" id="showtimesModalBody">
        <div class="text-center py-4"><div class="spinner-border text-danger" role="status" aria-hidden="true"></div></div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
(function($){
    var ajaxInFlight = false;

    function renderCinemas(html){
        $('#cinemas-grid').html(html);
    }

    function showLoading(){
        var sk = '<div class="row g-3">';
        for(var i=0;i<3;i++){
            sk += '<div class="col-12"><div class="card p-3"><div class="d-flex gap-3"><div style="width:140px;height:90px;" class="skeleton"></div><div class="flex-fill"><div class="skeleton mb-2" style="height:18px;width:50%"></div><div class="skeleton mb-2" style="height:14px;width:70%"></div><div class="skeleton" style="height:14px;width:30%"></div></div></div></div></div>';
        }
        sk += '</div>';
        $('#cinemas-grid').html(sk);
    }

    function syncActiveFilters(params){
        var container = $('#active-filters');
        container.empty();
        if(params.q) container.append('<span class="badge badge-active-filter py-2 px-2">Tìm: '+params.q+' <button class="btn-close btn-close-white ms-2 clear-filter" data-key="q" aria-label="Xóa tìm"></button></span>');
        if(params.city) container.append('<span class="badge badge-active-filter py-2 px-2">Thành phố: '+params.city+' <button class="btn-close btn-close-white ms-2 clear-filter" data-key="city" aria-label="Xóa thành phố"></button></span>');
        if(params.amenities){
            (params.amenities||[]).forEach(function(a){ container.append('<span class="badge badge-active-filter py-2 px-2">'+a+' <button class="btn-close btn-close-white ms-2 clear-filter" data-key="amenities" data-val="'+a+'" aria-label="Xóa"></button></span>'); });
        }
    }

    function buildQueryFromForm($form){
        var data = $form.serializeArray();
        var out = {};
        data.forEach(function(i){
            if(i.name.endsWith('[]')){
                var key = i.name.replace('[]',''); out[key]= out[key]||[]; out[key].push(i.value);
            } else {
                out[i.name]= i.value;
            }
        });
        return out;
    }

    function fetchCinemas(params, pushState){
        if(ajaxInFlight) return;
        ajaxInFlight = true;
        showLoading();
        $.ajax({
            url: '{{ route('cinemas.index') }}',
            data: params,
            dataType: 'json'
        }).done(function(res){
            if(res.success && res.html){
                renderCinemas(res.html);
                $('#pagination').html(res.pagination || '');
            } else if(res.html){
                renderCinemas(res.html);
            } else {
                $('#cinemas-grid').html('<div class="card p-4 text-center">Không có kết quả</div>');
            }
            syncActiveFilters(params || {});
            if(pushState) history.replaceState({}, '', '{{ route('cinemas.index') }}' + (Object.keys(params||{}).length ? ('?' + $.param(params)) : ''));
        }).fail(function(){
            $('#cinemas-grid').html('<div class="card p-4 text-center">Lỗi khi tải dữ liệu</div>');
        }).always(function(){ ajaxInFlight = false; });
    }

    $(function(){
        // Bind desktop form
        $('#cinema-filter-form').on('submit', function(e){
            e.preventDefault();
            var params = buildQueryFromForm($(this));
            fetchCinemas(params, true);
        });

        // Mobile form binding
        $('#cinema-filter-form-mobile').on('submit', function(e){
            e.preventDefault();
            var params = buildQueryFromForm($(this));
            var $off = $('#filtersOffcanvas');
            var bs = bootstrap.Offcanvas.getInstance($off[0]);
            if(bs) bs.hide();
            fetchCinemas(params, true);
        });

        $('#clear-filters, #clear-filters-mobile').on('click', function(){
            $('#cinema-filter-form')[0].reset();
            $('#cinema-filter-form-mobile')[0].reset();
            fetchCinemas({}, true);
        });

        // Delegate showtimes button
        $(document).on('click', '.btn-showtimes', function(){
            var id = $(this).data('cinema-id');
            $('#showtimesModalBody').html('<div class="text-center py-4"><div class="spinner-border text-danger" role="status"></div></div>');
            var modal = new bootstrap.Modal(document.getElementById('showtimesModal'));
            modal.show();
            $.ajax({ url: '{{ url('') }}'+ '/cinemas/' + id + '/showtimes', dataType: 'json' }).done(function(res){
                if(res.html) $('#showtimesModalBody').html(res.html);
                else if(res.data){
                    var out = '';
                    Object.keys(res.data).forEach(function(day){ out += '<h6>' + day + '</h6>'; res.data[day].forEach(function(g){ out += '<div class="mb-2"><strong>'+ (g.movie?g.movie.title:'') +'</strong> '; g.showtimes.forEach(function(s){ out += '<span class="badge bg-light text-dark me-1">'+s.show_time+'</span>'; }); out += '</div>'; }); });
                    $('#showtimesModalBody').html(out);
                } else $('#showtimesModalBody').html('<div class="text-muted">Không có lịch chiếu.</div>');
            }).fail(function(){ $('#showtimesModalBody').html('<div class="text-muted">Lỗi khi tải lịch chiếu.</div>'); });
        });

        // Active filter badge clear
        $(document).on('click', '.clear-filter', function(){
            var key = $(this).data('key');
            var val = $(this).data('val');
            if(key=='amenities'){
                $('input[name="amenities[]"][value="'+val+'"]').prop('checked', false);
            } else {
                $('input[name="'+key+'"], select[name="'+key+'"]').val('');
            }
            $('#cinema-filter-form').submit();
        });

        // Hijack pagination links
        $(document).on('click', '#pagination a', function(e){
            var href = $(this).attr('href');
            if(!href) return;
            e.preventDefault();
            var query = href.split('?')[1] || '';
            var params = {};
            if(query) {
                query.split('&').forEach(function(p){ var parts=p.split('='); params[decodeURIComponent(parts[0])] = decodeURIComponent(parts[1]||''); });
            }
            fetchCinemas(params, true);
        });
    });
})(jQuery);
</script>
@endpush

@endsection
