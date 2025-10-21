<form id="cinema-filter-form">
    <div class="card mb-3">
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Search</label>
                <input type="search" name="q" class="form-control" placeholder="Cinema name or address">
            </div>
            <div class="mb-3">
                <label class="form-label">City</label>
                <select name="city" class="form-select">
                    <option value="">All Cities</option>
                    @foreach($cities ?? [] as $c)
                        <option value="{{ $c }}">{{ $c }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Amenities</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="amenities[]" value="parking" id="amen-parking">
                    <label class="form-check-label" for="amen-parking">Parking</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="amenities[]" value="wifi" id="amen-wifi">
                    <label class="form-check-label" for="amen-wifi">Free WiFi</label>
                </div>
            </div>
            <div class="d-grid">
                <button class="btn btn-primary">Apply</button>
            </div>
        </div>
    </div>
</form>
