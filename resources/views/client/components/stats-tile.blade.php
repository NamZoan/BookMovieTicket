@props(['stat'])

<div class="stats-tile h-100" data-count="{{ $stat['value'] }}" data-suffix="{{ $stat['suffix'] ?? '' }}">
    <div class="d-flex flex-column gap-3">
        <div class="d-flex align-items-center gap-3">
            @if(!empty($stat['icon']))
                <span class="stats-tile__icon">
                    <i class="bi bi-{{ $stat['icon'] }}"></i>
                </span>
            @endif
            <span class="text-uppercase small fw-semibold">{{ $stat['label'] }}</span>
        </div>
        <div class="display-6 fw-bold stats-tile__value">{{ $stat['value'] }}<span class="fs-5 fw-semibold">{{ $stat['suffix'] ?? '' }}</span></div>
    </div>
</div>
